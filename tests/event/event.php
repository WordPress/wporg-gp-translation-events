<?php

namespace Wporg\Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use WP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\InvalidStartOrEnd;

class Event_Test extends WP_UnitTestCase {
	public function test_validates_start_and_end() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		$this->expectException( InvalidStartOrEnd::class );
		new Event(
			1,
			$now,
			$now->modify( '-1 hours' ),
			$timezone,
			'',
			'',
			'',
			'',
		);
	}
}
