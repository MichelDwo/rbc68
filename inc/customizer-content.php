<?php
/** Édition des listes de contenus dans Personnaliser. */
defined( 'ABSPATH' ) || exit;

function rbc68_clean_rows( $value ) {
	if ( is_string( $value ) ) { $value = json_decode( $value, true ); }
	return is_array( $value ) ? array_values( array_filter( $value, 'is_array' ) ) : array();
}
function rbc68_sanitize_sponsors( $value ) {
	$result = array();
	foreach ( rbc68_clean_rows( $value ) as $row ) {
		$result[] = array( 'name' => sanitize_text_field( is_string( $row['name'] ?? null ) ? $row['name'] : '' ), 'url' => esc_url_raw( is_string( $row['url'] ?? null ) ? $row['url'] : '', array( 'http', 'https' ) ), 'logo' => is_scalar( $row['logo'] ?? null ) ? absint( $row['logo'] ) : 0 );
	}
	return $result;
}
function rbc68_sponsors() {
	$saved = get_theme_mod( 'rbc68_sponsors_list', null );
	if ( null !== $saved ) { return rbc68_sanitize_sponsors( $saved ); }
	$rows = array();
	for ( $i = 1; $i <= 2; $i++ ) {
		$rows[] = array( 'name' => get_theme_mod( "rbc68_sponsor_{$i}_name", '' ), 'url' => get_theme_mod( "rbc68_sponsor_{$i}_url", '' ), 'logo' => get_theme_mod( "rbc68_sponsor_{$i}_logo", 0 ) );
	}
	return rbc68_sanitize_sponsors( $rows );
}
function rbc68_sanitize_routes( $value ) {
	$result = array();
	foreach ( rbc68_clean_rows( $value ) as $row ) {
		$label = sanitize_text_field( is_string( $row['label'] ?? null ) ? $row['label'] : '' );
		$emails = array();
		foreach ( preg_split( '/[,;\s]+/', is_string( $row['emails'] ?? null ) ? $row['emails'] : '' ) as $email ) {
			if ( is_email( $email ) ) { $emails[] = sanitize_email( $email ); }
		}
		if ( $label ) { $result[] = array( 'label' => $label, 'emails' => implode( ', ', array_unique( $emails ) ) ); }
	}
	return $result;
}
function rbc68_contact_routes() {
	$defaults = array();
	foreach ( array( 'Nous rejoindre', 'Demande de renseignement', 'Information sur les tournois', 'Autre' ) as $label ) { $defaults[] = array( 'label' => $label, 'emails' => '' ); }
	$routes = rbc68_sanitize_routes( get_theme_mod( 'rbc68_contact_routes', $defaults ) );
	$result = array();
	foreach ( $routes as $row ) { $result[ 'sujet-' . substr( hash( 'sha256', $row['label'] ), 0, 16 ) ] = $row; }
	return $result;
}
function rbc68_sanitize_documents( $value ) { return rbc68_join_clean_docs( rbc68_clean_rows( $value ) ); }

