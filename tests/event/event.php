<?php

namespace Wporg\Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use WP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\InvalidSlug;
use Wporg\TranslationEvents\Event\InvalidStartOrEnd;
use Wporg\TranslationEvents\Event\InvalidStatus;
use Wporg\TranslationEvents\Event\InvalidTitle;

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
			'foo-slug',
			'publish',
			'Foo title',
			'',
		);
	}

	public function test_validates_slug() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		$this->expectException( InvalidSlug::class );
		new Event(
			1,
			$now,
			$now->modify( '+1 hours' ),
			$timezone,
			'',
			'publish',
			'Foo title',
			'',
		);
	}

	public function test_validates_title() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		$this->expectException( InvalidTitle::class );
		new Event(
			1,
			$now,
			$now->modify( '+1 hours' ),
			$timezone,
			'foo-slug',
			'publish',
			'',
			'',
		);
	}

	public function test_validates_status() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );

		$this->expectException( InvalidStatus::class );
		new Event(
			1,
			$now,
			$now->modify( '+1 hours' ),
			$timezone,
			'foo-slug',
			'',
			'Foo title',
			'',
		);
	}
}
