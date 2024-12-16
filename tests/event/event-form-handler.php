<?php

namespace Wporg\Tests\Event;

use Wporg\Tests\Base_Test;
use Wporg\TranslationEvents\Attendee\Attendee_Repository;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Event\Event_Form_Handler;
use Wporg\TranslationEvents\Tests\Event_Form_Handler_Factory;

class Event_Form_Handler_Test extends Base_Test {
	private Event_Repository $event_repository;
	private Attendee_Repository $attendee_repository;
	private Event_Form_Handler $event_form_handler;
	private Event_Form_Handler_Factory $event_form_handler_factory;

	public function setUp(): void {
		parent::setUp();
		$this->attendee_repository        = new Attendee_Repository();
		$this->event_repository           = new Event_Repository( $this->now, $this->attendee_repository );
		$this->event_form_handler         = new Event_Form_Handler( $this->now, $this->event_repository );
		$this->event_form_handler_factory = new Event_Form_Handler_Factory();
		$this->set_normal_user_as_current();
	}

	/**
	 * Test that the user must be logged in to create an event.
	 *
	 * @return void
	 */
	public function test_user_is_not_logged_in() {
		wp_set_current_user( 0 );
		$form_data = $this->event_form_handler_factory->future_inactive_event_form_data( 'create_event', $this->now );
		$response  = $this->event_form_handler->process_form( $form_data );

		$this->assertEquals( 'The user must be logged in.', $response->get_error_message() );
	}

	public function test_form_name_is_invalid() {
		$form_data = $this->event_form_handler_factory->future_inactive_event_form_data( 'invalid_form_name', $this->now );
		$response  = $this->event_form_handler->process_form( $form_data );

		$this->assertEquals( 'Invalid form name.', $response->get_error_message() );
	}

	public function test_invalid_permissions() {
		$form_data_1 = $this->event_form_handler_factory->future_inactive_event_form_data( 'create_event', $this->now );
		$response_1  = $this->event_form_handler->process_form( $form_data_1 );
		$this->assertEquals( 'You do not have permissions to create events.', $response_1->get_error_message() );

		$form_data_2 = $this->event_form_handler_factory->future_inactive_event_form_data( 'edit_event', $this->now );
		$response_2  = $this->event_form_handler->process_form( $form_data_2 );
		$this->assertEquals( 'You do not have permissions to edit this event.', $response_2->get_error_message() );

		$form_data_3 = $this->event_form_handler_factory->future_inactive_event_form_data( 'trash_event', $this->now );
		$response_3  = $this->event_form_handler->process_form( $form_data_3 );
		$this->assertEquals( 'You do not have permissions to delete this event.', $response_3->get_error_message() );
	}
}
