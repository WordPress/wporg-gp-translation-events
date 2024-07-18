<?php

namespace Wporg\TranslationEvents\Blocks\EventList;

use Wporg\TranslationEvents\Translation_Events;

add_action(
	'init',
	function () {
		register_block_type(
			__DIR__ . '/../../../build/blocks/event-list',
			array(
				'attributes'      => array(
					'filter' => array(
						'type' => 'string',
					),
				),
				'render_callback' => function ( array $attributes ) {
					ob_start();
					render( $attributes );
					return do_blocks( ob_get_clean() );
				},
			)
		);
	}
);

function render( array $attributes ): void {
	$event_repository = Translation_Events::get_event_repository();
	$filter           = $attributes['filter'] ?? '';

	switch ( $filter ) {
		case 'past':
			$events = $event_repository->get_past_events();
			break;
		case 'upcoming':
			$events = $event_repository->get_upcoming_events();
			break;
		case 'current':
		default:
			$events = $event_repository->get_current_events();
			break;
	}

	include_once __DIR__ . '/render.php';
}
