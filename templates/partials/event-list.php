<?php
namespace Wporg\TranslationEvents\Templates\Partials;

use Wporg\TranslationEvents\Event\Event_End_Date;
use Wporg\TranslationEvents\Event\Event_Start_Date;
use Wporg\TranslationEvents\Event\Events_Query_Result;
use Wporg\TranslationEvents\Urls;

/** @var Events_Query_Result $query */
/** @var ?string $pagination_query_param */
/** @var ?bool $show_start */
/** @var ?bool $show_end */
/** @var ?bool $show_excerpt */
/** @var ?string $date_format */
/** @var ?bool $relative_time */
/** @var ?string[] $extra_classes */

$show_start    = $show_start ?? false;
$show_end      = $show_end ?? false;
$show_excerpt  = $show_excerpt ?? true;
$date_format   = $date_format ?? '';
$relative_time = $relative_time ?? true;
$extra_classes = isset( $extra_classes ) ? implode( $extra_classes, ' ' ) : '';

/**
 * @param Event_Start_Date|Event_End_Date $time
 */
$print_time = function ( $time ) use ( $date_format, $relative_time ): void {
	if ( $relative_time ) {
		$time->print_relative_time_html( $date_format );
	} else {
		$time->print_time_html( $date_format );
	}
};
?>

<ul class="event-list <?php echo esc_attr( $extra_classes ); ?>">
	<?php foreach ( $query->events as $event ) : ?>
		<li class="event-list-item">
			<?php // Title. ?>
			<?php // phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace ?>
			<a <?php if ( $event->is_draft() ) : ?>class="event-link-draft" <?php endif; ?>
				href="<?php echo esc_url( Urls::event_details( $event->id() ) ); ?>"
			>
				<?php echo esc_html( $event->title() ); ?>
			</a>
			<?php if ( $event->is_draft() ) : ?>
				<span class="event-label-draft">Draft</span>
			<?php endif; ?>

			<?php // Buttons. ?>
			<?php if ( current_user_can( 'edit_translation_event', $event->id() ) ) : ?>
				<a href="<?php echo esc_url( Urls::event_edit( $event->id() ) ); ?>" class="button is-small action edit">Edit</a>
			<?php endif; ?>
			<?php if ( current_user_can( 'trash_translation_event', $event->id() ) ) : ?>
				<?php if ( $event->is_trashed() ) : ?>
					<a href="<?php echo esc_url( Urls::event_trash( $event->id() ) ); ?>" class="button is-small">Restore</a>
				<?php else : ?>
					<a href="<?php echo esc_url( Urls::event_trash( $event->id() ) ); ?>" class="button is-small is-destructive">Delete</a>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( current_user_can( 'manage_translation_events' ) ) : ?>
				<?php if ( current_user_can( 'delete_translation_event', $event->id() ) ) : ?>
					<a href="<?php echo esc_url( Urls::event_delete( $event->id() ) ); ?>" class="button is-small is-destructive">Delete Permanently</a>
				<?php endif; ?>
			<?php endif; ?>

			<?php // Dates. ?>
			<?php if ( $show_start ) : ?>
				<?php if ( $event->start()->is_in_the_past() ) : ?>
					<span class="event-list-date">started <?php $print_time( $event->start() ); ?></span>
				<?php else : ?>
					<span class="event-list-date">starts <?php $print_time( $event->start() ); ?></span>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( $show_end ) : ?>
				<?php if ( $event->end()->is_in_the_past() ) : ?>
					<span class="event-list-date">ended <?php $print_time( $event->end() ); ?></span>
				<?php else : ?>
					<span class="event-list-date">ends <?php $print_time( $event->end() ); ?></time></span>
				<?php endif; ?>
			<?php endif; ?>

			<?php // Excerpt. ?>
			<?php if ( $show_excerpt ) : ?>
				<?php echo esc_html( get_the_excerpt( $event->id() ) ); ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php
if ( ! empty( $pagination_parameter ) ) {
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $query->page_count,
				'current'   => $query->current_page,
				'format'    => "?$pagination_query_param=%#%",
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
			)
		) ?? ''
	);
}
