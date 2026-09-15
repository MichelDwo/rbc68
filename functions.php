<?php
/**
 * Fonctions du thème RBC68.
 *
 * @package RBC68
 */

defined( 'ABSPATH' ) || exit;

define( 'RBC68_VERSION', '1.0.0' );

function rbc68_setup() {
	load_theme_textdomain( 'rbc68', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 121,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'rbc68_setup' );

function rbc68_assets() {
	wp_enqueue_style( 'rbc68-style', get_stylesheet_uri(), array(), RBC68_VERSION );
	wp_enqueue_script( 'rbc68-main', get_template_directory_uri() . '/assets/js/main.js', array(), RBC68_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'rbc68_assets' );

function rbc68_logo_url() {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$custom_logo = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $custom_logo ) {
			return $custom_logo;
		}
	}

	return get_template_directory_uri() . '/assets/images/logo-rbc68.png';
}

function rbc68_mod( $name, $default = '' ) {
	return get_theme_mod( $name, $default );
}

function rbc68_gallery_image( $number, $fallback ) {
	$image_id = absint( get_theme_mod( 'rbc68_gallery_' . $number ) );
	if ( $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $url ) {
			return $url;
		}
	}

	return $fallback;
}

function rbc68_placeholder_image( $label, $background, $foreground, $width, $height ) {
	$label      = htmlspecialchars( wp_strip_all_tags( $label ), ENT_QUOTES, 'UTF-8' );
	$background = sanitize_hex_color( $background );
	$foreground = sanitize_hex_color( $foreground );
	$width      = max( 1, absint( $width ) );
	$height     = max( 1, absint( $height ) );
	$svg        = sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d"><rect width="%1$d" height="%2$d" fill="%3$s"/><text x="50%%" y="50%%" text-anchor="middle" dominant-baseline="middle" font-family="Arial,sans-serif" font-size="20" fill="%4$s">%5$s</text></svg>',
		$width,
		$height,
		$background ?: '#e0f2fe',
		$foreground ?: '#0284c7',
		$label
	);

	return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode( $svg );
}

function rbc68_customizer( $customizer ) {
	$customizer->add_section(
		'rbc68_homepage',
		array(
			'title'       => __( 'Page d’accueil RBC68', 'rbc68' ),
			'description' => __( 'Réglages principaux du one-page.', 'rbc68' ),
			'priority'    => 30,
		)
	);

	$text_settings = array(
		'rbc68_hero_title' => array( 'Titre principal', 'Riedisheim Badminton Club' ),
		'rbc68_hero_text'  => array( 'Texte d’introduction', 'Découvrez la passion du badminton dans une ambiance conviviale et sportive. Ouvert à tous les niveaux, du débutant au compétiteur.' ),
		'rbc68_email'      => array( 'Adresse e-mail affichée et destinataire du formulaire', 'contact@rbc68.fr' ),
		'rbc68_address'    => array( 'Adresse du club', 'Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim' ),
		'rbc68_facebook'   => array( 'Lien Facebook', 'https://facebook.com/p/Riedisheim-Badminton-Club-100024060309053/' ),
		'rbc68_instagram'  => array( 'Lien Instagram', 'https://instagram.com/rbc68/' ),
	);

	foreach ( $text_settings as $setting => $details ) {
		$sanitize = 'sanitize_text_field';
		$type     = 'text';
		if ( 'rbc68_email' === $setting ) {
			$sanitize = 'sanitize_email';
			$type     = 'email';
		} elseif ( in_array( $setting, array( 'rbc68_facebook', 'rbc68_instagram' ), true ) ) {
			$sanitize = 'esc_url_raw';
			$type     = 'url';
		}

		$customizer->add_setting(
			$setting,
			array(
				'default'           => $details[1],
				'sanitize_callback' => $sanitize,
			)
		);
		$customizer->add_control(
			$setting,
			array(
				'label'   => __( $details[0], 'rbc68' ),
				'section' => 'rbc68_homepage',
				'type'    => $type,
			)
		);
	}

	foreach ( array( 1 => 'Première photo de la galerie', 2 => 'Seconde photo de la galerie' ) as $number => $label ) {
		$setting = 'rbc68_gallery_' . $number;
		$customizer->add_setting( $setting, array( 'sanitize_callback' => 'absint' ) );
		$customizer->add_control(
			new WP_Customize_Media_Control(
				$customizer,
				$setting,
				array(
					'label'     => __( $label, 'rbc68' ),
					'section'   => 'rbc68_homepage',
					'mime_type' => 'image',
				)
			)
		);
	}

	$customizer->add_setting(
		'rbc68_primary_color',
		array(
			'default'           => '#2563eb',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$customizer->add_control(
		new WP_Customize_Color_Control(
			$customizer,
			'rbc68_primary_color',
			array(
				'label'   => __( 'Couleur principale', 'rbc68' ),
				'section' => 'rbc68_homepage',
			)
		)
	);
}
add_action( 'customize_register', 'rbc68_customizer' );

function rbc68_custom_css() {
	$primary = sanitize_hex_color( get_theme_mod( 'rbc68_primary_color', '#2563eb' ) );
	if ( ! $primary ) {
		$primary = '#2563eb';
	}
	printf( '<style id="rbc68-custom-colors">:root{--primary:%1$s}</style>', esc_html( $primary ) );
}
add_action( 'wp_head', 'rbc68_custom_css' );

function rbc68_contact_redirect( $status ) {
	wp_safe_redirect( add_query_arg( 'contact', $status, home_url( '/' ) ) . '#contact' );
	exit;
}

function rbc68_handle_contact() {
	if ( ! isset( $_POST['rbc68_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rbc68_contact_nonce'] ) ), 'rbc68_contact' ) ) {
		rbc68_contact_redirect( 'error' );
	}

	if ( ! empty( $_POST['website'] ) ) {
		rbc68_contact_redirect( 'success' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_key( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$consent = isset( $_POST['consent'] );

	$subjects = array(
		'inscription'  => 'Inscription au club',
		'renseignement' => 'Demande de renseignement',
		'tournoi'       => 'Information sur les tournois',
		'autre'         => 'Autre',
	);

	if ( ! $name || ! is_email( $email ) || ! isset( $subjects[ $subject ] ) || strlen( $message ) < 10 || ! $consent ) {
		rbc68_contact_redirect( 'error' );
	}

	$recipient = sanitize_email( get_theme_mod( 'rbc68_email', 'contact@rbc68.fr' ) );
	if ( ! is_email( $recipient ) ) {
		$recipient = get_option( 'admin_email' );
	}

	$mail_subject = sprintf( '[RBC68] %s — %s', $subjects[ $subject ], $name );
	$mail_body    = "Nom : {$name}\nE-mail : {$email}\nTéléphone : {$phone}\nSujet : {$subjects[$subject]}\n\nMessage :\n{$message}\n";
	$headers      = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	rbc68_contact_redirect( wp_mail( $recipient, $mail_subject, $mail_body, $headers ) ? 'success' : 'error' );
}
add_action( 'admin_post_nopriv_rbc68_contact', 'rbc68_handle_contact' );
add_action( 'admin_post_rbc68_contact', 'rbc68_handle_contact' );
