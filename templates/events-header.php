<?php

namespace Wporg\TranslationEvents;

use GP;
use Wporg\TranslationEvents\Event\Event;

/** @var Event  $event */
/** @var string $event_page_title */
/** @var bool   $is_editable_event */
?>

<div class="event-list-top-bar">
<h2 class="event-page-title">
	<?php echo esc_html( $event_page_title ); ?>
	<?php if ( isset( $event ) && 'draft' === $event->status() ) : ?>
				<span class="event-label-draft"><?php echo esc_html( $event->status() ); ?></span>
			<?php endif; ?>
</h2>
	<ul class="event-list-nav">
		<?php if ( is_user_logged_in() ) : ?>
			<li><a href="<?php echo esc_url( gp_url( '/events/my-events/' ) ); ?>">My Events</a></li>
			<?php
			/**
			 * Filter the ability to create, edit, or delete an event.
			 *
			 * @param bool $can_crud_event Whether the user can create, edit, or delete an event.
			 */
			$can_crud_event = apply_filters( 'gp_translation_events_can_crud_event', GP::$permission->current_user_can( 'admin' ) );
			if ( $can_crud_event ) :
				?>
				<li><a class="button is-primary" href="<?php echo esc_url( gp_url( '/events/new/' ) ); ?>">Create Event</a></li>
			<?php endif; ?>
		<?php endif; ?>
	</ul>
	<?php if ( isset( $event ) && ! isset( $event_form_name ) ) : ?>
	<p class="event-sub-head">
			<span class="event-host">
				<?php
				if ( 1 === count( $hosts ) ) :
					esc_html_e( 'Host:', 'gp-translation-events' );
				else :
					esc_html_e( 'Hosts:', 'gp-translation-events' );
				endif;
				?>
				<?php foreach ( $hosts as $host ) : ?>
					<?php $user = get_userdata( $host->user_id() ); ?>
					&nbsp;<a href="<?php echo esc_attr( get_author_posts_url( $user->ID ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $user->ID ) ); ?></a>
					<?php if ( end( $hosts ) !== $host ) : ?>
						,
					<?php endif; ?>
				<?php endforeach; ?>
			.</span>
			<?php $show_edit_button = ( ( $user_is_attending && $attendee->is_host() ) || current_user_can( 'edit_post', $event->id() ) ) && $is_editable_event; ?>
			<?php if ( $show_edit_button ) : ?>
				<a class="event-page-edit-link" href="<?php echo esc_url( gp_url( 'events/edit/' . $event->id() ) ); ?>"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Edit event', 'gp-translation-events' ); ?></a>
			<?php endif ?>
		</p>
		<?php endif; ?>

</div>
