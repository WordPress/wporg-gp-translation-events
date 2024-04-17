<?php

namespace Wporg\TranslationEvents;

use GP;
use Wporg\TranslationEvents\Event\Event;

/** @var Event  $event */

/* translators: %s: Event title. */
gp_title( sprintf( __( 'Translation Events - %s' ), esc_html( $event->title() ) ) );
gp_breadcrumb_translation_events( array( '<a href="' . esc_attr( gp_url( wp_make_link_relative( get_the_permalink( $event->id() ) ) ) ) . '">' . esc_html( $event->title() ) . '</a>', __( 'Translations', 'glotpress' ), $locale ) );
gp_enqueue_scripts( array( 'gp-editor', 'gp-translations-page' ) );
wp_localize_script(
	'gp-translations-page',
	'$gp_translations_options',
	array(
		'sort'   => __( 'Sort', 'glotpress' ),
		'filter' => __( 'Filter', 'glotpress' ),
	)
);

gp_tmpl_header();
?>

<div class="event-list-top-bar">
<h2 class="event-page-title">
	<?php echo esc_html( $event->title() ); ?>
	<?php if ( isset( $event ) && 'draft' === $event->status() ) : ?>
				<span class="event-label-draft"><?php echo esc_html( $event->status() ); ?></span>
			<?php endif; ?>
</h2>
</div>
<div class="event-page-wrapper">
	<h4>
	<?php
	echo esc_html(
		sprintf(
		// Translators: %s is the locale name.
			__( 'Translations to %s', 'glotpress' ),
			$locale->english_name
		)
	);
	?>
	</h4>
	<ul id="translation-links">
	<?php foreach ( $translation_sets as $translation_set ) : ?>
		<li id="translations_link_<?php echo esc_attr( $translation_set->translation_set_id ); ?>"><a href="#translations_<?php echo esc_attr( $translation_set->translation_set_id ); ?>"><?php echo esc_html( gp_project_names_from_root( $projects[ $translation_set->translation_set_id ] ) ); ?></a></li>
	<?php endforeach; ?>
	</ul>

	<?php
	if ( trim( $status, '/' ) !== 'waiting' ) {
		?>
		<a href="<?php echo esc_url( gp_url_join( gp_url(), wp_make_link_relative( get_the_permalink( $event->id() ) ), $locale->slug, 'waiting' ) ); ?>"><?php esc_html_e( 'Show only waiting translations', 'glotpress' ); ?></a>
		<?php
	} else {
		?>
		<a href="<?php echo esc_url( gp_url_join( gp_url(), wp_make_link_relative( get_the_permalink( $event->id() ) ), $locale->slug ) ); ?>"><?php esc_html_e( 'Show all contributed translations', 'glotpress' ); ?></a>
		<?php
	}
	?>
	<hr>
