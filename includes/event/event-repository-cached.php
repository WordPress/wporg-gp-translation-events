<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

class Event_Repository_Cached extends Event_Repository {
	private const CACHE_DURATION    = 60 * 60 * 24; // 24 hours.
	private const ACTIVE_EVENTS_KEY = 'translation-events-active-events';

	public function create_event( Event $event ): void {
		parent::create_event( $event );
		$this->invalidate_cache();
	}

	public function update_event( Event $event ): void {
		parent::update_event( $event );
		$this->invalidate_cache();
	}

	public function get_current_events( int $current_page = -1, int $page_size = -1 ): Event_Query_Result {
		$cache_duration = self::CACHE_DURATION;
		$now            = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$boundary_start = $now;
		$boundary_end   = $now->modify( "+$cache_duration seconds" );

		$events = wp_cache_get( self::ACTIVE_EVENTS_KEY, '', false, $found );
		if ( ! $found ) {
			$events = $this->get_events_between( $boundary_start, $boundary_end );
			wp_cache_set( self::ACTIVE_EVENTS_KEY, $events, '', self::CACHE_DURATION );
		} elseif ( ! is_array( $events ) ) {
			throw new Exception( 'Cached events is not an array, something is wrong' );
		}

		// Filter out events that aren't actually active at $at.
		$events = array_values(
			array_filter(
				$events,
				function ( $event ) use ( $now ) {
					return $event->start() <= $now && $now <= $event->end();
				}
			)
		);

		// TODO: paginate results.

		return new Event_Query_Result( $events );
	}

	private function invalidate_cache(): void {
		wp_cache_delete( self::ACTIVE_EVENTS_KEY );
	}
}
