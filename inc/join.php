<?php
/** Inscription : une source éditable pour l’accueil. */
defined( 'ABSPATH' ) || exit;

function rbc68_join_fields() {
	return array(
		'title' => array( 'Titre du bloc', 'Nous rejoindre', 'text' ),
		'season' => array( 'Saison', '2026-2027', 'text' ),
		'intro' => array( 'Introduction', 'Deux séances d’essai gratuites avant de vous décider.', 'textarea' ),
		'new_title' => array( 'Titre — première inscription', 'Première inscription au RBC ?', 'text' ),
		'new_text' => array( 'Première inscription — démarches', 'Venez essayer sur un créneau adapté à votre âge. Adressez-vous au responsable de salle au début ou à la fin de la séance pour préparer votre inscription.', 'textarea' ),
		'renew_title' => array( 'Titre — renouvellement', 'Déjà membre du RBC ?', 'text' ),
		'renew_text' => array( 'Renouvellement — démarches', 'Renouvelez votre licence sur la plateforme de la Fédération, puis remettez les documents demandés et réglez votre cotisation.', 'textarea' ),
		'renew_url' => array( 'Lien de renouvellement', 'https://licence.ffbad.org/', 'url' ),
		'renew_button' => array( 'Libellé du bouton de renouvellement', 'Renouveler ma licence', 'text' ),
		'dates' => array( 'Dates utiles (facultatif)', 'Reprise : 1er septembre 2026. Inscriptions jusqu’au 25 septembre 2026.', 'textarea' ),
		'payment' => array( 'Paiement', 'Par chèque à l’ordre du Riedisheim Badminton Club ou par virement. Le RIB est disponible auprès des responsables du club.', 'textarea' ),
		'reference' => array( 'Libellé du virement ({saison} est remplacé automatiquement)', 'Inscription {saison} — Nom et prénom du joueur', 'text' ),
		'documents_intro' => array( 'Consignes pour les documents', 'Préparez les documents correspondant à votre situation. Le responsable de salle vous précisera les pièces nécessaires et les modalités de remise.', 'textarea' ),
	);
}

function rbc68_join_value( $key ) {
	$fields = rbc68_join_fields();
	return get_theme_mod( 'rbc68_join_' . $key, $fields[ $key ][1] );
}

function rbc68_join_customize( $customizer ) {
	$customizer->add_section( 'rbc68_join', array(
		'title' => 'Nous rejoindre — inscriptions', 'priority' => 32,
		'description' => 'Textes, saison et documents de l’accueil. Les tarifs et disponibilités se règlent dans Informations pratiques et sont partagés avec ce bloc. Un texte vide masque la mention correspondante. Choisissez les documents dans la médiathèque ; seuls les fichiers sélectionnés apparaissent.',
	) );
	foreach ( rbc68_join_fields() as $key => $field ) {
		$id = 'rbc68_join_' . $key;
		$sanitize = 'url' === $field[2] ? 'esc_url_raw' : ( 'textarea' === $field[2] ? 'sanitize_textarea_field' : 'sanitize_text_field' );
		$customizer->add_setting( $id, array( 'default' => $field[1], 'sanitize_callback' => $sanitize ) );
		$customizer->add_control( $id, array( 'label' => $field[0], 'section' => 'rbc68_join', 'type' => $field[2] ) );
	}
	for ( $i = 1; $i <= 8; $i++ ) {
		$id = 'rbc68_join_doc_' . $i;
		$customizer->add_setting( $id, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$customizer->add_control( new WP_Customize_Media_Control( $customizer, $id, array( 'label' => 'Document ' . $i, 'section' => 'rbc68_join' ) ) );
		foreach ( array( 'label' => 'Nom du document', 'note' => 'Pour qui / consigne (facultatif)' ) as $suffix => $label ) {
			$customizer->add_setting( $id . '_' . $suffix, array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
			$customizer->add_control( $id . '_' . $suffix, array( 'label' => $label . ' ' . $i, 'section' => 'rbc68_join', 'type' => 'text' ) );
		}
	}
}
add_action( 'customize_register', 'rbc68_join_customize' );

function rbc68_join_documents() {
	$documents = array();
	for ( $i = 1; $i <= 8; $i++ ) {
		$key = 'rbc68_join_doc_' . $i;
		$id = absint( get_theme_mod( $key, 0 ) );
		$url = $id ? wp_get_attachment_url( $id ) : false;
		if ( ! $url || 'attachment' !== get_post_type( $id ) || 'trash' === get_post_status( $id ) ) {
			continue;
		}
		$label = get_theme_mod( $key . '_label', '' );
		$documents[] = array( 'url' => $url, 'label' => $label ? $label : get_the_title( $id ), 'note' => get_theme_mod( $key . '_note', '' ) );
	}
	return $documents;
}

/** Conserver les anciens liens sans afficher une seconde procédure. */
function rbc68_join_redirect() {
	if ( is_page( 'nous-rejoindre' ) && ! is_front_page() && ! is_preview() && ! is_customize_preview() ) {
		wp_safe_redirect( home_url( '/#inscription' ), 302 );
		exit;
	}
}
add_action( 'template_redirect', 'rbc68_join_redirect' );
