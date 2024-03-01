<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use WP_Error;
use WP_Post;
use WP_Query;
use Wporg\TranslationEvents\Route;

class Event_Repository implements Event_Repository_Interface {
	private const POST_TYPE               = 'event';
	private const USER_META_KEY_ATTENDING = Route::USER_META_KEY_ATTENDING;

	public function create_event( Event $event ): void {
		$event_id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_name'    => $event->slug(),
				'post_title'   => $event->title(),
				'post_content' => $event->description(),
				'post_status'  => $event->status(),
			)
		);

		if ( $event_id instanceof WP_Error ) {
			$error = $event_id;
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new CreateEventFailed( $error->get_error_message(), $error->get_error_code() );
		}

		$event->set_id( $event_id );
		$this->update_event_meta( $event );
	}

	public function update_event( Event $event ): void {
		$error = wp_update_post(
			array(
				'ID'           => $event->id(),
				'post_name'    => $event->slug(),
				'post_title'   => $event->title(),
				'post_content' => $event->description(),
				'post_status'  => $event->status(),
			)
		);

		if ( $error instanceof WP_Error ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new UpdateEventFailed( $error->get_error_message(), $error->get_error_code() );
		}

		$this->update_event_meta( $event );
	}

	public function get_event( int $id ): Event {
		$post = $this->get_event_post( $id );

		try {
			$meta = $this->get_event_meta( $id );
			return new Event(
				$post->ID,
				$meta['start'],
				$meta['end'],
				$meta['timezone'],
				$post->post_name,
				$post->post_status,
				$post->post_title,
				$post->post_content,
			);
		} catch ( Exception $e ) {
			// This should not be possible as it means data in the database is invalid.
			// So we consider an invalid event to be not found.
			throw new EventNotFound();
		}
	}

	public function get_current_events( int $current_page = -1, int $page_size = -1 ): Events_Query_Result {
		$this->assert_pagination_arguments( $current_page, $page_size );
		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );

		return $this->get_events_active_between(
			$now,
			$now,
			array(),
			$current_page,
			$page_size
		);
	}

	public function get_current_events_for_user( int $user_id, int $current_page = -1, int $page_size = -1 ): Events_Query_Result {
		$this->assert_pagination_arguments( $current_page, $page_size );

		$attending_array = get_user_meta( $user_id, self::USER_META_KEY_ATTENDING, true );
		if ( ! $attending_array ) {
			$attending_array = array();
		}

		// $attending_array is an associative array with the event_id as key.
		$event_ids_user_is_attending = array_keys( $attending_array );

		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		return $this->get_events_active_between(
			$now,
			$now,
			$event_ids_user_is_attending,
			$current_page,
			$page_size
		);
	}

	public function get_past_events_for_user( int $user_id, int $current_page = -1, int $page_size = -1 ): Events_Query_Result {
		$this->assert_pagination_arguments( $current_page, $page_size );

		$attending_array = get_user_meta( $user_id, self::USER_META_KEY_ATTENDING, true );
		if ( ! $attending_array ) {
			$attending_array = array();
		}

		// $attending_array is an associative array with the event_id as key.
		$event_ids_user_is_attending = array_keys( $attending_array );

		// We consider the start of time to be January 1st 2024,
		// which is guaranteed to be earlier than when this plugin was created.
		// It's not possible for there to be events before the plugin was created.
		$boundary_start = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$boundary_start = $boundary_start->setDate( 2024, 1, 1 )->setTime( 0, 0 );
		$boundary_end   = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );

		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		return $this->execute_events_query(
			array(
				'post_status'    => 'publish',
				'post__in'       => $event_ids_user_is_attending,
				'paged'          => $current_page,
				'posts_per_page' => $page_size,
				'meta_query'     => array(
					array(
						'key'     => '_event_end',
						'value'   => $boundary_start->format( 'Y-m-d H:i:s' ),
						'compare' => '>=',
						'type'    => 'DATETIME',
					),
					array(
						'key'     => '_event_end',
						'value'   => $boundary_end->format( 'Y-m-d H:i:s' ),
						'compare' => '<',
						'type'    => 'DATETIME',
					),
				),
				'meta_key'       => '_event_end',
				'meta_type'      => 'DATETIME',
				'orderby'        => array( 'meta_value', 'ID' ),
				'order'          => 'DESC',
			)
		);
		// phpcs:enable
	}

	public function get_events_created_by_user( int $user_id, int $current_page = -1, int $page_size = -1 ): Events_Query_Result {
		$this->assert_pagination_arguments( $current_page, $page_size );
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		return $this->execute_events_query(
			array(
				'post_status'    => array( 'publish', 'draft' ),
				'author'         => $user_id,
				'paged'          => $current_page,
				'posts_per_page' => $page_size,
				'meta_key'       => '_event_start',
				'orderby'        => array( 'meta_value', 'ID' ),
				'order'          => 'DESC',
			)
		);
		// phpcs:enable
	}

	/**
	 * @throws Exception
	 */
	protected function get_events_active_between(
		DateTimeImmutable $boundary_start,
		DateTimeImmutable $boundary_end,
		array $filter_by_ids = array(),
		int $current_page = -1,
		int $page_size = -1
	): Events_Query_Result {
		if ( $boundary_end < $boundary_start ) {
			throw new Exception( 'boundary end must not be before boundary start' );
		}

		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$query_args = array(
			'post_status'    => 'publish',
			'paged'          => $current_page,
			'posts_per_page' => $page_size,
			'meta_query'     => array(
				array(
					'key'     => '_event_start',
					'value'   => $boundary_end->format( 'Y-m-d H:i:s' ),
					'compare' => '<',
					'type'    => 'DATETIME',
				),
				array(
					'key'     => '_event_end',
					'value'   => $boundary_start->format( 'Y-m-d H:i:s' ),
					'compare' => '>',
					'type'    => 'DATETIME',
				),
			),
			'meta_key'       => '_event_start',
			'meta_type'      => 'DATETIME',
			'orderby'        => array( 'meta_value', 'ID' ),
		);
		// phpcs:enable

		if ( ! empty( $filter_by_ids ) ) {
			$query_args['post__in'] = $filter_by_ids;
		}

		return $this->execute_events_query( $query_args );
	}

	/**
	 * @throws Exception
	 */
	protected function assert_pagination_arguments( int $current_page, int $page_size ) {
		if ( -1 !== $current_page && $current_page <= 0 ) {
			throw new Exception( 'current page must be greater than 0' );
		}
		if ( -1 !== $page_size && $page_size <= 0 ) {
			throw new Exception( 'page size must be greater than 0' );
		}
		if ( $page_size > 0 && -1 === $current_page ) {
			throw new Exception( 'if page size is specified, current page must also be' );
		}
	}

	/**
	 * @throws InvalidStartOrEnd
	 * @throws InvalidTitle
	 * @throws InvalidStatus
	 * @throws Exception
	 */
	private function execute_events_query( array $args ): Events_Query_Result {
		$args = array_replace_recursive(
			$args,
			array(
				'post_type' => self::POST_TYPE,
			),
		);

		$query  = new WP_Query( $args );
		$posts  = $query->get_posts();
		$events = array();

		foreach ( $posts as $post ) {
			$meta     = $this->get_event_meta( $post->ID );
			$events[] = new Event(
				$post->ID,
				$meta['start'],
				$meta['end'],
				$meta['timezone'],
				$post->post_name,
				$post->post_status,
				$post->post_title,
				$post->post_content,
			);
		}

		return new Events_Query_Result( $events, $query->max_num_pages );
	}

	/**
	 * @throws EventNotFound
	 */
	private function get_event_post( int $event_id ): WP_Post {
		if ( 0 === $event_id ) {
			throw new EventNotFound();
		}
		$post = get_post( $event_id );
		if ( ! ( $post instanceof WP_Post ) ) {
			throw new EventNotFound();
		}
		if ( self::POST_TYPE !== $post->post_type ) {
			throw new EventNotFound();
		}

		return $post;
	}

	/**
	 * @throws Exception
	 */
	private function get_event_meta( int $event_id ): array {
		$meta = get_post_meta( $event_id );

		return array(
			'start'    => self::parse_utc_datetime( $meta['_event_start'][0] ),
			'end'      => self::parse_utc_datetime( $meta['_event_end'][0] ),
			'timezone' => new DateTimeZone( $meta['_event_timezone'][0] ),
		);
	}

	private function update_event_meta( Event $event ) {
		update_post_meta( $event->id(), '_event_start', self::serialize_datetime( $event->start() ) );
		update_post_meta( $event->id(), '_event_end', self::serialize_datetime( $event->end() ) );
		update_post_meta( $event->id(), '_event_timezone', $event->timezone()->getName() );
	}

	private static function parse_utc_datetime( string $datetime ): DateTimeImmutable {
		return DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $datetime, new DateTimeZone( 'UTC' ) );
	}

	private static function serialize_datetime( DateTimeImmutable $value ): string {
		$value->setTimezone( new DateTimeZone( 'UTC' ) );

		return $value->format( 'Y-m-d H:i:s' );
	}
}
