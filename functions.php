<?php
/**
 * Fonctions du thème RBC68.
 *
 * @package RBC68
 */

defined( 'ABSPATH' ) || exit;

define( 'RBC68_VERSION', '1.7.0' );

require_once get_template_directory() . '/inc/committee.php';

require_once get_template_directory() . '/inc/calendar.php';

require_once get_template_directory() . '/inc/event-admin.php';
require_once get_template_directory() . '/inc/event-content.php';

/** Saison sportive de septembre à août. */
function rbc68_season_start( $date ) {
	$year = (int) substr( $date, 0, 4 );
	return (int) substr( $date, 5, 2 ) < 9 ? $year - 1 : $year;
}

function rbc68_event_archive_group( $id ) {
	if ( ! rbc68_event_is_past( $id ) ) {
		return 'upcoming';
	}
	$season = rbc68_season_start( get_post_meta( $id, 'rbc68_event_date', true ) );
	return $season >= rbc68_season_start( wp_date( 'Y-m-d' ) ) ? 'past' : (string) $season;
}

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
	wp_enqueue_style( 'rbc68-style', get_stylesheet_uri(), array(), RBC68_VERSION . '.' . filemtime( get_stylesheet_directory() . '/style.css' ) );
	wp_enqueue_script( 'rbc68-main', get_template_directory_uri() . '/assets/js/main.js', array(), (string) filemtime( get_template_directory() . '/assets/js/main.js' ), true );
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

function rbc68_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function rbc68_sanitize_availability( $value ) {
	return in_array( $value, array( 'available', 'full' ), true ) ? $value : 'available';
}

function rbc68_availability_label( $section ) {
	$status = rbc68_mod( 'rbc68_availability_' . $section, 'available' );
	return 'full' === $status ? 'Complet' : '';
}

function rbc68_event_kind( $post_id ) {
	$kind = get_post_meta( $post_id, 'rbc68_event_kind', true );
	return 'event' === $kind || ! $kind ? 'public' : $kind;
}

function rbc68_event_kind_label( $post_id ) {
	$labels = array(
		'public'    => 'Public',
		'interclub' => 'Interclub',
		'internal'  => 'Interne',
		'training'  => 'Entraînement',
	);
	$kind = rbc68_event_kind( $post_id );
	return isset( $labels[ $kind ] ) ? $labels[ $kind ] : 'Public';
}

function rbc68_format_event_date( $date ) {
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $date ) ) {
		return '';
	}

	$datetime = date_create_immutable_from_format( '!Y-m-d', $date, wp_timezone() );
	return $datetime ? wp_date( 'j F Y', $datetime->getTimestamp(), wp_timezone() ) : '';
}

function rbc68_format_event_time( $time ) {
	if ( ! preg_match( '/^(?:[01]\d|2[0-3]):[0-5]\d$/', (string) $time ) ) {
		return '';
	}

	return str_replace( ':', 'h', $time );
}

function rbc68_event_is_past( $post_id ) {
	$start_date = get_post_meta( $post_id, 'rbc68_event_date', true );
	$end_date   = get_post_meta( $post_id, 'rbc68_event_end_date', true ) ?: $start_date;
	$end_time   = get_post_meta( $post_id, 'rbc68_event_end_time', true );
	$value      = $end_date . ' ' . ( $end_time ? $end_time . ':00' : '23:59:59' );
	$end        = date_create_immutable_from_format( '!Y-m-d H:i:s', $value, wp_timezone() );
	$start      = date_create_immutable_from_format( '!Y-m-d', $start_date, wp_timezone() );

	return $start && $start->format( 'Y-m-d' ) === $start_date && $end && $end->format( 'Y-m-d H:i:s' ) === $value && $end >= $start && $end < new DateTimeImmutable( 'now', wp_timezone() );
}

