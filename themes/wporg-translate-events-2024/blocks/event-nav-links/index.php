<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Urls;

register_block_type(
	'wporg-translate-events-2024/event-nav-links',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function () {

			ob_start();
			?>
			<!-- wp:group {"align":"right","layout":{"type":"flex","justifyContent":"right"}} -->
			<div class="wp-block-group alignright" style="width: 35%; margin-left: auto; padding-right:var(--wp--preset--spacing--edge-space);">
				<div class="wp-block-group__inner-container" style="display: flex; justify-content: flex-end; gap: 10px; align-items: center;">
					<?php if ( is_user_logged_in() ) : ?>
						<?php if ( current_user_can( 'manage_translation_events' ) ) : ?>
						<a class="event-nav-link" href="<?php echo esc_url( Urls::events_trashed() ); ?>">Deleted Events</a>
					<?php endif; ?>
					<a class="event-nav-link" href="<?php echo esc_url( Urls::my_events() ); ?>">My Events</a>
						<?php if ( current_user_can( 'create_translation_event' ) ) : ?>
							<!-- wp:button {"className":"is-style-outline"} -->
							<div class="wp-block-button is-style-outline">
								<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( Urls::event_create() ); ?>">Create Event</a>
							</div>
							<!-- /wp:button -->
					<?php endif; ?>
				<?php endif; ?>
				</div>
			</div>
			<!-- /wp:group -->
			<?php
			return ob_get_clean();
		},
	)
);
