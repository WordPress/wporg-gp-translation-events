<?php

namespace Wporg\Tests\Event;

use WP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Event_Repository_Test extends WP_UnitTestCase {
	private Event_Factory $event_factory;
	private Event_Repository $repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->repository    = new Event_Repository();
	}
}
