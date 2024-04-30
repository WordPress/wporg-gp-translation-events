<?php
/**
 * Events list page.
 */

namespace Wporg\TranslationEvents;

gp_title( __( 'Translation Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events();
gp_tmpl_header();
$event_page_title = __( 'Manage Attendees', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );



?>
<div class="event-page-wrapper">
	<div class="event-details-stats">
	<a href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>" class="view-event-page">View Event</a>
	<ul class="event-attendees-filter">
		<li><a href="<?php echo esc_url( Urls::event_attendees( $event_slug ) ); ?>"><?php esc_html_e( 'All attendees', 'gp-translation-events' ); ?></a></a></li>
		<li><a href="?filter=hosts"><?php esc_html_e( 'Hosts', 'gp-translation-events' ); ?></a></li>
	</ul>
<?php if ( ! empty( $attendees ) ) : ?>
	<table>
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Name', 'gp-translation-events' ); ?></th>
				<th><?php esc_html_e( 'Host', 'gp-translation-events' ); ?></th>
				<th><?php esc_html_e( 'Action', 'gp-translation-events' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $attendees as $attendee ) : ?>
				<tr>
					<td>
						<a class="attendee-avatar" href="<?php echo esc_url( get_author_posts_url( $attendee->user_id() ) ); ?>" class="avatar"><?php echo get_avatar( $attendee->user_id(), 48 ); ?></a>
						<a href="<?php echo esc_url( get_author_posts_url( $attendee->user_id() ) ); ?>" class="name"><?php echo esc_html( get_the_author_meta( 'display_name', $attendee->user_id() ) ); ?></a>
					</td>
					<td>
						<?php if ( $attendee->is_host() ) : ?>
							<span><?php esc_html_e( 'Yes', 'gp-translation-events' ); ?></span>
							<?php endif; ?>
					</td>
					<td>
					<form class="add-remove-user-as-host" method="post" action="<?php echo esc_url( Urls::event_toggle_host( $event->id(), $attendee->user_id() ) ); ?>">
						<?php if ( $attendee->is_host() ) : ?>
							<input type="submit" class="button is-primary remove-as-host" value="<?php echo esc_attr__( 'Remove as host', 'gp-translation-events' ); ?>"/>
							<?php else : ?>
									<input type="submit" class="button is-secondary convert-to-host" value="<?php echo esc_attr__( 'Make co-host', 'gp-translation-events' ); ?>"/>
							<?php endif; ?>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php else : ?>
		<p><?php esc_html_e( 'No attendees found.', 'gp-translation-events' ); ?></p>
	</div>
<?php endif; ?>
<?php
gp_tmpl_footer();
