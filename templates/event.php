<?php
/* translators: %s: Event title. */
gp_title( sprintf( __( 'Translation Events - %s' ), esc_html( $event_title ) ) );
gp_tmpl_header();
?>

<div class="event-details-page">
	<div class="event-details-head">
		<h1><?php echo esc_html( $event_title ); ?></h1>
		<p>Host: <a href="<?php echo esc_attr( get_author_posts_url( $event->post_author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $event->post_author ) ); ?></a></p>
	</div>
<div class="event-details-left">
	<div class="event-page-content">
		<?php echo esc_html( $event_description ); ?>
	</div>
</div>
	<div class="event-details-right">
		<div class="event-details-date">
			<p><span class="dashicons dashicons-calendar"></span> <?php echo esc_html( ( new DateTime( $event_start_date ) )->format( 'l, F j, Y' ) ); ?></p>
			<p><span class="dashicons dashicons-clock"></span> 13:00 - 15:00</p>
		</div>
		<form class="event-details-attend">
			<?php if ( ! $user_is_attending ): ?>
				<input type="submit" class="button is-primary" value="Attend Event"/>
			<?php else: ?>
				<input type="submit" class="button is-secondary" value="You're attending"/>
			<?php endif ?>
		</form>
	</div>
</div>
