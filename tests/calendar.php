<?php
/** php tests/calendar.php — tests du générateur avec API WordPress simulée. */
define( 'ABSPATH', __DIR__ );
function add_action( ...$args ) {}
function home_url( $path ) { return 'https://example.org' . $path; }
function wp_timezone() { return new DateTimeZone( 'Europe/Paris' ); }
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function get_post( $id ) { return $id === 7 ? $GLOBALS['event'] : null; }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['meta'][ $key ] ?? ''; }
require __DIR__ . '/../inc/calendar.php';

function check( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
function fixture( $date, $time = '', $end_date = '', $end_time = '' ) {
	$GLOBALS['event'] = (object) array( 'post_type' => 'rbc68_event', 'post_status' => 'publish', 'post_password' => '', 'post_title' => 'Tournoi' );
	$GLOBALS['meta'] = array( 'rbc68_event_date' => $date, 'rbc68_event_start_time' => $time, 'rbc68_event_end_date' => $end_date, 'rbc68_event_end_time' => $end_time );
}

fixture( '2026-10-02', '19:00', '', '22:00' );
$ics = rbc68_calendar_ics( 7 );
check( strpos( $ics, 'DTSTART:20261002T170000Z' ) !== false, 'Heure d’été' );
check( strpos( $ics, 'DTEND:20261002T200000Z' ) !== false, 'Fin en UTC' );
fixture( '2026-12-18', '19:00', '', '22:00' );
check( strpos( rbc68_calendar_ics( 7 ), 'DTSTART:20261218T180000Z' ) !== false, 'Heure d’hiver' );
fixture( '2026-10-25', '01:00', '', '04:00' );
$ics = rbc68_calendar_ics( 7 );
check( strpos( $ics, 'DTSTART:20261024T230000Z' ) !== false && strpos( $ics, 'DTEND:20261025T030000Z' ) !== false, 'Changement d’heure pendant l’événement' );
fixture( '2026-10-31' );
check( strpos( rbc68_calendar_ics( 7 ), 'DTEND;VALUE=DATE:20261101' ) !== false, 'Fin exclusive journée entière' );
fixture( '2026-12-30', '', '2027-01-02' );
check( strpos( rbc68_calendar_ics( 7 ), 'DTEND;VALUE=DATE:20270103' ) !== false, 'Plusieurs jours' );
foreach ( array( '', '18:00', '19:00' ) as $end ) {
	fixture( '2026-10-02', '19:00', '', $end );
	check( strpos( rbc68_calendar_ics( 7 ), 'DTEND' ) === false, 'Pas de fin invalide/inventée' );
}
fixture( '2026-10-02', '23:00', '2026-10-03', '01:00' );
check( strpos( rbc68_calendar_ics( 7 ), 'DTEND:20261002T230000Z' ) !== false, 'Passage de minuit' );
fixture( '2026-02-30' );
check( rbc68_calendar_ics( 7 ) === false, 'Date invalide' );
fixture( '2026-10-02', '25:00' );
check( rbc68_calendar_ics( 7 ) === false, 'Heure invalide' );
fixture( '2026-10-02' );
foreach ( array( 'draft', 'private', 'trash', 'future' ) as $status ) {
	$GLOBALS['event']->post_status = $status;
	check( rbc68_calendar_ics( 7 ) === false, 'Contenu non public' );
}
fixture( '2026-10-02' );
$GLOBALS['event']->post_password = 'secret';
check( rbc68_calendar_ics( 7 ) === false, 'Contenu protégé' );
fixture( '2026-10-02' );
$GLOBALS['event']->post_type = 'post';
check( rbc68_calendar_ics( 7 ) === false && rbc68_calendar_ics( 99 ) === false, 'Type ou ID incorrect' );
fixture( '2026-10-02' );
$GLOBALS['event']->post_title = str_repeat( 'Équipe 🏸 ', 30 ) . '&amp; amis';
$GLOBALS['meta']['rbc68_event_details'] = "Texte, point; chemin\\suite\r\nBEGIN:VEVENT";
$ics = rbc68_calendar_ics( 7 );
foreach ( explode( "\r\n", $ics ) as $line ) {
	check( strlen( $line ) <= 75 && preg_match( '//u', $line ) === 1, 'Pliage UTF-8 à 75 octets' );
}
check( substr_count( $ics, "\r\nBEGIN:VEVENT\r\n" ) === 1, 'Pas d’injection par retour à la ligne' );
$unfolded = str_replace( "\r\n ", '', $ics );
check( strpos( $unfolded, 'SUMMARY:' . $GLOBALS['event']->post_title ) === false, 'Entités HTML décodées' );
check( strpos( $unfolded, 'Texte\\, point\\; chemin\\\\suite\\nBEGIN:VEVENT' ) !== false, 'Échappement iCalendar' );
preg_match( '/UID:([^\r]+)/', $ics, $first );
$GLOBALS['event']->post_title = 'Titre modifié';
preg_match( '/UID:([^\r]+)/', rbc68_calendar_ics( 7 ), $second );
check( $first[1] === $second[1], 'Identifiant stable après modification' );
echo "OK — dates, UTC été/hiver, fins, confidentialité, UTF-8, échappement, UID\n";
