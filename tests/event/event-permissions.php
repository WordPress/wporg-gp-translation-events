<?php

namespace event;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\Tests\Stats_Factory;
use Wporg\TranslationEvents\User\Cannot_Edit;
use Wporg\TranslationEvents\User\Event_Permissions;

class Event_Permissions_Test extends GP_UnitTestCase {
	private Event_Factory $event_factory;
	private Stats_Factory $stats_factory;
	private Event_Repository $event_repository;
	private Attendee_Repository $attendee_repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory       = new Event_Factory();
		$this->stats_factory       = new Stats_Factory();
		$this->attendee_repository = new Attendee_Repository();
		$this->event_repository    = new Event_Repository( $this->attendee_repository );
		$this->permissions         = new Event_Permissions();
	}

	public function test_author_can_edit() {
		$this->set_normal_user_as_current();
		$author_user_id = get_current_user_id();

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$this->expectNotToPerformAssertions();
		$this->permissions->assert_can_edit( $event, $author_user_id );
	}

	public function test_non_author_cannot_edit() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$this->expectException( Cannot_Edit::class );
		$this->expectExceptionMessage( 'You are not allowed to edit the event.' );
		$this->permissions->assert_can_edit( $event, $non_author_user_id );
	}

	public function test_can_edit_with_edit_capability() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$this->markTestSkipped( 'How can we test the edit capability?' );
		// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
		// $this->permissions->assert_can_edit( $event, $non_author_user_id );
	}

	public function test_host_can_edit() {
		$this->set_normal_user_as_current();
		$non_author_user_id = get_current_user_id();
		$this->set_normal_user_as_current(); // This user is the author.

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$attendee = new Attendee( $event_id, $non_author_user_id );
		$attendee->mark_as_host();
		$this->attendee_repository->insert_attendee( $attendee );

		$this->expectNotToPerformAssertions();
		$this->permissions->assert_can_edit( $event, $non_author_user_id );
	}

	public function test_cannot_edit_past_event() {
		$this->set_normal_user_as_current();
		$author_user_id = get_current_user_id();

		$event_id = $this->event_factory->create_inactive_past();
		$event    = $this->event_repository->get_event( $event_id );

		$this->expectException( Cannot_Edit::class );
		$this->expectExceptionMessage( 'Past events cannot be edited.' );
		$this->permissions->assert_can_edit( $event, $author_user_id );
	}

	public function test_cannot_edit_event_with_stats() {
		$this->set_normal_user_as_current();
		$author_user_id = get_current_user_id();

		$event_id = $this->event_factory->create_active();
		$event    = $this->event_repository->get_event( $event_id );

		$this->stats_factory->create( $event_id, $author_user_id, 1, 'create' );

		$this->expectException( Cannot_Edit::class );
		$this->expectExceptionMessage( 'Events with stats cannot be edited.' );
		$this->permissions->assert_can_edit( $event, $author_user_id );
	}
}