function rbc68_event_map_embed_url( $post_id ) {
	$latitude  = (float) get_post_meta( $post_id, 'rbc68_event_latitude', true );
	$longitude = (float) get_post_meta( $post_id, 'rbc68_event_longitude', true );
	if ( ! $latitude || ! $longitude ) {
		return '';
	}

	$delta = 0.007;
	return add_query_arg(
		array(
			'bbox'   => ( $longitude - $delta ) . ',' . ( $latitude - $delta / 2 ) . ',' . ( $longitude + $delta ) . ',' . ( $latitude + $delta / 2 ),
			'layer'  => 'mapnik',
			'marker' => $latitude . ',' . $longitude,
		),
		'https://www.openstreetmap.org/export/embed.html'
	);
}

function rbc68_event_map_link( $post_id ) {
	$latitude  = (float) get_post_meta( $post_id, 'rbc68_event_latitude', true );
	$longitude = (float) get_post_meta( $post_id, 'rbc68_event_longitude', true );
	if ( $latitude && $longitude ) {
		return 'https://www.openstreetmap.org/?mlat=' . rawurlencode( $latitude ) . '&mlon=' . rawurlencode( $longitude ) . '#map=17/' . rawurlencode( $latitude ) . '/' . rawurlencode( $longitude );
	}

	$address = get_post_meta( $post_id, 'rbc68_event_location', true );
	return $address ? 'https://www.openstreetmap.org/search?query=' . rawurlencode( $address ) : '';
}

function rbc68_event_article_url( $post_id ) {
	return get_permalink( $post_id );
}

function rbc68_event_when_label( $post_id ) {
	$start_date = get_post_meta( $post_id, 'rbc68_event_date', true );
	$end_date   = get_post_meta( $post_id, 'rbc68_event_end_date', true );
	$start_time = rbc68_format_event_time( get_post_meta( $post_id, 'rbc68_event_start_time', true ) );
	$end_time   = rbc68_format_event_time( get_post_meta( $post_id, 'rbc68_event_end_time', true ) );
	$label      = rbc68_format_event_date( $start_date );

	if ( $end_date && $end_date !== $start_date ) {
		$label .= ' – ' . rbc68_format_event_date( $end_date );
	}
	if ( $start_time ) {
		$label .= ', ' . $start_time;
		if ( $end_time ) {
			$label .= '–' . $end_time;
		}
	}

	return $label;
}

function rbc68_event_ics_date( $date, $add_day = false ) {
	$datetime = date_create_immutable_from_format( '!Y-m-d', (string) $date, wp_timezone() );
	if ( ! $datetime ) {
		return '';
	}
	if ( $add_day ) {
		$datetime = $datetime->modify( '+1 day' );
	}
	return $datetime->format( 'Ymd' );
}

function rbc68_get_upcoming_events( $limit = 3 ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'rbc68_event',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => 'rbc68_event_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					array(
						'key'     => 'rbc68_event_date',
						'value'   => wp_date( 'Y-m-d' ),
						'compare' => '>=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'rbc68_event_end_date',
						'value'   => wp_date( 'Y-m-d' ),
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
				array(
					'key'   => 'rbc68_event_kind',
					'value' => 'training',
					'compare' => '!=',
				),
			),
		)
	);
	$events = array_values(
		array_filter(
			$query->posts,
			function ( $event ) {
				return ! rbc68_event_is_past( $event->ID );
			}
		)
	);

	return array_slice( $events, 0, max( 0, absint( $limit ) ) );
}

function rbc68_register_event_type() {
	register_post_type(
		'rbc68_event',
		array(
			'labels' => array(
				'name'          => __( 'Événements', 'rbc68' ),
				'singular_name' => __( 'Événement', 'rbc68' ),
				'add_new_item'  => __( 'Ajouter un événement', 'rbc68' ),
				'edit_item'     => __( 'Modifier l’événement', 'rbc68' ),
			),
			'public'       => true,
			'has_archive'  => false,
			'menu_icon'    => 'dashicons-calendar-alt',
			'show_in_rest' => false,
			'supports'     => array( 'title' ),
			'rewrite'      => array( 'slug' => 'evenement' ),
		)
	);
}
add_action( 'init', 'rbc68_register_event_type' );

function rbc68_event_classic_editor( $use_block_editor, $post_type ) {
	return 'rbc68_event' === $post_type ? false : $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'rbc68_event_classic_editor', 10, 2 );

