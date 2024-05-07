<?php

namespace Wporg\TranslationEvents\Routes\Event;

use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Translation_Events;

/**
 * Displays the page that shows the list of trashed events.
 */
class List_Trashed_Route extends Route {
	private Event_Repository_Interface $event_repository;

	public function __construct() {
		parent::__construct();
		$this->event_repository = Translation_Events::get_event_repository();
	}

	public function handle(): void {
		if ( ! is_user_logged_in() ) {
			global $wp;
			wp_safe_redirect( wp_login_url( home_url( $wp->request ) ) );
			exit;
		}

		if ( ! current_user_can( 'manage_translation_events' ) ) {
			$this->die_with_error( 'You do not have permission to manage events.' );
		}

		// TODO.

		$this->tmpl( 'events-list-trashed', get_defined_vars() );
	}
}
