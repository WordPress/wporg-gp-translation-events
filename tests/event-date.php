<?php

namespace Wporg\Tests;

use GP_UnitTestCase;
use Wporg\TranslationEvents\Event\Event_Start_Date;

class Event_Date_Test extends GP_UnitTestCase {
	public function test_timezone() {
		$start = new Event_Start_Date( '2024-03-07 12:00:00', new \DateTimeZone( 'UTC' ) );
		$this->assertEquals( 'UTC', $start->timezone()->getName() );
		$this->assertEquals( 'UTC', $start->utc()->getTimezone()->getName() );
		$this->assertEquals( '2024-03-07 12:00:00', $start->utc()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-03-07 12:00:00', $start->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-03-07 12:00:00', strval( $start ) );
		$this->assertEquals( 'Thursday, March 7, 2024', $start->utc()->format( 'l, F j, Y' ) );
		$this->assertEquals( 'Thursday, March 7, 2024', $start->format( 'l, F j, Y' ) );

		$start = new Event_Start_Date( '2024-03-07 12:00:00', new \DateTimeZone( 'Asia/Taipei' ) );
		$this->assertEquals( 'Asia/Taipei', $start->timezone()->getName() );
		$this->assertEquals( 'UTC', $start->utc()->getTimezone()->getName() );
		$this->assertEquals( '2024-03-07 20:00:00', $start->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-03-07 12:00:00', $start->utc()->format( 'Y-m-d H:i:s' ) );
		$this->assertEquals( '2024-03-07 12:00:00', strval( $start ) );
	}
}
