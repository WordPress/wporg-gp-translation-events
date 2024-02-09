<?php
/**
 * Routes: WPORG_GP_Translation_Events_Route class
 *
 * @package wporg-gp-translation-events
 */
class WPORG_GP_Translation_Events_Route extends GP_Route {
	private const USER_META_KEY_ATTENDING = 'translation-events-attending';

	/**
	 * WPORG_GP_Translation_Events_Route constructor.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->template_path = dirname( __FILE__ ) . '/../templates/';
	}

	/**
	 * Loads the 'events_list' template.
	 *
	 * @return void
	 */
	public function events_list() {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$this->tmpl( 'events-list', get_defined_vars() );
	}

	/**
	 * Loads the 'events_create' template.
	 *
	 * @return void
	 */
	public function events_create() {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$event_form_title   = 'Create Event';
		$event_form_name    = 'create_event';
		$css_show_url       = 'hide-event-url';
		$event_id           = null;
		$event_title        = '';
		$event_description  = '';
		$event_timezone     = '';
		$event_start        = '';
		$event_end          = '';
		$event_locale       = '';
		$event_project_name = '';

		$this->tmpl( 'events-form', get_defined_vars() );
	}

	/**
	 * Loads the 'events_edit' template.
	 *
	 * @param int $event_id The event ID.
	 * @return void
	 */
	public function events_edit( $event_id ) {
		if ( ! is_user_logged_in() ) {
			$this->die_with_404();
		}
		$event = get_post( $event_id );
		if ( ! $event || 'event' !== $event->post_type || ! ( current_user_can( 'edit_post', $event->ID ) || intval( $event->post_author ) === get_current_user_id() ) ) {
			$this->die_with_404();
		}

		include ABSPATH . 'wp-admin/includes/post.php';
		$event_form_title   = 'Edit Event';
		$event_form_name    = 'edit_event';
		$css_show_url       = '';
		$event_title        = $event->post_title;
		$event_description  = $event->post_content;
		$event_timezone     = get_post_meta( $event_id, '_event_timezone', true ) ?? '';
		$event_start        = self::convertToTimezone( get_post_meta( $event_id, '_event_start', true ), $event_timezone ) ?? '';
		$event_end          = self::convertToTimezone( get_post_meta( $event_id, '_event_end', true ), $event_timezone ) ?? '';
		$event_locale       = get_post_meta( $event_id, '_event_locale', true ) ?? '';
		$event_project_name = get_post_meta( $event_id, '_event_project_name', true ) ?? '';
		$event_status       = $event->post_status;
		list( $permalink, $post_name ) = get_sample_permalink( $event_id );
		$permalink = str_replace( '%pagename%', $post_name, $permalink );
		$this->tmpl( 'events-form', get_defined_vars() );
	}

	/**
	 * Loads the 'event' template.
	 *
	 * @param string $event_slug The event slug.
	 * @return void
	 */
	public function events_details( $event_slug ) {
		$user = wp_get_current_user();
		if ( ! $user ) {
			$this->die_with_404();
		}
		$event = get_page_by_path( $event_slug, OBJECT, 'event' );

		if ( ! $event ) {
			$this->die_with_404();
		}
		$event_id           = $event->ID;
		$event_title        = $event->post_title;
		$event_description  = $event->post_content;
		$event_start_date   = get_post_meta( $event->ID, '_event_start_date', true ) ?? '';
		$event_end_date     = get_post_meta( $event->ID, '_event_end_date', true ) ?? '';
		$event_locale       = get_post_meta( $event->ID, '_event_locale', true ) ?? '';
		$event_project_name = get_post_meta( $event->ID, '_event_project_name', true ) ?? '';

		$attending_event_ids = get_user_meta( $user->ID, self::USER_META_KEY_ATTENDING, true ) ?? [];
		$user_is_attending   = array_key_exists( $event_id, $attending_event_ids );

		$this->tmpl( 'event', get_defined_vars() );
	}

	/**
	 * Toggle whether the current user is attending an event.
	 * If the user is not currently marked as attending, they will be marked as attending.
	 * If the user is currently marked as attending, they will be marked as not attending.
	 *
	 * @param int $event_id
	 */
	public function events_attend( int $event_id ) {
		$user = wp_get_current_user();
		if ( ! $user ) {
			$this->die_with_error( 'Only logged-in users can attend events', 403 );
		}

		$event = get_post( $event_id );
		if ( ! $event ) {
			$this->die_with_404();
		}

		$event_ids = get_user_meta( $user->ID, self::USER_META_KEY_ATTENDING, true ) ?? [];
		if ( ! $event_ids ) {
			$event_ids = [];
		}

		if ( ! array_key_exists( $event_id, $event_ids ) ) {
			// Not yet attending, mark as attending.
			$event_ids[ $event_id ] = true;
		} else {
			// Currently attending, mark as not attending.
			unset( $event_ids[ $event_id ] );
		}

		update_user_meta( $user->ID, self::USER_META_KEY_ATTENDING, $event_ids );

		wp_safe_redirect( gp_url( "/events/$event->post_name" ) );
		exit;
	}

	/**
	 * Convert date time stored in UTC to a date time in a time zone.
	 *
	 * @param string $date_time The date time in UTC.
	 * @param string $time_zone The time zone.
	 *
	 * @return string The date time in the time zone.
	 */
	public static function convertToTimezone( $date_time, $time_zone ) {
		return ( new DateTime( $date_time, new DateTimeZone( 'UTC' ) ) )->setTimezone( new DateTimeZone( $time_zone ) )->format( 'Y-m-d H:i:s' );
	}
}
