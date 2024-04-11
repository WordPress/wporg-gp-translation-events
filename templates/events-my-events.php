<?php
/**
 * Template for My Events.
 */

namespace Wporg\TranslationEvents;

use Wporg\TranslationEvents\Event\Events_Query_Result;
use Wporg\TranslationEvents\Stats\Stats_Calculator;

/** @var Events_Query_Result $events_i_created_query */
/** @var Events_Query_Result $events_i_attended_query */

gp_title( esc_html__( 'Translation Events', 'gp-translation-events' ) . ' - ' . esc_html__( 'My Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events( array( esc_html__( 'My Events', 'gp-translation-events' ) ) );
gp_tmpl_header();
$event_page_title = __( 'My Events', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<div class="event-page-wrapper">
	<h2><?php esc_html_e( 'Events I have created', 'gp-translation-events' ); ?> </h2>
	<?php if ( ! empty( $events_i_created_query->events ) ) : ?>
		<ul>
		<?php
		foreach ( $events_i_created_query->events as $event ) :
			list( $permalink, $post_name ) = get_sample_permalink( $event->id() );
			$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
			$event_url                     = gp_url( wp_make_link_relative( $permalink ) );
			$event_edit_url                = gp_url( 'events/edit/' . $event->id() );
			$stats_calculator              = new Stats_Calculator();
			$has_stats                     = $stats_calculator->event_has_stats( $event->id() );
			?>
			<li class="event-list-item">
				<a class="event-link-<?php echo esc_attr( $event->status() ); ?>" href="<?php echo esc_url( $event_url ); ?>"><?php echo esc_html( $event->title() ); ?></a>
				<?php if ( ! $event->end()->is_in_the_past() && ! $has_stats ) : ?>
					<a href="<?php echo esc_url( $event_edit_url ); ?>" class="button is-small action edit">Edit</a>
				<?php endif; ?>
				<?php if ( 'draft' === $event->status() ) : ?>
					<span class="event-label-<?php echo esc_attr( $event->status() ); ?>"><?php echo esc_html( $event->status() ); ?></span>
				<?php endif; ?>
				<?php if ( $event->start()->format( 'Y-m-d' ) === $event->end()->format( 'Y-m-d' ) ) : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?></span>
				<?php else : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?> - <?php $event->end()->print_time_html(); ?></span>
				<?php endif; ?>
				<p><?php echo esc_html( get_the_excerpt( $event->id() ) ); ?></p>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $events_i_created_query->page_count,
					'current'   => $events_i_created_query->current_page,
					'format'    => '?events_i_created_paged=%#%',
					'prev_text' => '&laquo; Previous',
					'next_text' => 'Next &raquo;',
				)
			) ?? ''
		);

		wp_reset_postdata();
	else :
		echo 'No events found.';
	endif;
	?>

	<h2><?php esc_html_e( 'Events I attended', 'gp-translation-events' ); ?> </h2>
	<?php if ( ! empty( $events_i_attended_query->events ) ) : ?>
		<ul>
		<?php
		foreach ( $events_i_attended_query->events as $event ) :
			list( $permalink, $post_name ) = get_sample_permalink( $event->id() );
			$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
			$event_url                     = gp_url( wp_make_link_relative( $permalink ) );
			?>
			<li class="event-list-item">
				<a class="event-link-<?php echo esc_attr( $event->status() ); ?>" href="<?php echo esc_url( $event_url ); ?>"><?php echo esc_html( $event->title() ); ?></a>
				<?php if ( $event->start() === $event->end() ) : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?></span>
				<?php else : ?>
					<span class="event-list-date events-i-am-attending"><?php $event->start()->print_time_html(); ?> - <?php $event->end()->print_time_html(); ?></span>
				<?php endif; ?>
				<p><?php echo esc_html( get_the_excerpt( $event->id() ) ); ?></p>
			</li>
		<?php endforeach; ?>
		</ul>

		<?php
		echo wp_kses_post(
			paginate_links(
				array(
					'total'     => $events_i_attended_query->page_count,
					'current'   => $events_i_attended_query->current_page,
					'format'    => '?events_i_attended_paged=%#%',
					'prev_text' => '&laquo; Previous',
					'next_text' => 'Next &raquo;',
				)
			) ?? ''
		);

		wp_reset_postdata();
	else :
		echo 'No events found.';
	endif;
	?>
</div>
<?php
	gp_tmpl_footer();
?>
