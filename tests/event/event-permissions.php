<?php

namespace event;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;
use Wporg\TranslationEvents\User\Event_Permissions;

class Event_Permissions_Test extends GP_UnitTestCase {
	private Event_Factory $event_factory;
	private Attendee_Repository $attendee_repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory       = new Event_Factory();
		$this->attendee_repository = new Attendee_Repository();
		$this->permissions         = new Event_Permissions();
	}
}
