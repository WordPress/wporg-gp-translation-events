<?php

namespace Wporg\Tests\Event;

use Wporg\Tests\Base_Test;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Event\Event_Form_Handler;



class Event_Form_Handler_Test extends Base_Test {
	private Event_Repository $event_repository;
	private Attendee_Repository $attendee_repository;
	private $event_form_handler;

	public function setUp(): void {
		parent::setUp();
		$this->attendee_repository = new Attendee_Repository();
		$this->event_repository    = new Event_Repository( $this->now, $this->attendee_repository );
		$this->event_form_handler  = new Event_Form_Handler( $this->now, $this->event_repository );
	}
}