function rbc68_event_meta_box() {
	add_meta_box( 'rbc68-event-details', __( 'Informations pratiques de l’événement', 'rbc68' ), 'rbc68_event_meta_box_html', 'rbc68_event', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'rbc68_event_meta_box' );

function rbc68_event_meta_box_html( $post ) {
	wp_nonce_field( 'rbc68_save_event', 'rbc68_event_nonce' );
	$start = get_post_meta( $post->ID, 'rbc68_event_date', true );
	$end   = get_post_meta( $post->ID, 'rbc68_event_end_date', true );
	$kind  = get_post_meta( $post->ID, 'rbc68_event_kind', true ) ?: 'public';
	$kind  = 'event' === $kind ? 'public' : $kind;
	$fields = array(
		'rbc68_event_start_time'  => get_post_meta( $post->ID, 'rbc68_event_start_time', true ),
		'rbc68_event_end_time'    => get_post_meta( $post->ID, 'rbc68_event_end_time', true ),
		'rbc68_event_location'    => get_post_meta( $post->ID, 'rbc68_event_location', true ),
		'rbc68_event_latitude'    => get_post_meta( $post->ID, 'rbc68_event_latitude', true ),
		'rbc68_event_longitude'   => get_post_meta( $post->ID, 'rbc68_event_longitude', true ),
		'rbc68_event_team'        => get_post_meta( $post->ID, 'rbc68_event_team', true ),
		'rbc68_event_details'     => get_post_meta( $post->ID, 'rbc68_event_details', true ),
		'rbc68_event_article_url' => get_post_meta( $post->ID, 'rbc68_event_article_url', true ),
	);
	?>
	<div class="rbc68-event-admin-grid">
		<p><label for="rbc68_event_kind"><strong>Catégorie</strong></label><br><select id="rbc68_event_kind" name="rbc68_event_kind"><option value="public" <?php selected( $kind, 'public' ); ?>>Public</option><option value="interclub" <?php selected( $kind, 'interclub' ); ?>>Interclub</option><option value="internal" <?php selected( $kind, 'internal' ); ?>>Interne</option><option value="training" <?php selected( $kind, 'training' ); ?>>Entraînement (masqué de l’accueil)</option></select></p>
		<p><label for="rbc68_event_team"><strong>Équipe concernée</strong> (interclubs)</label><br><input class="widefat" type="text" id="rbc68_event_team" name="rbc68_event_team" value="<?php echo esc_attr( $fields['rbc68_event_team'] ); ?>"></p>
		<p><label for="rbc68_event_date"><strong>Date de début</strong></label><br><input type="date" id="rbc68_event_date" name="rbc68_event_date" value="<?php echo esc_attr( $start ); ?>" required></p>
		<p><label for="rbc68_event_end_date"><strong>Date de fin</strong> (facultative)</label><br><input type="date" id="rbc68_event_end_date" name="rbc68_event_end_date" value="<?php echo esc_attr( $end ); ?>"></p>
		<p><label for="rbc68_event_start_time"><strong>Heure de début</strong></label><br><input type="text" inputmode="text" placeholder="19:30" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" maxlength="5" size="5" title="Heure au format 24 h : HH:MM (exemple : 19:30)" id="rbc68_event_start_time" name="rbc68_event_start_time" value="<?php echo esc_attr( $fields['rbc68_event_start_time'] ); ?>"></p>
		<p><label for="rbc68_event_end_time"><strong>Heure de fin</strong></label><br><input type="text" inputmode="text" placeholder="19:30" pattern="([01][0-9]|2[0-3]):[0-5][0-9]" maxlength="5" size="5" title="Heure au format 24 h : HH:MM (exemple : 19:30)" id="rbc68_event_end_time" name="rbc68_event_end_time" value="<?php echo esc_attr( $fields['rbc68_event_end_time'] ); ?>"></p>
	</div>
	<p><label for="rbc68_event_location"><strong>Lieu / adresse affichée</strong></label><br><input class="widefat" type="text" id="rbc68_event_location" name="rbc68_event_location" value="<?php echo esc_attr( $fields['rbc68_event_location'] ); ?>"></p>
	<div class="rbc68-event-admin-grid"><p><label for="rbc68_event_latitude"><strong>Latitude</strong></label><br><input type="number" step="any" id="rbc68_event_latitude" name="rbc68_event_latitude" value="<?php echo esc_attr( $fields['rbc68_event_latitude'] ); ?>"></p><p><label for="rbc68_event_longitude"><strong>Longitude</strong></label><br><input type="number" step="any" id="rbc68_event_longitude" name="rbc68_event_longitude" value="<?php echo esc_attr( $fields['rbc68_event_longitude'] ); ?>"></p></div>
	<p class="description">Les coordonnées placent le repère sur la mini-carte OpenStreetMap. Pour la salle habituelle : 47.7331928 / 7.3777678.</p>
	<p><label for="rbc68_event_details"><strong>Annonce — description courte</strong> (affichée dans la liste et sur la fiche)</label><br><textarea class="widefat" rows="4" id="rbc68_event_details" name="rbc68_event_details"><?php echo esc_textarea( $fields['rbc68_event_details'] ); ?></textarea></p>
	<?php rbc68_event_content_fields( $post ); ?>
	<style>.rbc68-event-admin-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:0 1rem}.rbc68-event-admin-grid input,.rbc68-event-admin-grid select{max-width:100%}</style>
	<?php
}

