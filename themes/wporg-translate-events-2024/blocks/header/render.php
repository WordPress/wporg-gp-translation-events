<?php namespace Wporg\TranslationEvents\Theme_2024;

/** @var array $attributes */

$html_title = implode( ' | ', array( $attributes['title'], __( 'Translation Events', 'wporg-translate-events-2024' ) ) );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title><?php echo esc_html( $html_title ); ?></title>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<div class="wp-site-blocks">
			<!-- wp:wporg/global-header {"style":{"border":{"bottom":{"color":"var:preset|color|white-opacity-15","style":"solid","width":"1px"}}}} /-->

			<!-- wp:wporg/local-navigation-bar {"backgroundColor":"charcoal-2"} -->
			<!-- wp:site-title {"level":0,"fontSize":"small"} /-->
			<!-- wp:navigation {"menuSlug":"site-header-menu", "icon":"menu","backgroundColor": "charcoal-2", "overlayBackgroundColor":"charcoal-2","overlayTextColor":"white","layout":{"type":"flex","orientation":"horizontal"},"fontSize":"small"} /-->
			<!-- /wp:wporg/local-navigation-bar -->

			<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"18px","bottom":"18px","left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}},"backgroundColor":"white","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
			<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-top:18px;padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:18px;padding-left:var(--wp--preset--spacing--edge-space)">
				<!-- wp:wporg/site-breadcrumbs {"fontSize":"small"} /-->
			</div>
			<!-- /wp:group -->
			<div class="wp-block-group alignfull has-white-background-color has-background" style="padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:18px;padding-left:var(--wp--preset--spacing--edge-space)">
				<h2 class="wp-block-heading"><?php echo esc_html( $attributes['title'] ); ?></h2>
