<?php
/** Réglages de présentation : réseaux et partenaires. */
defined( 'ABSPATH' ) || exit;

function rbc68_presentation_customizer( $customizer ) {
	foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram' ) as $key => $label ) {
		$customizer->add_setting( 'rbc68_show_' . $key, array( 'default' => 'facebook' === $key, 'sanitize_callback' => 'rbc68_sanitize_checkbox' ) );
		$customizer->add_control( 'rbc68_show_' . $key, array( 'label' => 'Afficher ' . $label, 'section' => 'rbc68_homepage', 'type' => 'checkbox' ) );
	}
	$customizer->add_section( 'rbc68_sponsors', array( 'title' => 'Sponsors', 'description' => 'Logos affichés après Contact. Un emplacement sans logo reste masqué.', 'priority' => 36 ) );

}
add_action( 'customize_register', 'rbc68_presentation_customizer', 20 );

function rbc68_render_sponsors() {
	$items = array();
	foreach ( rbc68_sponsors() as $sponsor ) {
		$name = $sponsor['name'];
		$logo = wp_get_attachment_image( $sponsor['logo'], 'medium', false, array( 'alt' => $name ?: 'Sponsor du RBC68', 'loading' => 'lazy' ) );
		if ( ! $logo ) { continue; }
		$url = esc_url( $sponsor['url'] );
		$items[] = '<li>' . ( $url ? '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $logo . '</a>' : $logo ) . '</li>';
	}
	if ( $items ) {
		echo '<section id="sponsors" aria-labelledby="sponsors-title"><h2 id="sponsors-title" class="section-title">Nos Sponsors</h2><ul class="sponsor-logos">' . implode( '', $items ) . '</ul></section>';
	}
}

function rbc68_join_text_refresh( $value ) {
	return 'Nouvelles inscriptions sur place, auprès du responsable de salle. Reprise le 1er septembre 2026.' === $value ? 'Venez essayer nos créneaux adultes, raquettes mises à disposition.' : $value;
}
add_filter( 'theme_mod_rbc68_join_text', 'rbc68_join_text_refresh' );
