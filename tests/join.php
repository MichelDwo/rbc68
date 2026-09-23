<?php
/** php tests/join.php — rendu de l’inscription avec API WordPress simulée. */
define( 'ABSPATH', __DIR__ );
$mods = array();
function add_action( ...$args ) {}
function get_theme_mod( $key, $default = '' ) { return $GLOBALS['mods'][$key] ?? $default; }
function rbc68_mod( $key, $default = '' ) { return get_theme_mod( $key, $default ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_get_attachment_url( $id ) { return $id === 9 ? false : 'https://example.org/document.pdf'; }
function get_post_type( $id ) { return $id === 8 ? 'post' : 'attachment'; }
function get_post_status( $id ) { return $id === 7 ? 'trash' : 'inherit'; }
function get_the_title( $id ) { return 'Formulaire'; }
function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return esc_html( $value ); }
function rbc68_availability_label( $key ) { return $key === 'youth' ? 'Complet' : ''; }
require __DIR__ . '/../inc/join.php';
function check( $value, $message ) { if ( ! $value ) { throw new RuntimeException( $message ); } }
function render_join() { ob_start(); require __DIR__ . '/../template-parts/join.php'; return ob_get_clean(); }
check( ! rbc68_join_documents(), 'Aucun lien sans document sélectionné' );
$mods = array( 'rbc68_join_doc_1' => 4, 'rbc68_join_doc_2' => 9, 'rbc68_join_doc_3' => 8, 'rbc68_join_doc_4' => 7, 'rbc68_join_season' => '2027-2028', 'rbc68_price_adults' => '110 €', 'rbc68_join_doc_1_note' => '<script>test</script>' );
check( count( rbc68_join_documents() ) === 1, 'Pièces supprimées, corbeille et autres contenus exclus' );
$html = render_join();
check( strpos( $html, '110 €' ) !== false && strpos( $html, 'Inscription 2027-2028' ) !== false, 'Saison et tarif partagés' );
check( strpos( $html, '<script>' ) === false && strpos( $html, '&lt;script&gt;' ) !== false, 'Consignes échappées' );
check( strpos( $html, 'Complet' ) !== false, 'Disponibilités conservées' );
$mods['rbc68_join_dates'] = '';
$mods['rbc68_join_renew_url'] = '';
$html = render_join();
check( strpos( $html, 'join-dates' ) === false && strpos( $html, 'licence.ffbad.org' ) === false, 'Champs vides masqués' );
if ( getenv( 'RBC68_RENDER' ) ) { echo $html; } else { echo "OK — inscription, tarifs, documents et champs vides\n"; }
