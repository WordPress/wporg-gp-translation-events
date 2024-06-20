<?php

namespace Wporg\TranslationEvents\Routes\Event;

use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Urls;

/**
 * Displays the RSS page.
 */
class Rss_Route extends Route {
	private Event_Repository_Interface $event_repository;

	/**
	 * Rss_Route constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->event_repository = Translation_Events::get_event_repository();
	}

	/**
	 * Handle the request.
	 *
	 * @return void
	 */
	public function handle(): void {
		$current_events_query = $this->event_repository->get_all_events( 1, 10 );
		$rss_feed             = $this->get_rss_20_header( $current_events_query->events );

		foreach ( $current_events_query->events as $event ) {
			$rss_feed .= $this->get_item( $event );
		}
		$rss_feed .= $this->get_rss_20_footer();

		header( 'Content-Type: application/xml; charset=UTF-8' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $rss_feed;
		exit();
	}

	/**
	 * Get the RSS 2.0 header.
	 *
	 * @return string
	 */
	private function get_rss_20_header( array $events ): string {
		$header  = '<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">';
		$header .= '	<channel>';
		$header .= '		<title>' . esc_html__( 'WordPress.org Global Translation Events', 'gp-translation-events' ) . '</title>';
		$header .= '		<link>' . home_url( gp_url( '/events' ) ) . '</link>';
		$header .= '		<description>' . esc_html__( 'WordPress.org Global Translation Events', 'gp-translation-events' ) . '</description>';
		$header .= '		<language>en-us</language>';
		$header .= '	 	<pubDate>' . $this->get_pub_date( $events ) . '</pubDate>';
		$header .= '		<lastBuildDate>' . $this->get_last_build_date( $events ) . '</lastBuildDate>';
		$header .= '		<docs>https://www.rssboard.org/rss-specification</docs>';
		$header .= '		<generator>Translation Events</generator>';
		$header .= '		<atom:link href="' . home_url( gp_url( '/events/rss' ) ) . '" rel="self" type="application/rss+xml" />';
		return $header;
	}

	/**
	 * Get the RSS 2.0 footer.
	 *
	 * @return string
	 */
	private function get_rss_20_footer(): string {
		$footer  = '	</channel>';
		$footer .= '</rss>';
		return $footer;
	}

	private function get_item( Event $event ) {
		$item  = '		<item>';
		$item .= '			<title>' . esc_html( $event->title() ) . '</title>';
		$item .= '			<link>' . home_url( gp_url( gp_url_join( 'events', $event->slug() ) ) ) . '</link>';
		$item .= '			<description>' . esc_html( $event->description() ) . '</description>';
		$item .= '          <enclosure url="' . Urls::event_image( $event->id() ) . '" type="image/png" />';
		$item .= '			<pubDate>' . $event->created_at()->format( DATE_RSS ) . '</pubDate>';
		$item .= '			<guid>' . home_url( gp_url( gp_url_join( 'events', $event->slug() ) ) ) . '</guid>';
		$item .= '		</item>';
		return $item;
	}

	/**
	 * Get the most recent event's pub date.
	 *
	 * @param Event[] $events Array of events to use for the pub date.
	 *
	 * @return string|null
	 */
	private function get_pub_date( array $events ): ?string {
		if ( empty( $events ) ) {
			return null;
		}

		$pub_date = $events[0]->created_at()->format( DATE_RSS );

		foreach ( $events as $event ) {
			if ( strtotime( $event->created_at()->format( DATE_RSS ) ) > strtotime( $pub_date ) ) {
				$pub_date = $event->created_at()->format( DATE_RSS );
			}
		}

		return gmdate( DATE_RSS, strtotime( $pub_date ) );
	}


	private function get_last_build_date( $events ) {
		if ( empty( $events ) ) {
			return null;
		}

		$last_build_date = $events[0]->created_at()->format( DATE_RSS );

		foreach ( $events as $event ) {
			if ( strtotime( $event->created_at()->format( DATE_RSS ) ) > strtotime( $last_build_date ) ) {
				$last_build_date = $event->created_at()->format( DATE_RSS );
			}
		}

		return gmdate( DATE_RSS, strtotime( $last_build_date ) );
	}
}
