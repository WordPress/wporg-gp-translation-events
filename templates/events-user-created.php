<?php
gp_title( __( 'Translation Events' ) . ' - ' . esc_html( 'My Events' ) );
gp_tmpl_header();
gp_tmpl_load( 'events-header', get_defined_vars(), dirname( __FILE__ ) );
?>



<h2 class="event_page_title">My Events</h2>
<div>
<?php
if ( $query->have_posts() ) :
	?>
	<ul>
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$event_start                   = get_post_meta( get_the_ID(), '_event_start', true );
			list( $permalink, $post_name ) = get_sample_permalink( get_the_ID() );
			$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
			?>
			<li class="event-list-item">
				<span class="event-list-date"><time class="event-utc-time" datetime="<?php echo esc_attr( $event_start ); ?>"></span>
				<a href="<?php echo esc_url( gp_url( wp_make_link_relative( $permalink ) ) ); ?>"><?php the_title(); ?></a>
				<span class="event-list-status"><?php echo 'draft' == get_post_status( get_the_ID() ) ? esc_html( '[' . get_post_status( get_the_ID() ) . ']' ) : ''; ?></span>
				<p><?php the_excerpt(); ?></p>
			</li>
			<?php
		endwhile;
		?>
	</ul>

	<?php
	echo paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'prev_text' => '&laquo; Previous',
			'next_text' => 'Next &raquo;',
		)
	);

	wp_reset_postdata();
else :
	echo 'No events found.';
endif;
?>
</div>
