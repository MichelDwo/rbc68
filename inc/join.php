<?php
/** Parcours d’inscription et réglages du club. */
defined( 'ABSPATH' ) || exit;

function rbc68_join_defaults() {
	$base = 'https://frontwebservice.ffbad.org/storage/documents/pages/Pratiquer/Se_licencier/Licence/';
	$minor = array(
		array( 'label' => 'Bulletin de licence — mineur', 'url' => $base . 'ffbad-2026-2027-formulaire-de-prise-de-licence-mineur-a4-05.pdf' ),
		array( 'label' => 'Questionnaire de santé et attestation — mineur', 'url' => 'https://echange.ffbad.org/index.php/s/fLDrPAzzi3pmNbw#pdfviewer' ),
		array( 'label' => 'Certificat médical — si nécessaire', 'url' => $base . 'Certificat%20m%C3%A9dical%20de%20non%20contre-Indication.pdf' ),
	);
	$adult = $minor;
	$adult[0] = array( 'label' => 'Bulletin de licence — adulte', 'url' => $base . 'ffbad-2026-2027-formulaire-de-prise-de-licence-adulte-a4-07.pdf' );
	$adult[1] = array( 'label' => 'Questionnaire de santé et attestation — adulte', 'url' => 'https://echange.ffbad.org/index.php/s/gwxR3oAB8JMWQnc#pdfviewer' );
	return array(
		'season' => '2026-2027', 'previous' => '2025-2026', 'short_season' => '2026-27',
		'intro' => 'Deux séances d’essai gratuites avant de vous décider.',
		'summary' => 'Première inscription ou renouvellement : choisissez votre parcours ci-dessous.',
		'deadline' => 'mercredi 30 septembre 2026',
		'renew_url' => 'https://www.myffbad.fr/adherer/RBC68',
		'renew_text' => 'Complétez toutes les informations directement en ligne et réglez votre cotisation par carte bancaire ou virement.',
		'licence_help' => "Sur MyFFBaD, utilisez la barre de recherche en haut du site.\nSaisissez votre nom de famille, éventuellement votre prénom.\nSélectionnez votre fiche joueur.\nVotre numéro de licence FFBaD figure sur votre profil.",
		'new_text' => 'Choisissez la tranche d’âge du joueur pour retrouver les démarches et les documents qui le concernent.',
		'payment' => 'Par chèque à l’ordre du Riedisheim Badminton Club ou par virement bancaire.',
		'reference' => 'NOM/Prénom {saison_courte}',
		'transfer_note' => 'En cas de virement, précisez-le sur le bulletin d’inscription.',
		'rib_url' => '', 'rib_note' => 'RIB disponible auprès du responsable du créneau.',
		'handover' => 'Remettez le dossier complet au responsable de votre créneau.',
		'documents_text' => 'Complétez lisiblement le bulletin de licence et préparez les pièces correspondant à votre situation.',
		'medical' => 'Remplissez le questionnaire de santé. Si toutes les réponses sont négatives, remettez uniquement l’attestation complétée et signée. Si vous avez répondu « oui » à une question, faites compléter le certificat médical par votre médecin.',
		'renew_docs' => array(),
		'groups' => array(
			'mini' => array( 'label' => 'Mini-bad', 'ages' => 'Nés en 2019 et après', 'price' => get_theme_mod( 'rbc68_price_mini', '70 €' ), 'note' => '', 'docs' => $minor ),
			'youth' => array( 'label' => 'Jeunes', 'ages' => 'Nés de 2009 à 2018', 'price' => get_theme_mod( 'rbc68_price_youth', '85 €' ), 'note' => '', 'docs' => $minor ),
			'adults' => array( 'label' => 'Adultes', 'ages' => 'Nés avant 2009', 'price' => get_theme_mod( 'rbc68_price_adults', '105 €' ), 'note' => '', 'docs' => $adult ),
		),
	);
}
function rbc68_join_settings() {
	$defaults = rbc68_join_defaults();
	$saved = get_option( 'rbc68_join_v2', array() );
	if ( ! is_array( $saved ) ) { return $defaults; }
	$result = array_merge( $defaults, $saved );
	foreach ( $defaults['groups'] as $key => $group ) {
		$result['groups'][$key] = array_merge( $group, $saved['groups'][$key] ?? array() );
	}
	return $result;
}
function rbc68_join_price( $key ) { return rbc68_join_settings()['groups'][$key]['price']; }
function rbc68_join_clean_docs( $docs ) {
	$result = array();
	foreach ( is_array( $docs ) ? $docs : array() as $doc ) {
		if ( ! is_array( $doc ) ) { continue; }
		$label = isset( $doc['label'] ) && is_string( $doc['label'] ) ? sanitize_text_field( $doc['label'] ) : '';
		$url = isset( $doc['url'] ) && is_string( $doc['url'] ) ? esc_url_raw( $doc['url'], array( 'https', 'http' ) ) : '';
		if ( $label && $url ) { $result[] = array( 'label' => $label, 'url' => $url ); }
	}
	return $result;
}
function rbc68_join_sanitize( $input ) {
	$old = rbc68_join_settings();
	if ( ! is_array( $input ) ) { return $old; }
	foreach ( rbc68_join_defaults() as $key => $default ) {
		if ( is_array( $default ) || ! isset( $input[$key] ) || ! is_string( $input[$key] ) ) { continue; }
		$old[$key] = substr( $key, -4 ) === '_url' ? esc_url_raw( $input[$key], array( 'https', 'http' ) ) : sanitize_textarea_field( $input[$key] );
	}
	$old['renew_docs'] = rbc68_join_clean_docs( $input['renew_docs'] ?? array() );
	foreach ( $old['groups'] as $key => $group ) {
		foreach ( array( 'label', 'ages', 'price', 'note' ) as $field ) {
			if ( isset( $input['groups'][$key][$field] ) && is_string( $input['groups'][$key][$field] ) ) {
				$old['groups'][$key][$field] = sanitize_textarea_field( $input['groups'][$key][$field] );
			}
		}
		$old['groups'][$key]['docs'] = rbc68_join_clean_docs( $input['groups'][$key]['docs'] ?? array() );
	}
	return $old;
}
add_action( 'admin_init', function() {
	register_setting( 'rbc68_join', 'rbc68_join_v2', array( 'sanitize_callback' => 'rbc68_join_sanitize', 'type' => 'array' ) );
} );
add_action( 'admin_menu', function() {
	add_theme_page( 'Nous rejoindre', 'Nous rejoindre', 'manage_options', 'rbc68-join', 'rbc68_join_admin' );
} );
add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( 'appearance_page_rbc68-join' !== $hook ) { return; }
	wp_enqueue_media();
	wp_enqueue_script( 'rbc68-join-admin', get_template_directory_uri() . '/assets/js/join-admin.js', array( 'media-views' ), RBC68_VERSION, true );
} );
function rbc68_join_admin_field( $name, $label, $value, $type = 'text' ) {
	$id = 'join-' . sanitize_key( $name );
	echo '<p><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
	if ( 'textarea' === $type ) {
		echo '<textarea class="large-text" rows="3" id="' . esc_attr( $id ) . '" name="rbc68_join_v2[' . esc_attr( $name ) . ']">' . esc_textarea( $value ) . '</textarea>';
	} else {
		echo '<input class="large-text" id="' . esc_attr( $id ) . '" type="' . esc_attr( $type ) . '" name="rbc68_join_v2[' . esc_attr( $name ) . ']" value="' . esc_attr( $value ) . '">';
	}
	echo '</p>';
}
function rbc68_join_doc_row( $prefix, $index, $doc ) {
	?>
	<div class="join-doc-row" style="border-left:3px solid #ccc;padding:8px 12px;margin:10px 0">
		<label>Nom du document <input class="large-text" name="<?php echo esc_attr( $prefix . '[' . $index . '][label]' ); ?>" value="<?php echo esc_attr( $doc['label'] ); ?>"></label>
		<label>Lien du document <input class="large-text" type="url" data-document-url name="<?php echo esc_attr( $prefix . '[' . $index . '][url]' ); ?>" value="<?php echo esc_attr( $doc['url'] ); ?>"></label>
		<button type="button" class="button" data-select-document>Choisir dans la médiathèque</button>
		<button type="button" class="button" data-remove-document>Retirer</button>
	</div>
	<?php
}
function rbc68_join_admin_docs( $name, $docs ) {
	$prefix = 'rbc68_join_v2[' . $name . ']'; ?>
	<div data-documents data-next="<?php echo count( $docs ); ?>">
		<h3>Documents de ce parcours</h3><p>Ajoutez un lien externe ou choisissez un fichier. Pour retirer un document, utilisez « Retirer », puis enregistrez.</p>
		<div data-document-rows><?php foreach ( $docs as $i => $doc ) { rbc68_join_doc_row( $prefix, $i, $doc ); } ?></div>
		<template><?php rbc68_join_doc_row( $prefix, '__INDEX__', array( 'label' => '', 'url' => '' ) ); ?></template>
		<button type="button" class="button" data-add-document>Ajouter un document</button>
		<p data-document-status role="status"></p>
	</div>
	<?php
}
function rbc68_join_admin() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$s = rbc68_join_settings(); ?>
	<div class="wrap" style="max-width:950px"><h1>Nous rejoindre</h1>
	<p>Contenu du parcours d’inscription de l’accueil. À chaque saison, vérifiez les années, la date limite, les tarifs et les documents. Les tarifs alimentent aussi « Infos pratiques ».</p>
	<?php settings_errors(); ?>
	<form action="options.php" method="post"><?php settings_fields( 'rbc68_join' ); ?>
	<h2>Saison et accueil</h2>
	<?php foreach ( array( 'season' => 'Saison actuelle', 'previous' => 'Saison précédente', 'short_season' => 'Saison abrégée', 'deadline' => 'Date limite de remise des dossiers', 'intro' => 'Introduction / séances d’essai', 'summary' => 'Résumé dans Infos pratiques' ) as $key => $label ) { rbc68_join_admin_field( $key, $label, $s[$key] ); } ?>
	<h2>Renouvellement</h2>
	<?php rbc68_join_admin_field( 'renew_url', 'Lien MyFFBaD du club', $s['renew_url'], 'url' );
	rbc68_join_admin_field( 'renew_text', 'Démarches et paiement en ligne', $s['renew_text'], 'textarea' );
	rbc68_join_admin_field( 'licence_help', 'Retrouver son numéro de licence — une étape par ligne', $s['licence_help'], 'textarea' );
	rbc68_join_admin_docs( 'renew_docs', $s['renew_docs'] ); ?>
	<h2>Première inscription — consignes communes</h2>
	<?php foreach ( array( 'new_text' => 'Introduction du choix d’âge', 'payment' => 'Moyens de paiement', 'reference' => 'Libellé du virement — {saison} ou {saison_courte}', 'transfer_note' => 'Consigne pour le virement', 'handover' => 'Remise du dossier', 'documents_text' => 'Consigne pour le bulletin de licence', 'medical' => 'Consignes pour le questionnaire et le certificat médical', 'rib_note' => 'Texte affiché si aucun RIB n’est sélectionné' ) as $key => $label ) { rbc68_join_admin_field( $key, $label, $s[$key], 'textarea' ); } ?>
	<div class="join-doc-row"><?php rbc68_join_admin_field( 'rib_url', 'RIB — lien commun aux trois groupes', $s['rib_url'], 'url' ); ?><button type="button" class="button" data-select-document>Choisir le RIB dans la médiathèque</button></div>
	<?php foreach ( $s['groups'] as $key => $group ) : ?><hr><h2><?php echo esc_html( $group['label'] ); ?></h2>
	<?php foreach ( array( 'label' => 'Nom du groupe', 'ages' => 'Années de naissance', 'price' => 'Tarif (avec unité, ex. 70 €)', 'note' => 'Précisions propres à ce groupe (facultatif)' ) as $field => $label ) { rbc68_join_admin_field( 'groups][' . $key . '][' . $field, $label, $group[$field], 'note' === $field ? 'textarea' : 'text' ); }
	rbc68_join_admin_docs( 'groups][' . $key . '][docs', $group['docs'] ); ?>
	<?php endforeach; ?>
	<?php submit_button(); ?></form></div>
	<?php
}
