<?php
/** Réglages de présentation : réseaux et partenaires. */
defined( 'ABSPATH' ) || exit;

function rbc68_presentation_customizer( $customizer ) {
	foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram' ) as $key => $label ) {
		$customizer->add_setting( 'rbc68_show_' . $key, array( 'default' => 'facebook' === $key, 'sanitize_callback' => 'rbc68_sanitize_checkbox' ) );
		$customizer->add_control( 'rbc68_show_' . $key, array( 'label' => 'Afficher ' . $label, 'section' => 'rbc68_homepage', 'type' => 'checkbox' ) );
	}
	$customizer->add_section( 'rbc68_sponsors', array( 'title' => 'Sponsors', 'description' => 'Logos affichés après Contact. Un emplacement sans logo reste masqué.', 'priority' => 36 ) );
	for ( $i = 1; $i <= 2; $i++ ) {
		$prefix = 'rbc68_sponsor_' . $i;
		foreach ( array( 'name' => 'Nom', 'url' => 'Site Internet' ) as $field => $label ) {
			$customizer->add_setting( $prefix . '_' . $field, array( 'default' => '', 'sanitize_callback' => 'url' === $field ? 'esc_url_raw' : 'sanitize_text_field' ) );
			$customizer->add_control( $prefix . '_' . $field, array( 'label' => $label . ' du sponsor ' . $i, 'section' => 'rbc68_sponsors', 'type' => 'url' === $field ? 'url' : 'text' ) );
		}
		$customizer->add_setting( $prefix . '_logo', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$customizer->add_control( new WP_Customize_Media_Control( $customizer, $prefix . '_logo', array( 'label' => 'Logo du sponsor ' . $i, 'section' => 'rbc68_sponsors', 'mime_type' => 'image' ) ) );
	}
}
add_action( 'customize_register', 'rbc68_presentation_customizer', 20 );

function rbc68_render_sponsors() {
	$items = array();
	for ( $i = 1; $i <= 2; $i++ ) {
		$prefix = 'rbc68_sponsor_' . $i;
		$name = get_theme_mod( $prefix . '_name', '' );
		$logo = wp_get_attachment_image( absint( get_theme_mod( $prefix . '_logo', 0 ) ), 'medium', false, array( 'alt' => $name ?: 'Sponsor du RBC68', 'loading' => 'lazy' ) );
		if ( ! $logo ) { continue; }
		$url = esc_url( get_theme_mod( $prefix . '_url', '' ) );
		$items[] = '<li>' . ( $url ? '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $logo . '</a>' : $logo ) . '</li>';
	}
	if ( $items ) {
		echo '<section id="sponsors" aria-labelledby="sponsors-title"><h2 id="sponsors-title" class="section-title">Nos sponsors</h2><ul class="sponsor-logos">' . implode( '', $items ) . '</ul></section>';
	}
}

function rbc68_join_text_refresh( $value ) {
	return 'Nouvelles inscriptions sur place, auprès du responsable de salle. Reprise le 1er septembre 2026.' === $value ? 'Venez essayer nos créneaux adultes, raquettes mises à disposition.' : $value;
}
add_filter( 'theme_mod_rbc68_join_text', 'rbc68_join_text_refresh' );
