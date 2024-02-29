<?php

namespace Wporg\TranslationEvents\Tests;

use DateTimeImmutable;
use DateTimeZone;
use WP_UnitTest_Factory_For_Post;
use WP_UnitTest_Generator_Sequence;
use Wporg\TranslationEvents\Route;

class Event_Factory extends WP_UnitTest_Factory_For_Post {
	public function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'post_status'  => 'publish',
			'post_title'   => new WP_UnitTest_Generator_Sequence( 'Event title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Event content %s' ),
			'post_excerpt' => new WP_UnitTest_Generator_Sequence( 'Event excerpt %s' ),
			'post_type'    => 'event',
		);
	}

	public function create_draft(): int {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		$event_id = $this->create_event(
			$now->modify( '-1 hours' ),
			$now->modify( '+1 hours' ),
			$timezone,
			array(),
		);

		$event              = get_post( $event_id );
		$event->post_status = 'draft';
		wp_update_post( $event );

		return $event_id;
	}

	public function create_active( array $attendee_ids = array(), $now = null ): int {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		if ( null === $now ) {
			$now = new DateTimeImmutable( 'now', $timezone );
		}

		return $this->create_event(
			$now->modify( '-1 second' ),
			$now->modify( '+1 hours' ),
			$timezone,
			$attendee_ids,
		);
	}

	public function create_inactive_past( array $attendee_ids = array() ): int {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		return $this->create_event(
			$now->modify( '-2 month' ),
			$now->modify( '-1 month' ),
			$timezone,
			$attendee_ids,
		);
	}

	public function create_inactive_future( array $attendee_ids = array() ): int {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		return $this->create_event(
			$now->modify( '+1 month' ),
			$now->modify( '+2 month' ),
			$timezone,
			$attendee_ids,
		);
	}

	public function create_event( DateTimeImmutable $start, DateTimeImmutable $end, DateTimeZone $timezone, array $attendee_ids ): int {
		$event_id = $this->create();
		$meta_key = Route::USER_META_KEY_ATTENDING;

		$user_id = get_current_user_id();
		if ( ! in_array( $user_id, $attendee_ids, true ) ) {
			// The current user will have been added as attending the event, but it was not specified as an attendee by
			// the caller of this function. So we remove the current user as attendee.
			$event_ids = get_user_meta( $user_id, $meta_key, true );
			unset( $event_ids[ $event_id ] );
			update_user_meta( $user_id, $meta_key, array() );
		}

		update_post_meta( $event_id, '_event_start', $start->format( 'Y-m-d H:i:s' ) );
		update_post_meta( $event_id, '_event_end', $end->format( 'Y-m-d H:i:s' ) );
		update_post_meta( $event_id, '_event_timezone', $timezone->getName() );

		foreach ( $attendee_ids as $user_id ) {
			$event_ids   = get_user_meta( $user_id, $meta_key, true ) ?: array();
			$event_ids[] = $event_id;
			update_user_meta( $user_id, $meta_key, $event_ids );
		}

		return $event_id;
	}
}
