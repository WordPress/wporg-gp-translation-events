<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Urls;

register_block_type(
	'wporg-translate-events-2024/event-attend-button',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			if ( ! isset( $attributes['id'] ) || ! isset( $attributes['user_is_attending'] ) || ! isset( $attributes['user_is_contributor'] ) ) {
				return '';
			}
			$event_id = $attributes['id'];
			$user_is_attending = $attributes['user_is_attending'];
			$event = Translation_Events::get_event_repository()->get_event( $event_id );
			if ( ! $event ) {
				return '';
			}

			ob_start();
			if ( is_user_logged_in() ) :
				?>
		<div class="event-details-join">
				<?php if ( ! $event->end()->is_in_the_past() ) : ?>
			<form class="event-details-attend" method="post" action="<?php echo esc_url( Urls::event_toggle_attendee( $event->id() ) ); ?>">
					<?php wp_nonce_field( '_attendee_nonce', '_attendee_nonce' ); ?>
					<?php if ( $user_is_attending ) : ?>
					<input type="submit" class="wp-block-button__link" value="<?php esc_attr_e( "You're attending", 'gp-translation-events' ); ?>" />
				<?php else : ?>
					<?php if ( ! $event->is_remote() ) : ?>
						<input type="submit" class="wp-block-button__link" value="<?php esc_attr_e( 'Attend Event On-site', 'gp-translation-events' ); ?>"/>
					<?php endif; ?>
					<?php if ( $event->is_remote() || $event->is_hybrid() ) : ?>
						<input type="submit" name="attend_remotely" class="wp-block-button__link" value="<?php esc_attr_e( 'Attend Event Remotely', 'gp-translation-events' ); ?>"/>
					<?php endif; ?>
				<?php endif; ?>
			</form>
			<?php endif; ?>
		</div>
		<?php else : ?>
		<div class="event-details-join">
			<p>
				<?php if ( ! $event->end()->is_in_the_past() ) : ?>
					<a href="<?php echo esc_url( wp_login_url() ); ?>" class="wp-block-button__link"><?php esc_html_e( 'Login to attend', 'gp-translation-events' ); ?></a>
				<?php else : ?>
					<button disabled="disabled" class="wp-block-button__link"><?php esc_html_e( 'Event is over', 'gp-translation-events' ); ?></button>
				<?php endif; ?>
			</p>
		</div>
			<?php
			endif;
			return ob_get_clean();
		},
	)
);
