<?php
gp_title( __( 'Translation Events' ) . ' - ' . esc_html__( 'My Events' ) );
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
			$event_id                      = get_the_ID();
			$event_start                   = get_post_meta( $event_id, '_event_start', true );
			list( $permalink, $post_name ) = get_sample_permalink( $event_id );
			$permalink                     = str_replace( '%pagename%', $post_name, $permalink );
			$event_status                  = ( 'publish' === get_post_status( $event_id ) ) ? 'published' : get_post_status( $event_id );
			?>
			<li class="event-list-item">
				<a class="event-status-<?php echo esc_attr( $event_status ); ?>" href="<?php echo esc_url( gp_url( wp_make_link_relative( $permalink ) ) ); ?>"><?php echo ( ( 'draft' === $event_status ) ? esc_html( ucfirst( $event_status ) ) . ' - ' : '' ) . the_title( '', '', false ); ?></a>
				<a href="<?php echo esc_url( gp_url( 'events/edit/' . $event_id ) ); ?>" class="button is-small action edit">Edit</a>
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
