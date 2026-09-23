<?php
/** php tests/event-content.php — scénarios de bilan, API WordPress simulée. */
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
class WP_Error { public $code; public $message; function __construct( $code, $message ) { $this->code = $code; $this->message = $message; } }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function get_post( $id ) { return $GLOBALS['posts'][$id] ?? null; }
function get_post_meta( $id, $key, $single = true ) { return $GLOBALS['meta'][$id][$key] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['meta'][$id][$key] = $value; }
function get_post_stati() { return array_fill_keys( array( 'publish', 'draft', 'pending', 'future', 'private', 'trash' ), true ); }
function get_posts( $args ) {
	$out = array();
	foreach ( $GLOBALS['posts'] as $id => $post ) {
		if ( 'post' === $post->post_type && get_post_meta( $id, $args['meta_key'] ) == $args['meta_value'] ) { $out[] = $id; }
	}
	return $out;
}
function get_post_type_object( $type ) { return (object) array( 'cap' => (object) array( 'create_posts' => 'edit_posts' ) ); }
function current_user_can( ...$args ) { return $GLOBALS['allowed']; }
function get_current_user_id() { return 1; }
class Test_DB {
	public $options = 'wp_options';
	function prepare( $sql, $key ) { return $key; }
	function query( $key ) {
		if ( isset( $GLOBALS['options'][$key] ) ) { return 0; }
		$GLOBALS['options'][$key] = 1; return 1;
	}
	function delete( $table, $where, $format ) { unset( $GLOBALS['options'][$where['option_name']] ); }
}
$GLOBALS['wpdb'] = new Test_DB();
function wp_slash( $v ) { return is_array( $v ) ? array_map( 'wp_slash', $v ) : ( is_string( $v ) ? addslashes( $v ) : $v ); }
function wp_insert_post( $data, $error ) {
	if ( ! empty( $GLOBALS['fail'] ) ) { return new WP_Error( 'db', 'Échec simulé' ); }
	// Simule une seconde requête pendant l’insertion de la première.
	$GLOBALS['concurrent'] = rbc68_ensure_event_report( $data['meta_input']['rbc68_report_event_id'] );
	$id = 100 + ++$GLOBALS['inserts'];
	$GLOBALS['posts'][$id] = (object) array( 'ID' => $id, 'post_type' => 'post', 'post_status' => $data['post_status'], 'post_title' => stripslashes( $data['post_title'] ), 'post_password' => '' );
	$GLOBALS['meta'][$id] = $data['meta_input'];
	return $id;
}
function url_to_postid( $url ) { return $GLOBALS['urls'][$url] ?? 0; }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function home_url() { return 'https://club.test'; }
function get_permalink( $id ) { return 'https://club.test/?p=' . $id; }
require dirname( __DIR__ ) . '/inc/event-content.php';
function check( $ok, $label ) { if ( ! $ok ) { throw new RuntimeException( $label ); } }
function event( $id ) { $GLOBALS['posts'][$id] = (object) array( 'ID' => $id, 'post_type' => 'rbc68_event', 'post_status' => 'publish', 'post_title' => "Soirée d’automne \\ RBC" ); }
$GLOBALS['allowed'] = true; $GLOBALS['inserts'] = 0;
event( 1 ); event( 2 ); event( 3 ); event( 4 ); event( 5 );
$id = rbc68_ensure_event_report( 1 );
check( $id === 101 && $GLOBALS['inserts'] === 1, 'Premier brouillon' );
check( get_post( $id )->post_title === get_post( 1 )->post_title, 'Titre identique et caractères préservés' );
check( get_post_meta( $id, 'rbc68_report_event_id' ) === 1, 'Lien retour' );
check( is_wp_error( $GLOBALS['concurrent'] ) && $GLOBALS['concurrent']->code === 'busy', 'Double requête bloquée' );
check( rbc68_ensure_event_report( 1 ) === $id && $GLOBALS['inserts'] === 1, 'Second clic sans doublon' );
foreach ( array( 'draft', 'pending', 'future', 'private', 'trash', 'publish' ) as $status ) {
	get_post( $id )->post_status = $status;
	check( rbc68_ensure_event_report( 1 ) === $id, 'Réutilisation état ' . $status );
	check( rbc68_event_report_url( 1 ) === ( 'publish' === $status ? get_permalink( $id ) : '' ), 'Visibilité état ' . $status );
}
get_post( $id )->post_password = 'secret';
check( rbc68_event_report_url( 1 ) === '', 'Bilan protégé masqué' );
get_post( $id )->post_password = '';
unset( $GLOBALS['meta'][1]['rbc68_event_report_id'] );
check( rbc68_ensure_event_report( 1 ) === $id, 'Retrouvé par le lien retour' );
$GLOBALS['allowed'] = false;
check( is_wp_error( rbc68_ensure_event_report( 2 ) ) && $GLOBALS['inserts'] === 1, 'Permissions' );
$GLOBALS['allowed'] = true;
$GLOBALS['fail'] = true;
check( is_wp_error( rbc68_ensure_event_report( 2 ) ) && empty( $GLOBALS['options'] ), 'Échec libère le verrou' );
$GLOBALS['fail'] = false;
check( rbc68_ensure_event_report( 2 ) === 102, 'Nouvel essai après échec' );
$GLOBALS['posts'][50] = (object) array( 'ID' => 50, 'post_type' => 'post', 'post_status' => 'draft', 'post_password' => '' );
check( rbc68_ensure_event_report( 3, 50 ) === 50 && $GLOBALS['inserts'] === 2, 'Lien manuel sans création' );
check( is_wp_error( rbc68_ensure_event_report( 4, 50 ) ), 'Article déjà affecté refusé' );
$GLOBALS['meta'][4]['rbc68_event_article_url'] = 'https://ailleurs.test/bilan';
check( is_wp_error( rbc68_ensure_event_report( 4 ) ) && $GLOBALS['inserts'] === 2, 'Lien historique externe sans doublon' );
$GLOBALS['meta'][5]['rbc68_event_article_url'] = 'https://club.test/bilan';
$GLOBALS['urls']['https://club.test/bilan'] = 50;
check( rbc68_ensure_event_report( 5 ) === 50 && rbc68_event_report_url( 5 ) === '', 'Lien historique local brouillon masqué' );
// Suppression définitive : un nouveau bilan peut être créé.
unset( $GLOBALS['posts'][102] );
check( rbc68_ensure_event_report( 2 ) === 103, 'Suppression définitive permet un nouveau bilan' );
check( empty( $GLOBALS['options'] ), 'Tous les verrous libérés' );
echo "OK — création, titre, doubles requêtes, états, permissions, échec, liens existants et suppression\n";
