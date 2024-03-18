<?php

namespace Wporg\TranslationEvents\Event;

use Exception;
use Throwable;
use WP_Error;

class EventNotFound extends Exception {
	public function __construct( Throwable $previous = null ) {
		parent::__construct( 'Event not found', 0, $previous );
	}
}

interface Event_Repository_Interface {
	/**
	 * @param Event $event Event to insert.
	 *
	 * @return int|WP_Error The id of the inserted event, or an error.
	 */
	public function insert_event( Event $event );

	/**
	 * @param Event $event Event to update.
	 *
	 * @return int|WP_Error The id of the updated event, or an error.
	 */
	public function update_event( Event $event );

	/**
	 * @throws EventNotFound
	 */
	public function get_event( int $id ): Event;

	/**
	 * @throws Exception
	 */
	public function get_current_events( int $page = -1, int $page_size = -1 ): Events_Query_Result;

	/**
	 * @throws Exception
	 */
	public function get_current_events_for_user( int $user_id, int $page = -1, int $page_size = -1 ): Events_Query_Result;

	/**
	 * @throws Exception
	 */
	public function get_past_events_for_user( int $user_id, int $page = -1, int $page_size = -1 ): Events_Query_Result;

	/**
	 * @throws Exception
	 */
	public function get_events_created_by_user( int $user_id, int $page = -1, int $page_size = -1 ): Events_Query_Result;
}

class Events_Query_Result {
	/**
	 * @var Event[]
	 */
	public array $events;

	public int $page_count;

	public function __construct( array $events, int $page_count ) {
		$this->events = $events;

		// The call to intval() is required because WP_Query::max_num_pages is sometimes a float, despite being type-hinted as int.
		$this->page_count = intval( $page_count );
	}
}
