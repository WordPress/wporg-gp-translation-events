<?php

namespace Wporg\TranslationEvents\Tests;

use DateTimeImmutable;
use DateTimeZone;

class Event_Form_Handler_Factory {

	public function future_inactive_event_form_data( $form_name, DateTimeImmutable $now, $event_id = 0 ): array {
		$timezone              = 'Europe/Lisbon';
		$event_title           = 'Foo title';
		$event_description     = 'Foo description';
		$event_attendance_mode = 'hybrid';
		$_event_id             = isset( $event_id ) ? $event_id : 0;

		return array(
			'action'                => 'submit_event_ajax',
			'form_name'             => $form_name,
			'event_id'              => $_event_id,
			'event_form_action'     => 'publish',
			'event_title'           => $event_title,
			'event_description'     => $event_description,
			'event_start'           => $now->modify( '+1 month' ),
			'event_end'             => $now->modify( '+2 month' ),
			'event_timezone'        => $timezone,
			'event_attendance_mode' => $event_attendance_mode,
		);
	}
}
