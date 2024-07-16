<?php namespace Wporg\TranslationEvents\Blocks\EventListQuery;

use WP_Block_Type;
use Wporg\TranslationEvents\Translation_Events;

add_filter(
	'get_block_type_variations',
	function ( array $variations, WP_Block_Type $block_type ) {
		$variation_name = 'wporg-translate/event-query';
		if ( 'core/query' === $block_type->name ) {
			$variations[] = array(
				'name'            => $variation_name,
				'title'           => __( 'Event Query Loop', 'gp-translation-events' ),
				'description'     => __( 'Displays a list of Events', 'gp-translation-events' ),
				'icon'            => 'calendar-alt',
				'attributes'      => array(
					'namespace' => $variation_name,
					'query'     => array(
						'postType' => Translation_Events::CPT,
					),
				),
				'isActive'        => array( 'namespace' ),
				'allowedControls' => array(),
				'innerBlocks'     => array(
					array(
						'core/post-template',
						array(),
						array(
							array( 'core/post-title' ),
							array( 'core/post-content' ),
						),
					),
					array( 'core/query-pagination' ),
					array( 'core/query-no-results' ),
				),
			);
		}

		return $variations;
	},
	10,
	2
);
