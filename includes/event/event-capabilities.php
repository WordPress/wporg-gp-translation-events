<?php

namespace Wporg\TranslationEvents\Event;

use GP;
use WP_User;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Stats\Stats_Calculator;

class Event_Capabilities {
	private const CREATE = 'create_translation_event';
	private const VIEW   = 'view_translation_event';
	private const EDIT   = 'edit_translation_event';
	private const DELETE = 'delete_translation_event';

	/**
	 * All the capabilities that concern an Event.
	 */
	private const CAPS = array(
		self::CREATE,
		self::VIEW,
		self::EDIT,
		self::DELETE,
	);

	private Event_Repository_Interface $event_repository;
	private Attendee_Repository $attendee_repository;
	private Stats_Calculator $stats_calculator;

	public function __construct(
		Event_Repository_Interface $event_repository,
		Attendee_Repository $attendee_repository,
		Stats_Calculator $stats_calculator
	) {
		$this->event_repository    = $event_repository;
		$this->attendee_repository = $attendee_repository;
		$this->stats_calculator    = $stats_calculator;
	}

	/**
	 * This function is automatically called whenever user_can() is called for one the capabilities in self::CAPS.
	 *
	 * @param string  $cap  Requested capability.
	 * @param array   $args Arguments that accompany the requested capability check.
	 * @param WP_User $user User for which we're evaluating the capability.
	 * @return bool
	 */
	private function has_cap( string $cap, array $args, WP_User $user ): bool {
		switch ( $cap ) {
			case self::CREATE:
				return $this->has_create( $user );
			case self::VIEW:
			case self::EDIT:
			case self::DELETE:
				if ( ! isset( $args[2] ) || ! is_int( $args[2] ) ) {
					return false;
				}
				$event = $this->event_repository->get_event( $args[2] );
				if ( ! $event ) {
					return false;
				}

				if ( self::VIEW === $cap ) {
					return $this->has_view( $user, $event );
				}
				if ( self::EDIT === $cap ) {
					return $this->has_edit( $user, $event );
				}
				if ( self::DELETE === $cap ) {
					return $this->has_delete( $user, $event );
				}
				break;
		}

		return false;
	}

	/**
	 * Evaluate whether a user can create events.
	 *
	 * @param WP_User $user User for which we're evaluating the capability.
	 * @return bool
	 */
	private function has_create( WP_User $user ): bool {
		return $this->is_gp_admin( $user );
	}

	/**
	 * Evaluate whether a user can view a specific event.
	 *
	 * @param WP_User $user  User for which we're evaluating the capability.
	 * @param Event   $event Event for which we're evaluating the capability.
	 * @return bool
	 */
	private function has_view( WP_User $user, Event $event ): bool {
		if ( $this->is_gp_admin( $user ) ) {
			return true;
		}

		return 'publish' === $event->status();
	}

	/**
	 * Evaluate whether a user can edit a specific event.
	 *
	 * @param WP_User $user  User for which we're evaluating the capability.
	 * @param Event   $event Event for which we're evaluating the capability.
	 * @return bool
	 */
	private function has_edit( WP_User $user, Event $event ): bool {
		if ( $event->end()->is_in_the_past() ) {
			return false;
		}

		if ( $this->stats_calculator->event_has_stats( $event->id() ) ) {
			return false;
		}

		if ( $event->author_id() === $user->ID ) {
			return true;
		}

		if ( user_can( $user->ID, 'edit_post', $event->id() ) ) {
			return true;
		}

		$attendee = $this->attendee_repository->get_attendee( $event->id(), $user->ID );
		if ( ( $attendee instanceof Attendee ) && $attendee->is_host() ) {
			return true;
		}

		if ( $this->is_gp_admin( $user ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Evaluate whether a user can delete a specific event.
	 *
	 * @param WP_User $user  User for which we're evaluating the capability.
	 * @param Event   $event Event for which we're evaluating the capability.
	 * @return bool
	 */
	private function has_delete( WP_User $user, Event $event ): bool {
		// Must be able to edit in order to delete.
		if ( ! $this->has_edit( $user, $event ) ) {
			return false;
		}

		if ( user_can( $user->ID, 'delete_post', $event->id() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Evaluate whether a user is a GlotPress admin.
	 *
	 * @param WP_User $user User for which we're evaluating the capability.
	 * @return bool
	 */
	private function is_gp_admin( WP_User $user ): bool {
		return apply_filters( 'gp_translation_events_can_crud_event', GP::$permission->user_can( $user, 'admin' ) );
	}

	public function register_hooks(): void {
		add_action(
			'user_has_cap',
			function ( $allcaps, $caps, $args, $user ) {
				foreach ( $caps as $cap ) {
					if ( ! in_array( $cap, self::CAPS, true ) ) {
						continue;
					}
					if ( $this->has_cap( $cap, $args, $user ) ) {
						$allcaps[ $cap ] = true;
					}
				}
				return $allcaps;
			},
			10,
			4,
		);
	}
}