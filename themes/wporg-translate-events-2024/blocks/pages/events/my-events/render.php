<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Event\Events_Query_Result;

/** @var Events_Query_Result $events */
$events = $attributes['events'] ?? array();

?>
<!-- wp:wporg-translate-events-2024/header <?php
echo wp_json_encode( array(
	'title' => __( 'My Events', 'wporg-translate-events-2024' ),
) ); ?> /-->
<!-- wp:paragraph --><p>my-events</p><!-- /wp:paragraph -->
<!-- wp:wporg-translate-events-2024/footer /-->
