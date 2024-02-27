<?php

namespace Wporg\TranslationEvents\Event;

use DateTimeImmutable;
use Exception;
use Throwable;

class EventNotFound extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event not found', 0, $previous );
	}
}

class CreateEventFailed extends Exception {}
class UpdateEventFailed extends Exception {}

interface Event_Repository_Interface {
	/**
	 * @throws CreateEventFailed
	 */
	public function create_event( Event $event ): void;

	/**
	 * @throws UpdateEventFailed
	 */
	public function update_event( Event $event ): void;

	/**
	 * @throws EventNotFound
	 */
	public function get_event( int $id ): Event;

	/**
	 * @return Event[]
	 * @throws Exception
	 */
	public function get_active_events( DateTimeImmutable $boundary_start = null, DateTimeImmutable $boundary_end = null ): array;
}
