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

class InvalidTimezone extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Invalid timezone', 0, $previous );
	}
}

class CreateEventFailed extends Exception {}

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

		update_post_meta( $event_id, '_event_start', self::serialize_datetime( $event->start() ) );
		update_post_meta( $event_id, '_event_end', self::serialize_datetime( $event->end() ) );
		update_post_meta( $event_id, '_event_timezone', $event->timezone()->getName() );

		$event->set_id( $event_id );
	}

	/**
	 * @throws EventNotFound
	 * @throws InvalidTimezone
	 */
	public function get_event( int $id ): Event {
		if ( 0 === $id ) {
			throw new EventNotFound();
		}

		$post = get_post( $id );
		if ( ! ( $post instanceof WP_Post ) ) {
			throw new EventNotFound();
		}

		if ( 'event' !== $post->post_type ) {
			throw new EventNotFound();
		}

		$meta  = get_post_meta( $post->ID );
		$start = self::parse_utc_datetime( $meta['_event_start'][0] );
		$end   = self::parse_utc_datetime( $meta['_event_end'][0] );

		try {
			$timezone = new DateTimeZone( $meta['_event_timezone'][0] );
		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidTimezone( $e );
		}

		try {
			return new Event(
				$post->ID,
				$start,
				$end,
				$timezone,
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

	private static function parse_utc_datetime( string $datetime ): DateTimeImmutable {
		return DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $datetime, new DateTimeZone( 'UTC' ) );
	}

	private static function serialize_datetime( DateTimeImmutable $value ): string {
		$value->setTimezone( new DateTimeZone( 'UTC' ) );
		return $value->format( 'Y-m-d H:i:s' );
	}
}
