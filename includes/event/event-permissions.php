<?php

namespace Wporg\TranslationEvents\User;

use Exception;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Stats\Stats_Calculator;
use Wporg\TranslationEvents\Translation_Events;

class Cannot_Edit extends Exception {
	public function __construct( string $message ) {
		parent::__construct( $message, 403 );
	}
}

class Event_Permissions {
	private Attendee_Repository $attendee_repository;
	private Stats_Calculator $stats_calculator;

	public function __construct() {
		$this->attendee_repository = Translation_Events::get_attendee_repository();
		$this->stats_calculator    = new Stats_Calculator();
	}

	/**
	 * @throws Cannot_Edit
	 */
	public function assert_can_edit( Event $event, int $user_id ): void {
		if ( $event->end()->is_in_the_past() ) {
			throw new Cannot_Edit( esc_html__( 'Past events cannot be edited.', 'gp-translation-events' ) );
		}

		if ( $this->stats_calculator->event_has_stats( $event->id() ) ) {
			throw new Cannot_Edit( esc_html__( 'Events with stats cannot be edited.', 'gp-translation-events' ) );
		}

		if ( $event->author_id() === $user_id ) {
			return;
		}

		if ( user_can( $user_id, 'edit_post', $event->id() ) ) {
			return;
		}

		$attendee = $this->attendee_repository->get_attendee( $event->id(), $user_id );
		if ( ( $attendee instanceof Attendee ) && $attendee->is_host() ) {
			return;
		}

		throw new Cannot_Edit( esc_html__( 'You are not allowed to edit the event.', 'gp-translation-events' ) );
	}
}
