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

class Event_Repository {
	/**
	 * @throws EventNotFound
	 */
	public static function get_event( int $id ): ?Event {
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

		// TODO: return an actual event.
		return null;
	}
}
