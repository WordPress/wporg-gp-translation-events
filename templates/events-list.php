<?php
/**
 * Events list page.
 */

namespace Wporg\TranslationEvents;

use DateTime;
use WP_Query;
use Wporg\TranslationEvents\Event\Event;

/** @var WP_Query $current_events_query */
/** @var WP_Query $upcoming_events_query */
/** @var WP_Query $past_events_query */

gp_title( __( 'Translation Events', 'gp-translation-events' ) );
gp_breadcrumb_translation_events();
gp_tmpl_header();
$event_page_title = __( 'Translation Events', 'gp-translation-events' );
gp_tmpl_load( 'events-header', get_defined_vars(), __DIR__ );
?>

<div class="event-page-wrapper">
<div class="event-left-col">
<?php
if ( $current_events_query->have_posts() ) :
	?>
	<h2><?php esc_html_e( 'Current events', 'gp-translation-events' ); ?></h2>
	<ul class="event-list">
		<?php
		while ( $current_events_query->have_posts() ) :
			$current_events_query->the_post();
			$event_end = new Event_End_Date( get_post_meta( get_the_ID(), '_event_end', true ) );
			$event_url = gp_url( wp_make_link_relative( get_the_permalink() ) );
			?>
			<li class="event-list-item">
				<a href="<?php echo esc_url( $event_url ); ?>"><?php the_title(); ?></a>
				<span class="event-list-date">ends <?php $event_end->print_relative_time_html(); ?></time></span>
				<?php the_excerpt(); ?>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $current_events_query->max_num_pages,
				'current'   => max( 1, $current_events_query->query_vars['paged'] ),
				'format'    => '?current_events_paged=%#%',
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
			)
		) ?? ''
	);

	wp_reset_postdata();
endif;
if ( $upcoming_events_query->have_posts() ) :
	?>
	<h2><?php esc_html_e( 'Upcoming events', 'gp-translation-events' ); ?></h2>
	<ul class="event-list">
		<?php
		while ( $upcoming_events_query->have_posts() ) :
			$upcoming_events_query->the_post();
			$event_start = new Event_Start_Date( get_post_meta( get_the_ID(), '_event_start', true ) );
			?>
			<li class="event-list-item">
				<a href="<?php echo esc_url( gp_url( wp_make_link_relative( get_the_permalink() ) ) ); ?>"><?php the_title(); ?></a>
				<span class="event-list-date">starts <?php $event_start->print_relative_time_html(); ?></span>
				<?php the_excerpt(); ?>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $upcoming_events_query->max_num_pages,
				'current'   => max( 1, $upcoming_events_query->query_vars['paged'] ),
				'format'    => '?upcoming_events_paged=%#%',
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
			)
		) ?? ''
	);

	wp_reset_postdata();
endif;
if ( $past_events_query->have_posts() ) :
	?>
	<h2><?php esc_html_e( 'Past events', 'gp-translation-events' ); ?></h2>
	<ul class="event-list">
		<?php
		while ( $past_events_query->have_posts() ) :
			$past_events_query->the_post();
			$event_end = new Event_End_Date( get_post_meta( get_the_ID(), '_event_end', true ) );
			?>
			<li class="event-list-item">
				<a href="<?php echo esc_url( gp_url( wp_make_link_relative( get_the_permalink() ) ) ); ?>"><?php the_title(); ?></a>
				<span class="event-list-date">ended <?php $event_end->print_relative_time_html( 'F j, Y H:i T' ); ?></span>
				<?php the_excerpt(); ?>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo wp_kses_post(
		paginate_links(
			array(
				'total'     => $past_events_query->max_num_pages,
				'current'   => max( 1, $past_events_query->query_vars['paged'] ),
				'format'    => '?past_events_paged=%#%',
				'prev_text' => '&laquo; Previous',
				'next_text' => 'Next &raquo;',
			)
		) ?? ''
	);

	wp_reset_postdata();
endif;

if ( 0 === $current_events_query->post_count && 0 === $upcoming_events_query->post_count && 0 === $past_events_query->post_count ) :
	esc_html_e( 'No events found.', 'gp-translation-events' );
endif;
?>
</div>
<?php if ( is_user_logged_in() ) : ?>
	<div class="event-right-col">
		<h2>Events I'm Attending</h2>
		<?php if ( ! $user_attending_events_query->have_posts() ) : ?>
			<p>You don't have any events to attend.</p>
		<?php else : ?>
			<ul class="event-attending-list">
				<?php
				while ( $user_attending_events_query->have_posts() ) :
					$user_attending_events_query->the_post();
					$event_start = new Event_Start_Date( get_post_meta( get_the_ID(), '_event_start', true ) );
					$event_end   = new Event_End_Date( get_post_meta( get_the_ID(), '_event_end', true ) );
					?>
					<li class="event-list-item">
						<a href="<?php echo esc_url( gp_url( wp_make_link_relative( get_the_permalink() ) ) ); ?>"><?php the_title(); ?></a>
						<?php if ( $event_start === $event_end ) : ?>
							<span class="event-list-date events-i-am-attending"><?php $event_start->print_time_html( 'F j, Y H:i T' ); ?></span>
						<?php else : ?>
							<span class="event-list-date events-i-am-attending"><?php $event_start->print_time_html( 'F j, Y H:i T' ); ?> - <?php $event_end->print_time_html( 'F j, Y H:i T' ); ?></span>
						<?php endif; ?>
					</li>
					<?php
				endwhile;
				?>
			</ul>
			<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $user_attending_events_query->max_num_pages,
							'current'   => max( 1, $user_attending_events_query->query_vars['paged'] ),
							'format'    => '?user_attending_events_paged=%#%',
							'prev_text' => '&laquo; Previous',
							'next_text' => 'Next &raquo;',
						)
					) ?? ''
				);

				wp_reset_postdata();
		endif;
		?>
	</div>
<?php endif; ?>
</div>
<div class="clear"></div>
<?php gp_tmpl_footer(); ?>
