<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Throwable;
use WP_Error;
use WP_Post;

class EventNotFound extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event not found', 0, $previous );
	}
}

class CreateEventFailed extends Exception {}
class UpdateEventFailed extends Exception {}

class Event_Repository {
	/**
	 * @throws CreateEventFailed
	 */
	public function create_event( Event $event ) {
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

	/**
	 * @throws UpdateEventFailed
	 */
	public function update_event( Event $event ) {
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

	/**
	 * @throws EventNotFound
	 */
	public function get_event( int $id ): Event {
		$post = $this->get_event_post( $id );

		try {
			$meta = $this->get_event_post_meta( $id );
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

	/**
	 * @return Event[]
	 * @throws Exception
	 */
	public function get_active_events( DateTimeImmutable $boundary_start = null, DateTimeImmutable $boundary_end = null ): array {
		$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		if ( null === $boundary_start ) {
			$boundary_start = $now;
		}
		if ( null === $boundary_end ) {
			$boundary_end = $boundary_start;
		}
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
			$meta     = $this->get_event_post_meta( $id );
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
	private function get_event_post_meta( int $event_id ): array {
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
