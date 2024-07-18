<?php

namespace Wporg\TranslationEvents\Blocks\EventList;

add_action(
	'init',
	function () {
		register_block_type(
			__DIR__ . '/../../../build/blocks/event-list',
			array(
				'attributes'      => array(),
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
	include_once __DIR__ . '/render.php';
}
