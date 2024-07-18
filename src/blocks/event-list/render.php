<?php

namespace Wporg\TranslationEvents\Blocks\EventList;

use Wporg\TranslationEvents\Event\Events_Query_Result;

/** @var Events_Query_Result $events */
?>

<ul>
<?php foreach ( $events->events as $event ) : ?>
	<li>
		<!-- wp:paragraph -->
		<p><span>title: </span><?php echo esc_html( $event->title() ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph -->
		<p><span>start: </span><?php echo esc_html( (string) $event->start() ); ?></p>
		<!-- /wp:paragraph -->
	</li>
<?php endforeach; ?>
</ul>
