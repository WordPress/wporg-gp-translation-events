<?php
namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Translation_Events;

register_block_type(
	'wporg-translate-events-2024/event-attendance-mode',
	array(
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event_id = $attributes['id'] ?? 0;
			$event = Translation_Events::get_event_repository()->get_event( $event_id );

			return '<div class="wporg-marker-list-item__attendance-mode">
' . esc_html( $event->attendance_mode() ) . '</div>';
		},
	)
);
