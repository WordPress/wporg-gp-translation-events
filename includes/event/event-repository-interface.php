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
	 * @throws Exception
	 */
	public function get_current_events(): Event_Query_Result;

	/**
	 * @throws Exception
	 */
	public function get_current_events_for_user( int $user_id ): Event_Query_Result;

	/**
	 * @throws Exception
	 */
	public function get_past_events_for_user( int $user_id ): Event_Query_Result;

	/**
	 * @throws Exception
	 */
	public function get_events_created_by_user( int $user_id ): Event_Query_Result;
}

class Event_Query_Result {
	/**
	 * @var Event[]
	 */
	public array $events;

	public function __construct( array $events ) {
		$this->events = $events;
	}
}
