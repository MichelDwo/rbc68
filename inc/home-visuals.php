<?php
/** Fonds de page et indicateurs récents du menu. */
defined( 'ABSPATH' ) || exit;

function rbc68_event_in_notice_window( $date, DateTimeImmutable $now ) {
	$start = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, $now->getTimezone() );
	return $start && $start->format( 'Y-m-d' ) === $date && $start >= $now->setTime( 0, 0 ) && $start <= $now->modify( '+30 days' )->setTime( 0, 0 );
}

function rbc68_menu_has_events() {
	$now = new DateTimeImmutable( 'now', wp_timezone() );
	foreach ( rbc68_get_upcoming_events( PHP_INT_MAX ) as $event ) {
		if ( ! post_password_required( $event ) && rbc68_event_in_notice_window( get_post_meta( $event->ID, 'rbc68_event_date', true ), $now ) ) { return true; }
	}
	return false;
}

function rbc68_menu_has_news() {
	$now = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	$query = new WP_Query( array(
		'post_type' => 'post', 'post_status' => 'publish', 'has_password' => false,
		'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true, 'ignore_sticky_posts' => true,
		'date_query' => array(
			array( 'column' => 'post_date_gmt', 'after' => $now->modify( '-30 days' )->format( 'Y-m-d H:i:s' ), 'inclusive' => false ),
			array( 'column' => 'post_date_gmt', 'before' => $now->format( 'Y-m-d H:i:s' ), 'inclusive' => true ),
		),
	) );
	return (bool) $query->posts;
}

function rbc68_news_notice() {
	echo '<span class="nav-shuttle"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M5 2h14l-4 14H9L5 2Z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="m8 2 2 14m2-14v14m4-14-2 14M8 12h8" fill="none" stroke="currentColor" stroke-width="1.3"/><path d="M9 17h6v2a3 3 0 0 1-6 0v-2Z" fill="currentColor"/></svg><span class="screen-reader-text"> — actualité publiée depuis moins de 30 jours</span></span>';
}

function rbc68_background_customizer( $customizer ) {
	$customizer->add_section( 'rbc68_backgrounds', array( 'title' => 'Photos de fond', 'description' => 'Une à trois photos dans le fond de toute la page d’accueil. Les photos du bandeau et du club sont reprises si aucun fond spécifique n’est choisi. Le voile et les cartes protègent la lisibilité.', 'priority' => 32 ) );
	$customizer->add_setting( 'rbc68_background_enabled', array( 'default' => true, 'sanitize_callback' => 'rbc68_sanitize_checkbox' ) );
	$customizer->add_control( 'rbc68_background_enabled', array( 'section' => 'rbc68_backgrounds', 'type' => 'checkbox', 'label' => 'Afficher les photos de fond' ) );
	for ( $i = 1; $i <= 3; $i++ ) {
		$key = 'rbc68_background_' . $i;
		$customizer->add_setting( $key, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$customizer->add_control( new WP_Customize_Media_Control( $customizer, $key, array( 'section' => 'rbc68_backgrounds', 'label' => 'Photo de fond ' . $i, 'mime_type' => 'image' ) ) );
	}
}
add_action( 'customize_register', 'rbc68_background_customizer' );

function rbc68_home_backgrounds() {
	if ( ! get_theme_mod( 'rbc68_background_enabled', true ) ) { return array(); }
	$ids = array();
	for ( $i = 1; $i <= 3; $i++ ) { $ids[] = absint( get_theme_mod( 'rbc68_background_' . $i, 0 ) ); }
	$ids = array_filter( $ids, 'wp_attachment_is_image' );
	if ( ! $ids ) {
		$ids = array_filter( array( absint( get_theme_mod( 'rbc68_hero_image' ) ), absint( get_theme_mod( 'rbc68_club_image' ) ) ), 'wp_attachment_is_image' );
	}
	return array_values( array_unique( $ids ) );
}

function rbc68_render_home_backgrounds( $ids ) {
	if ( ! $ids ) { return; }
	echo '<div class="home-backdrop" aria-hidden="true">';
	foreach ( $ids as $index => $id ) {
		echo '<div class="home-photo-band"><div class="home-photo-frame">';
		echo wp_get_attachment_image( $id, 'full', false, array( 'alt' => '', 'sizes' => '100vw', 'loading' => 0 === $index ? 'eager' : 'lazy', 'decoding' => 'async' ) );
		echo '</div></div>';
	}
	echo '</div>';
}
