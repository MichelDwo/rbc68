<?php
/** Export iCalendar accessible sans JavaScript et sans connexion. */
defined( 'ABSPATH' ) || exit;

function rbc68_calendar_url( $id ) {
	return add_query_arg( 'rbc68_calendar', absint( $id ), home_url( '/' ) );
}

function rbc68_ics_text( $text ) {
	$text = html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return str_replace( array( '\\', "\r\n", "\r", "\n", ';', ',' ), array( '\\\\', '\\n', '\\n', '\\n', '\\;', '\\,' ), $text );
}

/** RFC 5545 : lignes de 75 octets maximum, sans couper un caractère UTF-8. */
function rbc68_ics_fold( $line ) {
	$parts = array();
	while ( strlen( $line ) > 75 ) {
		$length = 75;
		while ( ( ord( $line[ $length ] ) & 0xC0 ) === 0x80 ) {
			--$length;
		}
		$parts[] = substr( $line, 0, $length );
		$line = ' ' . substr( $line, $length );
	}
	$parts[] = $line;
	return implode( "\r\n", $parts );
}

function rbc68_calendar_datetime( $date, $time = '00:00' ) {
	$value = $date . ' ' . $time;
	$parsed = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $value, wp_timezone() );
	return $parsed && $parsed->format( 'Y-m-d H:i' ) === $value ? $parsed : false;
}

/** Une fin inconnue est omise, jamais inventée ou égale au début. */
function rbc68_calendar_ics( $id ) {
	$post = get_post( $id );
	if ( ! $post || 'rbc68_event' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password ) {
		return false;
	}
	$date = get_post_meta( $id, 'rbc68_event_date', true );
	$time = get_post_meta( $id, 'rbc68_event_start_time', true );
	$end_date = get_post_meta( $id, 'rbc68_event_end_date', true ) ?: $date;
	$end_time = get_post_meta( $id, 'rbc68_event_end_time', true );
	$start = rbc68_calendar_datetime( $date, $time ?: '00:00' );
	if ( ! $start ) {
		return false;
	}
	$utc = new DateTimeZone( 'UTC' );
	$lines = array(
		'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//RBC68//Events//FR', 'CALSCALE:GREGORIAN',
		'BEGIN:VEVENT',
		'UID:rbc68-event-' . $id . '-' . md5( home_url( '/' ) ) . '@rbc68',
		'DTSTAMP:' . gmdate( 'Ymd\THis\Z' ),
	);
	if ( $time ) {
		$lines[] = 'DTSTART:' . $start->setTimezone( $utc )->format( 'Ymd\THis\Z' );
		$end = $end_time ? rbc68_calendar_datetime( $end_date, $end_time ) : false;
		if ( $end && $end > $start ) {
			$lines[] = 'DTEND:' . $end->setTimezone( $utc )->format( 'Ymd\THis\Z' );
		}
	} else {
		$end = rbc68_calendar_datetime( $end_date );
		$end = $end && $end >= $start ? $end : $start;
		$lines[] = 'DTSTART;VALUE=DATE:' . $start->format( 'Ymd' );
		$lines[] = 'DTEND;VALUE=DATE:' . $end->modify( '+1 day' )->format( 'Ymd' );
	}
	$lines[] = 'SUMMARY:' . rbc68_ics_text( $post->post_title );
	$lines[] = 'DESCRIPTION:' . rbc68_ics_text( get_post_meta( $id, 'rbc68_event_details', true ) );
	$lines[] = 'LOCATION:' . rbc68_ics_text( get_post_meta( $id, 'rbc68_event_location', true ) );
	$lines[] = 'END:VEVENT';
	$lines[] = 'END:VCALENDAR';
	return implode( "\r\n", array_map( 'rbc68_ics_fold', $lines ) ) . "\r\n";
}

function rbc68_serve_calendar() {
	if ( ! isset( $_GET['rbc68_calendar'] ) ) {
		return;
	}
	$value = $_GET['rbc68_calendar'];
	$id = is_string( $value ) && preg_match( '/^[1-9][0-9]*$/D', $value ) ? absint( $value ) : 0;
	$ics = $id ? rbc68_calendar_ics( $id ) : false;
	if ( false === $ics ) {
		wp_die( 'Événement indisponible ou date invalide.', 'Calendrier RBC68', array( 'response' => 404 ) );
	}
	status_header( 200 );
	nocache_headers();
	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Disposition: inline; filename="rbc68-event-' . $id . '.ics"' );
	header( 'X-Content-Type-Options: nosniff' );
	echo $ics; // Texte iCalendar échappé, pas de HTML.
	exit;
}
add_action( 'template_redirect', 'rbc68_serve_calendar', 0 );
