<?php
/** php tests/join.php — parcours et réglages, API WordPress simulée. */
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
function get_theme_mod( $key, $default = '' ) { return $key === 'rbc68_price_youth' ? '87 €' : $default; }
function get_option( $key, $default = array() ) { return $GLOBALS['saved'] ?? $default; }
function sanitize_textarea_field( $v ) { return strip_tags( $v ); }
function sanitize_text_field( $v ) { return trim( strip_tags( $v ) ); }
function sanitize_key( $v ) { return preg_replace( '/[^a-z0-9_-]/', '', $v ); }
function esc_url_raw( $v, $protocols = array() ) { return preg_match( '~^https?://~', $v ) ? $v : ''; }
function esc_html( $v ) { return htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $v ) { return esc_html( $v ); }
function esc_textarea( $v ) { return esc_html( $v ); }
function esc_url( $v ) { return esc_html( esc_url_raw( $v ) ); }
function rbc68_availability_label( $key ) { return $key === 'youth' ? 'Complet' : ''; }
require __DIR__ . '/../inc/join.php';
function check( $v, $message ) { if ( ! $v ) { throw new RuntimeException( $message ); } }
check( rbc68_join_price( 'youth' ) === '87 €', 'Tarif existant conservé au premier affichage' );
$settings = rbc68_join_defaults();
$settings['season'] = '2027-2028'; $settings['short_season'] = '2027-28';
$settings['groups']['mini']['price'] = '75 €';
$settings['groups']['mini']['docs'] = array();
$settings['groups']['adults']['docs'][] = array( 'label' => 'Pièce supplémentaire', 'url' => 'https://example.org/extra.pdf' );
$settings['groups']['adults']['docs'][] = array( 'label' => 'Mauvais lien', 'url' => 'javascript:alert(1)' );
$settings['groups']['adults']['docs'][] = array( 'label' => '', 'url' => 'https://example.org/unnamed.pdf' );
$settings['new_text'] = '<b>Choisissez votre âge</b>';
$GLOBALS['saved'] = rbc68_join_sanitize( $settings );
check( rbc68_join_price( 'mini' ) === '75 €', 'Tarif partagé après sauvegarde' );
check( count( rbc68_join_settings()['groups']['mini']['docs'] ) === 0, 'Les documents retirés ne réapparaissent pas' );
check( count( rbc68_join_settings()['groups']['youth']['docs'] ) === 3, 'Groupes indépendants' );
check( count( rbc68_join_settings()['groups']['adults']['docs'] ) === 4, 'Ajout et filtrage des liens' );
check( rbc68_join_settings()['new_text'] === 'Choisissez votre âge', 'Textes nettoyés' );
ob_start(); rbc68_join_admin_field( 'groups][mini][ages', 'Années', '2019 et après' ); $field = ob_get_clean();
check( strpos( $field, 'name="rbc68_join_v2[groups][mini][ages]"' ) !== false, 'Nom du champ de groupe' );
ob_start(); rbc68_join_admin_docs( 'groups][mini][docs', array() ); $docs = ob_get_clean();
check( strpos( $docs, 'rbc68_join_v2[groups][mini][docs][__INDEX__][url]' ) !== false, 'Champs de documents dynamiques' );
ob_start(); require __DIR__ . '/../template-parts/join.php'; $html = ob_get_clean();
check( strpos( $html, 'NOM/Prénom 2027-28' ) !== false, 'Saison dans le virement' );
check( strpos( $html, 'Complet' ) !== false, 'Disponibilités conservées' );
check( strpos( $html, 'myffbad.fr/adherer/RBC68' ) !== false && strpos( $html, 'mercredi 30 septembre 2026' ) !== false, 'Lien et date fournis' );
if ( getenv( 'RBC68_RENDER' ) ) {
	$GLOBALS['saved'] = null;
	ob_start(); require __DIR__ . '/../template-parts/join.php'; echo ob_get_clean();
} else { echo "OK — tarifs, saisons, documents indépendants, retrait, formulaires et rendu\n"; }
