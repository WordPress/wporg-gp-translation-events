<?php

namespace Wporg\Tests\Event;

use DateTimeImmutable;
use DateTimeZone;
use WP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event_Repository;
use Wporg\TranslationEvents\Event\EventNotFound;
use Wporg\TranslationEvents\Tests\Event_Factory;

class Event_Repository_Test extends WP_UnitTestCase {
	private Event_Factory $event_factory;
	private Event_Repository $repository;

	public function setUp(): void {
		parent::setUp();
		$this->event_factory = new Event_Factory();
		$this->repository    = new Event_Repository();
	}

	public function test_get_event_throws_not_found_when_event_does_not_exist() {
		$this->expectException( EventNotFound::class );
		$this->repository->get_event( 42 );
	}

	public function test_get_event_throws_not_found_when_post_is_not_event() {
		$post_id = $this->factory()->post->create();
		$this->expectException( EventNotFound::class );
		$this->repository->get_event( $post_id );
	}

	public function test_get_event() {
		$timezone = new DateTimeZone( 'Europe/Lisbon' );
		$now      = new DateTimeImmutable( 'now', $timezone );
		$start    = $now->modify( '-1 hours' );
		$end      = $now->modify( '+1 hours' );

		$event_id = $this->event_factory->create_event( $start, $end, $timezone, array() );
		$event    = $this->repository->get_event( $event_id );

		$this->assertEquals( $start->getTimestamp(), $event->start()->getTimestamp() );
		$this->assertEquals( $end->getTimestamp(), $event->end()->getTimestamp() );
		$this->assertEquals( $timezone, $event->timezone() );
	}
}
