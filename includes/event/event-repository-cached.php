<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;

class Event_Repository_Cached implements Event_Repository_Interface {
	private Event_Repository $repository;

	public function __construct( Event_Repository $repository ) {
		$this->repository = $repository;
	}

	public function create_event( Event $event ): void {
		$this->repository->create_event( $event );
	}

	public function update_event( Event $event ): void {
		$this->repository->update_event( $event );
	}

	public function get_event( int $id ): Event {
		return $this->repository->get_event( $id );
	}

	public function get_active_events( DateTimeImmutable $boundary_start = null, DateTimeImmutable $boundary_end = null ): array {
		return $this->repository->get_active_events( $boundary_start, $boundary_end );
	}
}
