<?php

namespace Wporg\Tests\Event;

use DateTimeZone;
use WP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\InvalidStart;
use Wporg\TranslationEvents\Event\InvalidEnd;
use Wporg\TranslationEvents\Event\InvalidStatus;
use Wporg\TranslationEvents\Event\InvalidTitle;
use Wporg\TranslationEvents\Event_End_Date;
use Wporg\TranslationEvents\Event_Start_Date;

class Event_Test extends WP_UnitTestCase {
	public function test_validates_start_and_end() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidEnd::class );
		new Event(
			0,
			new Event_Start_Date( 'now' ),
			( new Event_End_Date( 'now' ) )->modify( '-1 hour' ),
			$timezone,
			'publish',
			'Foo title',
			'',
		);
	}

	public function test_validates_start_and_end_timezone() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidStart::class );
		new Event(
			0,
			new Event_Start_Date( 'now', $timezone ),
			( new Event_End_Date( 'now', $timezone ) )->modify( '+1 hour' ),
			$timezone,
			'publish',
			'Foo title',
			'',
		);
	}

	public function test_validates_title() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidTitle::class );
		new Event(
			0,
			new Event_Start_Date( 'now' ),
			( new Event_End_Date( 'now' ) )->modify( '+1 hour' ),
			$timezone,
			'publish',
			'',
			'',
		);
	}

	public function test_validates_status() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );

		$this->expectException( InvalidStatus::class );
		new Event(
			0,
			new Event_Start_Date( 'now' ),
			( new Event_End_Date( 'now' ) )->modify( '+1 hour' ),
			$timezone,
			'',
			'Foo title',
			'',
		);
	}
}
