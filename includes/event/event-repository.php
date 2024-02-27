<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Throwable;
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

class Event_Repository {
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
}
