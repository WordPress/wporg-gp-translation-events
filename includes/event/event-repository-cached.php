<?php

namespace Wporg\TranslationEvents\Event;

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

	private function invalidate_cache(): void {
		wp_cache_delete( self::ACTIVE_EVENTS_KEY );
	}
}
