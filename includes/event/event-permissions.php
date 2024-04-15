<?php

namespace Wporg\TranslationEvents\User;

use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Translation_Events;

class Event_Permissions {
	private Attendee_Repository $attendee_repository;

	public function __construct() {
		$this->attendee_repository = Translation_Events::get_attendee_repository();
	}

	public function can_edit( Event $event, int $user_id ): bool {
		if ( $event->end()->is_in_the_past() ) {
			return false;
		}

		if ( $event->author_id() === $user_id ) {
			return true;
		}

		if ( user_can( $user_id, 'edit_post', $event->id() ) ) {
			return true;
		}

		$attendee = $this->attendee_repository->get_attendee( $event->id(), $user_id );
		if ( ( $attendee instanceof Attendee ) && $attendee->is_host() ) {
			return true;
		}

		return false;
	}
}
