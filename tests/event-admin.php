<?php
/** php tests/event-admin.php — API WordPress simulée. */
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function get_template_directory() { return dirname( __DIR__ ); }
function wp_timezone() { return new DateTimeZone( 'Europe/Paris' ); }
function get_post( $id ) { return $GLOBALS['posts'][$id] ?? null; }
function get_post_meta( $id, $key = null, $single = false ) {
	$meta = $GLOBALS['meta'][$id] ?? array();
	return null === $key ? array_map( function ( $v ) { return array( $v ); }, $meta ) : ( $meta[$key] ?? '' );
}
function wp_update_post( $data ) {
	$GLOBALS['posts'][$data['ID']]->post_status = $data['post_status'];
	$GLOBALS['updates'][] = $data;
	rbc68_sync_event_archive( $data['ID'] ); // Vérifie la protection contre la récursion.
}
function get_posts( $args ) {
	return array_keys( array_filter( $GLOBALS['posts'], function ( $p ) { return 'rbc68_event' === $p->post_type && 'publish' === $p->post_status; } ) );
}
function absint( $v ) { return abs( (int) $v ); }
function check_admin_referer( $action ) { if ( ! $GLOBALS['nonce'] ) { throw new RuntimeException( 'nonce' ); } }
function current_user_can( ...$args ) { return $GLOBALS['allowed']; }
function get_post_type_object( $type ) { return (object) array( 'cap' => (object) array( 'create_posts' => 'edit_posts' ) ); }
function wp_die( ...$args ) { throw new RuntimeException( 'denied' ); }
function get_current_user_id() { return 5; }
function wp_slash( $v ) { return is_array( $v ) ? array_map( 'wp_slash', $v ) : ( is_string( $v ) ? addslashes( $v ) : $v ); }
function wp_insert_post( $data, $error ) { $GLOBALS['insert'] = $data; return 99; }
function is_wp_error( $value ) { return false; }
function get_edit_post_link( $id, $context ) { return 'edit-' . $id; }
function wp_safe_redirect( $url ) { throw new RuntimeException( $url ); }
function is_admin() { return true; }
require dirname( __DIR__ ) . '/functions.php';
function check( $ok, $message ) { if ( ! $ok ) { throw new RuntimeException( $message ); } }
function event( $id, $status, $date, $end = '', $time = '' ) {
	$GLOBALS['posts'][$id] = (object) array( 'ID' => $id, 'post_type' => 'rbc68_event', 'post_status' => $status, 'post_title' => 'Soirée d’hiver', 'post_content' => '', 'post_excerpt' => '' );
	$GLOBALS['meta'][$id] = array( 'rbc68_event_date' => $date, 'rbc68_event_end_date' => $end, 'rbc68_event_end_time' => $time );
}
event( 1, 'publish', '2000-01-01' );
event( 2, 'publish', '2100-01-01' );
event( 3, 'draft', '2000-01-01' );
event( 4, 'publish', '2000-01-01', '2100-01-01' );
event( 5, 'publish', '2000-02-30' );
event( 6, 'publish', ( new DateTimeImmutable( 'now', wp_timezone() ) )->format( 'Y-m-d' ) );
event( 7, 'publish', '2000-01-01', '', '25:00' );
rbc68_archive_events();
check( 'rbc68_archived' === get_post( 1 )->post_status, 'Passé archivé' );
foreach ( array( 2, 4, 5, 6, 7 ) as $id ) { check( 'publish' === get_post( $id )->post_status, 'Futur/en cours/date invalide conservé' ); }
check( 'draft' === get_post( 3 )->post_status, 'Brouillon conservé' );
rbc68_archive_events();
check( count( $GLOBALS['updates'] ) === 1, 'Archivage idempotent' );
$GLOBALS['meta'][1]['rbc68_event_date'] = '2100-01-01';
rbc68_sync_event_archive( 1 );
check( 'publish' === get_post( 1 )->post_status, 'Report futur republié' );
$GLOBALS['posts'][1]->post_status = 'rbc68_archived';
$GLOBALS['meta'][1]['rbc68_event_location'] = 'Salle, Rixheim';
$GLOBALS['meta'][1]['rbc68_event_details'] = "L’équipe \\ test";
$GLOBALS['meta'][1]['_edit_lock'] = 'ne pas copier';
$_GET['post'] = '1'; $GLOBALS['nonce'] = true; $GLOBALS['allowed'] = true;
try { rbc68_duplicate_event(); } catch ( RuntimeException $e ) { check( 'edit-99' === $e->getMessage(), 'Redirection copie' ); }
$copy = $GLOBALS['insert'];
check( 'draft' === $copy['post_status'] && false !== strpos( $copy['post_title'], 'dupliqué' ), 'Copie en brouillon nommée' );
check( ! isset( $copy['meta_input']['_edit_lock'] ) && $copy['meta_input']['rbc68_event_location'] === 'Salle, Rixheim', 'Champs utiles seuls copiés' );
check( stripslashes( $copy['meta_input']['rbc68_event_details'] ) === $GLOBALS['meta'][1]['rbc68_event_details'], 'Texte préservé' );
unset( $GLOBALS['insert'] ); $GLOBALS['allowed'] = false;
try { rbc68_duplicate_event(); } catch ( RuntimeException $e ) { check( 'denied' === $e->getMessage(), 'Permission' ); }
check( ! isset( $GLOBALS['insert'] ), 'Aucune copie non autorisée' );
$GLOBALS['allowed'] = true; $GLOBALS['nonce'] = false;
try { rbc68_duplicate_event(); } catch ( RuntimeException $e ) { check( 'nonce' === $e->getMessage(), 'Nonce' ); }
check( ! isset( $GLOBALS['insert'] ), 'Aucune copie sans nonce' );
echo "OK — archivage, dates, report, duplication, permissions et nonce\n";
