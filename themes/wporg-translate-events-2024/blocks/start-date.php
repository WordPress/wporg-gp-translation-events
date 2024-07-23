<?php namespace Wporg\TranslationEvents\Theme_2024;
use Wporg\TranslationEvents\Event\Events_Query_Result;

register_block_type(
	'wporg-translate-events-2024/start-date',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Events_Query_Result::get_event( $attributes['id'] );
			return esc_html( $event->start() );
		},
	)
);
register_block_type(
	'wporg-translate-events-2024/title',
	array(
		// The $attributes argument cannot be removed despite not being used in this function,
		// because otherwise it won't be available in render.php.
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		'render_callback' => function ( array $attributes ) {
			$event = Events_Query_Result::get_event( $attributes['id'] );
			return  esc_html( $event->title() );
		},
	)
);
