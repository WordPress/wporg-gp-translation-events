<?php
gp_title( __( 'Translation Events' ) );
gp_tmpl_header();

?>

<h2 class="event_page_title">Upcoming Translation Events</h2>
<?php
$current_datetime_utc = ( new DateTime( null, new DateTimeZone( 'UTC' ) ) )->format( 'Y-m-d H:i:s' );
$_paged               = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args                 = array(
	'post_type'      => 'event',
	'posts_per_page' => 10,
	'paged'          => $_paged,
	'post_status'    => 'publish',
	'meta_query'     => array(
		array(
			'key'     => '_event_start',
			'value'   => $current_datetime_utc,
			'compare' => '>=',
			'type'    => 'DATETIME',
		),
	),
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
);

$query = new WP_Query( $args );

if ( $query->have_posts() ) :
	?>
	<ul>
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$event_start = ( new DateTime( get_post_meta( get_the_ID(), '_event_start', true ) ) )->format('l, F j, Y');
			?>
			<li class="event-list-item">
				<span class="event-list-date"><?php echo esc_html( $event_start ); ?></span>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> by <span><?php the_author(); ?></span>
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