add_action( 'customize_register', function( $c ) {
	class RBC68_List_Control extends WP_Customize_Control {
		public $fields = array();
		public $type = 'rbc68-list';
		public function render_content() {
			echo '<span class="customize-control-title">' . esc_html( $this->label ) . '</span><p>' . esc_html( $this->description ) . '</p>';
			echo '<div class="rbc68-list-control" data-fields="' . esc_attr( wp_json_encode( $this->fields ) ) . '"><div class="rbc68-list-rows"></div><button type="button" class="button rbc68-list-add">Ajouter</button></div>';
		}
	}
	$c->add_setting( 'rbc68_sponsors_list', array( 'default' => rbc68_sponsors(), 'sanitize_callback' => 'rbc68_sanitize_sponsors' ) );
	$c->add_control( new RBC68_List_Control( $c, 'rbc68_sponsors_list', array( 'section' => 'rbc68_sponsors', 'label' => 'Sponsors', 'fields' => array( 'name' => 'Nom', 'url' => 'Lien', 'logo' => 'Image' ) ) ) );
	$c->add_section( 'rbc68_contact_settings', array( 'title' => 'Contact', 'priority' => 37 ) );
	$c->add_setting( 'rbc68_contact_routes', array( 'default' => array_values( rbc68_contact_routes() ), 'sanitize_callback' => 'rbc68_sanitize_routes' ) );
	$c->add_control( new RBC68_List_Control( $c, 'rbc68_contact_routes', array( 'section' => 'rbc68_contact_settings', 'label' => 'Sujets et destinataires', 'description' => 'Une ou plusieurs adresses séparées par des virgules. Sans adresse valide, le message est envoyé à l’adresse générale du club.', 'fields' => array( 'label' => 'Sujet', 'emails' => 'Adresses e-mail' ) ) ) );
	$c->add_panel( 'rbc68_join_panel', array( 'title' => 'Nous rejoindre', 'priority' => 35 ) );
	$s = rbc68_join_settings();
	$labels = array( 'season'=>'Saison actuelle', 'previous'=>'Saison précédente', 'short_season'=>'Saison abrégée', 'intro'=>'Introduction / séances d’essai', 'summary'=>'Résumé dans Infos pratiques', 'deadline'=>'Date limite', 'renew_url'=>'Lien de renouvellement', 'renew_text'=>'Démarches de renouvellement', 'licence_help'=>'Retrouver sa licence — une étape par ligne', 'new_text'=>'Première inscription', 'payment'=>'Moyens de paiement', 'reference'=>'Libellé du virement', 'transfer_note'=>'Consigne de virement', 'rib_url'=>'Lien du RIB', 'rib_note'=>'Texte sans RIB', 'handover'=>'Remise du dossier', 'documents_text'=>'Consignes pour les documents', 'medical'=>'Consignes médicales' );
	$c->add_section( 'rbc68_join_general', array( 'title' => 'Consignes et renouvellement', 'panel' => 'rbc68_join_panel' ) );
	foreach ( $labels as $key => $label ) {
		$id = 'rbc68_join_v2[' . $key . ']';
		$c->add_setting( $id, array( 'type'=>'option', 'capability'=>'manage_options', 'default'=>$s[$key], 'sanitize_callback'=> '_url' === substr( $key, -4 ) ? 'esc_url_raw' : 'sanitize_textarea_field' ) );
		$c->add_control( $id, array( 'section'=>'rbc68_join_general', 'label'=>$label, 'type'=>'_url' === substr( $key, -4 ) ? 'url' : 'textarea' ) );
	}
	$docs = array( 'renew_docs' => array( 'rbc68_join_general', $s['renew_docs'] ) );
	foreach ( $s['groups'] as $key => $group ) {
		$section = 'rbc68_join_' . $key;
		$c->add_section( $section, array( 'title'=>$group['label'], 'panel'=>'rbc68_join_panel' ) );
		foreach ( array( 'label'=>'Nom du groupe', 'ages'=>'Années de naissance', 'price'=>'Tarif', 'note'=>'Précisions' ) as $field=>$label ) {
			$id = "rbc68_join_v2[groups][$key][$field]";
			$c->add_setting( $id, array( 'type'=>'option', 'capability'=>'manage_options', 'default'=>$group[$field], 'sanitize_callback'=>'sanitize_textarea_field' ) );
			$c->add_control( $id, array( 'section'=>$section, 'label'=>$label, 'type'=>'textarea' ) );
		}
		$docs[ "groups][$key][docs" ] = array( $section, $group['docs'] );
	}
	foreach ( $docs as $key=>$info ) {
		$id = "rbc68_join_v2[$key]";
		$c->add_setting( $id, array( 'type'=>'option', 'capability'=>'manage_options', 'default'=>$info[1], 'sanitize_callback'=>'rbc68_sanitize_documents' ) );
		$c->add_control( new RBC68_List_Control( $c, $id, array( 'section'=>$info[0], 'label'=>'Documents', 'fields'=>array( 'label'=>'Nom du document', 'url'=>'Lien du document' ) ) ) );
	}
}, 30 );
add_action( 'customize_controls_enqueue_scripts', function() {
	wp_enqueue_media();
	wp_enqueue_script( 'rbc68-content-controls', get_template_directory_uri() . '/assets/js/content-controls.js', array( 'jquery', 'customize-controls', 'media-views' ), RBC68_VERSION . '.' . filemtime( get_template_directory() . '/assets/js/content-controls.js' ), true );
} );
