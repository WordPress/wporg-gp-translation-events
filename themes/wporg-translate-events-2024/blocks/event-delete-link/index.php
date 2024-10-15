<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Translation_Events;
use Wporg\TranslationEvents\Urls;

register_block_type(
	'wporg-translate-events-2024/event-delete-link',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes, $content, $block ) {
			if ( ! isset( $block->context['postId'] ) ) {
				return '';
			}
			$event_id = $block->context['postId'];
			$event = Translation_Events::get_event_repository()->get_event( $event_id );
			if ( ! $event ) {
				return '';
			}
			ob_start();
			if ( ! current_user_can( 'delete_translation_event', $event->id() ) || 'trash' !== $event->status() ) {
				return '';
			}
			?>
				<a href="<?php echo esc_url( Urls::event_delete( $event->id() ) ); ?>"
					class="button is-small is-destructive"
					title="<?php echo esc_attr__( 'Delete permanently', 'gp-translation-events' ); ?>">
					<?php echo esc_attr__( 'Delete permanently', 'gp-translation-events' ); ?>
				</a>
					<?php
					return ob_get_clean();
		},
	)
);
