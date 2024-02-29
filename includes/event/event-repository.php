<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use WP_Error;
use WP_Post;

class Event_Repository implements Event_Repository_Interface {
	public function create_event( Event $event ): void {
		$event_id = wp_insert_post(
			array(
				'post_type'    => 'event',
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

	public function get_active_events(): array {
		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		return $this->get_events_between( $now, $now );
	}

	/**
	 * @return Event[]
	 * @throws Exception
	 */
	protected function get_events_between( DateTimeImmutable $boundary_start, DateTimeImmutable $boundary_end ): array {
		if ( $boundary_end < $boundary_start ) {
			throw new Exception( 'boundary end must be after boundary start' );
		}

		$ids = get_posts(
			array(
				'post_type'      => 'event',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
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
			),
		);

		$events = array();
		foreach ( $ids as $id ) {
			$post     = $this->get_event_post( $id );
			$meta     = $this->get_event_meta( $id );
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

		usort(
			$events,
			function ( Event $a, Event $b ) {
				return $a->id() <=> $b->id();
			}
		);

		return $events;
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
		if ( 'event' !== $post->post_type ) {
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
