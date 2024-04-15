<?php

namespace Wporg\TranslationEvents\Routes\Event;

use Wporg\TranslationEvents\Event\Event_Repository_Interface;
use Wporg\TranslationEvents\Routes\Route;
use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\User\Cannot_Edit;
use Wporg\TranslationEvents\User\Event_Permissions;

/**
 * Displays the event edit page.
 */
class Edit_Route extends Route {
	private Event_Repository_Interface $event_repository;
	private Event_Permissions $event_permissions;

	public function __construct() {
		parent::__construct();
		$this->event_repository  = Translation_Events::get_event_repository();
		$this->event_permissions = new Event_Permissions();
	}

	public function handle( int $event_id ): void {
		global $wp;
		if ( ! is_user_logged_in() ) {
			wp_safe_redirect( wp_login_url( home_url( $wp->request ) ) );
			exit;
		}

		$event = $this->event_repository->get_event( $event_id );
		if ( ! $event ) {
			$this->die_with_404();
		}

		try {
			$this->event_permissions->assert_can_edit( $event, get_current_user_id() );
		} catch ( Cannot_Edit $e ) {
			$this->die_with_error( $e->getMessage(), $e->getCode() );
		}

		include ABSPATH . 'wp-admin/includes/post.php';
		$event_page_title              = 'Edit Event';
		$event_form_name               = 'edit_event';
		$css_show_url                  = '';
		$event_title                   = $event->title();
		$event_description             = $event->description();
		$event_status                  = $event->status();
		list( $permalink, $post_name ) = get_sample_permalink( $event->id() );
		$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
		$event_url                     = get_site_url() . gp_url( wp_make_link_relative( $permalink ) );
		$event_timezone                = $event->timezone();
		$event_start                   = $event->start();
		$event_end                     = $event->end();
		$create_delete_button          = false;
		$visibility_delete_button      = 'inline-flex';
		$create_delete_button          = $this->event_permissions->can_delete( $event, get_current_user_id() );

		$this->tmpl( 'events-form', get_defined_vars() );
	}
}
