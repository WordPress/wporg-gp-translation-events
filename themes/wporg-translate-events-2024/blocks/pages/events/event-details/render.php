<?php
namespace Wporg\TranslationEvents\Theme_2024;

use Wporg\TranslationEvents\Urls;

$event               = $attributes['event'];
$user_is_attending   = $attributes['user_is_attending'];
$user_is_contributor = $attributes['user_is_contributor'];

?>

<div class="wp-block-grid is-layout-grid details-page-title">
	<div>
		<h2 class="wp-block-heading"><?php echo esc_html( $event->title() ); ?></h2>
	</div>
	<?php if ( current_user_can( 'edit_translation_event', $event->id() ) ) : ?>
		<div>
			<div class="wp-block-buttons is-content-justification-end">
				<div class="wp-block-button is-style-outline">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( Urls::event_edit( $event->id() ) ); ?>">
						<?php echo esc_html__( 'Edit Event', 'wporg-translate-events-2024' ); ?>
					</a>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php if ( $event->is_past() ) : ?>
	<!-- wp:wporg/notice {"type":"alert"} -->
	<div class="wp-block-wporg-notice is-alert-notice">
			<div class="wp-block-wporg-notice__icon"></div>
			<div class="wp-block-wporg-notice__content">
				<p>
				<?php echo esc_html__( 'This event has ended.', 'wporg-translate-events-2024' ); ?>
				</p>
			</div>
		</div>
		<!-- /wp:wporg/notice -->

<?php else : ?>
<!-- wp:wporg-translate-events-2024/event-attend-button 
	<?php
	echo wp_json_encode(
		array(
			'id'                  => $event->id(),
			'user_is_attending'   => $user_is_attending,
			'user_is_contributor' => $user_is_contributor,
		)
	);
	?>

/-->	
<?php endif; ?>

<?php
if ( is_user_logged_in() ) :
	if ( $event->is_past() ) :
		if ( $user_is_attending ) :
			?>
			<!-- wp:wporg/notice {"type":"info", "style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
			<div class="wp-block-wporg-notice is-info-notice" style="margin-top:var(--wp--preset--spacing--20)">
				<div class="wp-block-wporg-notice__icon"></div>
				<div class="wp-block-wporg-notice__content">
					<p>
						<?php esc_html_e( 'You attended this event.', 'wporg-translate-events-2024' ); ?>
					</p>
				</div>
			</div>
			<!-- /wp:wporg/notice -->
		<?php endif; ?>
	<?php else : ?>
		<?php if ( ! $event->is_hybrid() && ! $event->is_remote() ) : ?>
			<!-- wp:wporg/notice {"type":"tip","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
			<div class="wp-block-wporg-notice is-tip-notice" style="margin-top:var(--wp--preset--spacing--20)">
				<div class="wp-block-wporg-notice__icon"></div>
				<div class="wp-block-wporg-notice__content">
					<p>
						<?php echo wp_kses_post( __( 'This is an onsite-only event. Please only click attend if you are at the event. The host might otherwise remove you.', 'wporg-translate-events-2024' ) ); ?>
					</p>
				</div>
			</div>
			<!-- /wp:wporg/notice -->
		<?php endif; ?>	
		<?php
	endif;
endif;
?>

<!-- wp:paragraph -->
<p>
<!-- wp:wporg-translate-events-2024/event-host-list <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->

<span> Start Date: <strong><?php $event->start()->print_time_html(); ?></strong></span>
<span> End Date: <strong><?php $event->end()->print_time_html(); ?></strong></span>
</p>
<!-- /wp:paragraph -->

<!-- wp:wporg-translate-events-2024/event-description <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/contributor-list <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/attendee-list 
<?php
echo wp_json_encode(
	array(
		'id'        => $event->id(),
		'view_type' => 'list',
	)
);
?>

/-->
<!-- wp:wporg-translate-events-2024/event-stats <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/event-projects <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
<!-- wp:wporg-translate-events-2024/event-contribution-summary <?php echo wp_json_encode( array( 'id' => $event->id() ) ); ?> /-->
