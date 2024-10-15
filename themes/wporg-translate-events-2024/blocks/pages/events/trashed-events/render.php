<?php
namespace Wporg\TranslationEvents\Theme_2024;
$event_ids = $attributes['trashed_events_query'] ['event_ids'] ?? array();
$data      = array(
	'event_ids' => $event_ids,
	'show_flag' => false,
);
?>
<!-- wp:wporg-translate-events-2024/event-list <?php echo wp_json_encode( $data ); ?> /-->