function rbc68_save_event_meta( $post_id ) {
	if ( ! isset( $_POST['rbc68_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rbc68_event_nonce'] ) ), 'rbc68_save_event' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'rbc68_event_date', 'rbc68_event_end_date' ) as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		if ( $value && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
	$kind = isset( $_POST['rbc68_event_kind'] ) ? sanitize_key( wp_unslash( $_POST['rbc68_event_kind'] ) ) : 'event';
	update_post_meta( $post_id, 'rbc68_event_kind', in_array( $kind, array( 'public', 'interclub', 'internal', 'training' ), true ) ? $kind : 'public' );

	foreach ( array( 'rbc68_event_start_time', 'rbc68_event_end_time' ) as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		if ( preg_match( '/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value ) ) {
			update_post_meta( $post_id, $key, $value );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
	foreach ( array( 'rbc68_event_location', 'rbc68_event_team' ) as $key ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		$value ? update_post_meta( $post_id, $key, $value ) : delete_post_meta( $post_id, $key );
	}
	$details = isset( $_POST['rbc68_event_details'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rbc68_event_details'] ) ) : '';
	$details ? update_post_meta( $post_id, 'rbc68_event_details', $details ) : delete_post_meta( $post_id, 'rbc68_event_details' );
	rbc68_save_event_content( $post_id );
	foreach ( array( 'rbc68_event_latitude' => 90, 'rbc68_event_longitude' => 180 ) as $key => $limit ) {
		$value = isset( $_POST[ $key ] ) ? (float) wp_unslash( $_POST[ $key ] ) : 0;
		( $value >= -$limit && $value <= $limit && 0.0 !== $value ) ? update_post_meta( $post_id, $key, (string) $value ) : delete_post_meta( $post_id, $key );
	}
}
add_action( 'save_post_rbc68_event', 'rbc68_save_event_meta' );

function rbc68_seed_events() {
	if ( get_option( 'rbc68_events_seeded_130' ) ) {
		return;
	}
	if ( get_posts( array( 'post_type' => 'rbc68_event', 'numberposts' => 1, 'post_status' => 'any' ) ) ) {
		update_option( 'rbc68_events_seeded_130', 1 );
		return;
	}

	$events = array(
		array( 'Démarrage de la saison (section jeune et adulte)', '2026-09-01', '', 'training' ),
		array( 'Démarrage de la saison (section adulte compétiteurs)', '2026-09-02', '', 'training' ),
		array( 'Réunion de rentrée avec section jeune et mini-bad', '2026-09-04', '', 'training' ),
		array( 'Journées d’Automne et des Associations', '2026-09-05', '2026-09-06', 'public' ),
		array( 'Octobre rose', '2026-10-02', '', 'public' ),
		array( 'Halloween', '2026-10-31', '', 'public' ),
		array( 'Assemblée générale', '2026-11-15', '', 'internal' ),
		array( 'Fête de fin d’année', '2026-12-18', '', 'internal' ),
		array( 'Stage Club', '2027-02-07', '', 'internal' ),
		array( 'Bad brunch', '2027-03-21', '', 'public' ),
		array( 'Événement parents/jeunes', '2027-06-05', '', 'internal' ),
		array( 'Fête de fin de saison', '2027-06-27', '', 'internal' ),
	);
	foreach ( $events as $event ) {
		$post_id = wp_insert_post( array( 'post_type' => 'rbc68_event', 'post_status' => 'publish', 'post_title' => $event[0] ) );
		if ( ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, 'rbc68_event_date', $event[1] );
			if ( $event[2] ) {
				update_post_meta( $post_id, 'rbc68_event_end_date', $event[2] );
			}
			update_post_meta( $post_id, 'rbc68_event_kind', $event[3] );
		}
	}
	update_option( 'rbc68_events_seeded_130', 1 );
}
add_action( 'admin_init', 'rbc68_seed_events' );

function rbc68_maybe_flush_rewrites() {
	if ( RBC68_VERSION !== get_option( 'rbc68_rewrite_version' ) ) {
		flush_rewrite_rules( false );
		update_option( 'rbc68_rewrite_version', RBC68_VERSION );
	}
}
add_action( 'admin_init', 'rbc68_maybe_flush_rewrites', 20 );

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
		'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %1$d %2$d"><rect width="%1$d" height="%2$d" fill="%3$s"/><svg width="%1$d" height="%2$d" viewBox="0 0 400 240"><g fill="none" stroke="#cda445" stroke-width="2" opacity=".35"><path d="M20 205 Q120 230 190 170M15 190 Q115 215 175 165M285 35l12 4-8 10M310 175l9 4-5 9"/><circle cx="68" cy="60" r="25"/><circle cx="335" cy="85" r="42"/></g><g fill="none" stroke="%4$s" stroke-linecap="round" stroke-width="5" transform="rotate(-32 155 120)"><ellipse cx="155" cy="87" rx="35" ry="47"/><path d="M155 134v64"/><path stroke-width="9" d="M155 176v28"/><path stroke-width="1.3" opacity=".65" d="M130 57v60m12-72v85m13-90v94m13-88v82m12-70v57m-56-45h62m-65 14h68m-67 14h65m-59 14h51"/></g><g transform="translate(248 117) rotate(25)" stroke="%4$s" stroke-width="3" stroke-linejoin="round"><path fill="none" d="M-12 18L-35-40Q0-54 35-40L12 18M-24-44L-6 18M-12-47L0 18M0-48L6 18M12-47L9 18M24-44L12 18"/><path fill="%4$s" d="M-12 18h24v8a12 12 0 0 1-24 0z"/></g></svg></svg>',
		$width,
		$height,
		$background ?: '#182d52',
		$foreground ?: '#fefefe',
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
		'rbc68_hero_text'  => array( 'Texte d’introduction', 'Badminton loisir et compétition pour les adultes, les jeunes et le mini-bad, au complexe sportif C.M.C.A.S de Rixheim.' ),
		'rbc68_email'      => array( 'Adresse e-mail affichée et destinataire du formulaire', 'contact@rbc68.fr' ),
		'rbc68_address'    => array( 'Adresse du club', 'Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim' ),
		'rbc68_phone'      => array( 'Téléphone du club', '06 19 67 33 34' ),
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

	$image_settings = array(
		'rbc68_hero_image' => 'Photo horizontale du bandeau d’accueil',
		'rbc68_club_image' => 'Photo d’illustration de la section « Le club »',
		'rbc68_gallery_1'  => 'Première photo de la galerie',
		'rbc68_gallery_2'  => 'Seconde photo de la galerie',
	);

	foreach ( $image_settings as $setting => $label ) {
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

	$customizer->add_section(
		'rbc68_essentials',
		array(
			'title'       => __( 'Informations pratiques', 'rbc68' ),
			'description' => __( 'Contenu des quatre cartes affichées sous le bandeau.', 'rbc68' ),
			'priority'    => 31,
		)
	);

	$essential_settings = array(
		'rbc68_map_link' => array( 'Lien pour ouvrir l’itinéraire', 'https://www.google.com/maps/dir/?api=1&destination=47.7331928%2C7.3777678', 'url', 'esc_url_raw' ),
		'rbc68_map_embed' => array( 'Adresse de la mini-carte OpenStreetMap', 'https://www.openstreetmap.org/export/embed.html?bbox=7.3705%2C47.7295%2C7.3850%2C47.7370&layer=mapnik&marker=47.7331928%2C7.3777678', 'url', 'esc_url_raw' ),
		'rbc68_room_info' => array( 'Lien vers les informations sur la salle', '#club', 'text', 'sanitize_text_field' ),
		'rbc68_when_adults' => array( 'Créneaux Adultes (résumé)', 'Mardi, mercredi, vendredi et dimanche', 'text', 'sanitize_text_field' ),
		'rbc68_when_youth' => array( 'Créneaux Jeunes (résumé)', 'Mardi, vendredi et dimanche', 'text', 'sanitize_text_field' ),
		'rbc68_when_mini' => array( 'Créneaux Mini-bad (résumé)', 'Vendredi et dimanche', 'text', 'sanitize_text_field' ),
		'rbc68_price_mini' => array( 'Tarif Mini-bad', '70 €', 'text', 'sanitize_text_field' ),
		'rbc68_price_youth' => array( 'Tarif Jeunes', '85 €', 'text', 'sanitize_text_field' ),
		'rbc68_price_adults' => array( 'Tarif Adultes', '105 €', 'text', 'sanitize_text_field' ),
		'rbc68_join_text' => array( 'Résumé « Nous rejoindre »', 'Nouvelles inscriptions sur place, auprès du responsable de salle. Reprise le 1er septembre 2026.', 'textarea', 'sanitize_textarea_field' ),
	);

	foreach ( $essential_settings as $setting => $details ) {
		$customizer->add_setting( $setting, array( 'default' => $details[1], 'sanitize_callback' => $details[3] ) );
		$customizer->add_control(
			$setting,
			array(
				'label'   => __( $details[0], 'rbc68' ),
				'section' => 'rbc68_essentials',
				'type'    => $details[2],
			)
		);
	}

	foreach ( array( 'mini' => 'Mini-bad', 'youth' => 'Jeunes', 'adults' => 'Adultes' ) as $key => $label ) {
		$setting = 'rbc68_availability_' . $key;
		$customizer->add_setting( $setting, array( 'default' => 'available', 'sanitize_callback' => 'rbc68_sanitize_availability' ) );
		$customizer->add_control(
			$setting,
			array(
				'label'   => sprintf( __( 'Disponibilité — %s', 'rbc68' ), $label ),
				'section' => 'rbc68_essentials',
				'type'    => 'select',
				'choices' => array( 'available' => __( 'Places disponibles — aucune pastille', 'rbc68' ), 'full' => __( 'Complet', 'rbc68' ) ),
			)
		);
	}

	$customizer->add_section(
		'rbc68_announcement',
		array(
			'title'       => __( 'Bannière d’information', 'rbc68' ),
			'description' => __( 'Laissez le texte vide pour masquer la bannière.', 'rbc68' ),
			'priority'    => 29,
		)
	);
	$announcement_settings = array(
		'rbc68_announcement_text' => array( 'Message', '', 'textarea', 'sanitize_textarea_field' ),
		'rbc68_announcement_link' => array( 'Lien facultatif', '', 'url', 'esc_url_raw' ),
		'rbc68_announcement_label' => array( 'Texte du lien', 'En savoir plus', 'text', 'sanitize_text_field' ),
	);
	foreach ( $announcement_settings as $setting => $details ) {
		$customizer->add_setting( $setting, array( 'default' => $details[1], 'sanitize_callback' => $details[3] ) );
		$customizer->add_control( $setting, array( 'label' => __( $details[0], 'rbc68' ), 'section' => 'rbc68_announcement', 'type' => $details[2] ) );
	}
}
add_action( 'customize_register', 'rbc68_customizer' );

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
		'inscription'   => 'Nous rejoindre',
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
