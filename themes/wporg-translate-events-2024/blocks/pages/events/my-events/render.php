<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Event\Events_Query_Result;

/** @var Events_Query_Result $events */
$events = $attributes['events'] ?? array();

global $translation_events;
global $translation_events_lookup;
foreach ( $translation_events->events as $event ) {
	$translation_events_lookup[ $event->id() ] = $event;
	?>
	<!-- wp:wporg-translate-events-2024/title <?php echo json_encode( array('id'=>$event->id())); ?> /-->
	<!-- wp:wporg-translate-events-2024/start-date <?php echo json_encode( array('id'=>$event->id())); ?> /-->
	<?php
}
?>
