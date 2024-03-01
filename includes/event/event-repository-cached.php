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

	public function get_current_events( int $current_page = -1, int $page_size = -1 ): Events_Query_Result {
		$this->assert_pagination_arguments( $current_page, $page_size );

		$cache_duration = self::CACHE_DURATION;
		$now            = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		$boundary_start = $now;
		$boundary_end   = $now->modify( "+$cache_duration seconds" );

		$events = wp_cache_get( self::ACTIVE_EVENTS_KEY, '', false, $found );
		if ( ! $found ) {
			$events = $this->get_events_active_between( $boundary_start, $boundary_end )->events;
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

		if ( $current_page > 0 && $page_size > 0 ) {
			// Convert from 1-indexed to 0-indexed.
			--$current_page;
			$pages = array_chunk( $events, $page_size );
		} else {
			$current_page = 0;
			$pages        = array( $events );
		}

		return new Events_Query_Result( $pages[ $current_page ], count( $pages ) );
	}

	private function invalidate_cache(): void {
		wp_cache_delete( self::ACTIVE_EVENTS_KEY );
	}
}
