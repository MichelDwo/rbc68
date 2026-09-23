<?php
/** Fiche événement, affiche et bilan facultatif. */
defined( 'ABSPATH' ) || exit;

function rbc68_event_content_labels() {
	return array( 'program' => 'Programme', 'price' => 'Tarif', 'registration' => 'Modalités d’inscription', 'refreshments' => 'Restauration' );
}

/** Retrouve aussi les brouillons et les articles à la corbeille. */
function rbc68_event_report_id( $id ) {
	$report_id = (int) get_post_meta( $id, 'rbc68_event_report_id', true );
	$report = $report_id ? get_post( $report_id ) : null;
	if ( $report && 'post' === $report->post_type ) { return $report->ID; }
	$ids = get_posts( array(
		'post_type' => 'post', 'post_status' => array_keys( get_post_stati() ), 'numberposts' => 1,
		'fields' => 'ids', 'meta_key' => 'rbc68_report_event_id', 'meta_value' => $id,
	) );
	if ( $ids ) { return (int) $ids[0]; }
	$legacy = get_post_meta( $id, 'rbc68_event_article_url', true );
	$legacy_id = $legacy ? url_to_postid( $legacy ) : 0;
	$report = $legacy_id ? get_post( $legacy_id ) : null;
	return $report && 'post' === $report->post_type ? $report->ID : 0;
}

function rbc68_event_report_url( $id ) {
	$report = rbc68_event_report_id( $id );
	if ( $report ) {
		$post = get_post( $report );
		return 'publish' === $post->post_status && ! $post->post_password ? get_permalink( $report ) : '';
	}
	// Compatibilité avec les liens externes déjà saisis.
	$url = get_post_meta( $id, 'rbc68_event_article_url', true );
	return $url && wp_parse_url( $url, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ? $url : '';
}

function rbc68_event_content_fields( $post ) {
	?>
	<p class="description">Toutes les informations sont présentées sur la fiche de l’événement. Une actualité n’est utile que pour le bilan.</p>

	<p><label for="rbc68_event_registration_url"><strong>Lien d’inscription</strong> (facultatif)</label><input class="widefat" type="url" id="rbc68_event_registration_url" name="rbc68_event_registration_url" value="<?php echo esc_attr( get_post_meta( $post->ID, 'rbc68_event_registration_url', true ) ); ?>"></p>
	<h3>Affiche</h3>
	<?php $poster = absint( get_post_meta( $post->ID, 'rbc68_event_poster_id', true ) ); ?>
	<input type="hidden" id="rbc68_event_poster_id" name="rbc68_event_poster_id" value="<?php echo esc_attr( $poster ); ?>">
	<p id="rbc68-poster-name"><?php echo esc_html( $poster ? get_the_title( $poster ) : 'Aucune affiche' ); ?></p>
	<button type="button" class="button" id="rbc68-select-poster">Choisir une image ou un PDF</button>
	<button type="button" class="button" id="rbc68-remove-poster">Retirer</button>
	<p class="description">Affichée sur la fiche, téléchargeable et partageable. Une image permet aussi un aperçu sur les réseaux sociaux.</p>
	<h3>Article bilan (facultatif)</h3>
	<?php $report = rbc68_event_report_id( $post->ID ); $legacy = get_post_meta( $post->ID, 'rbc68_event_article_url', true ); ?>
	<?php if ( $report ) : ?>
		<p>Un bilan existe déjà : <strong><?php echo esc_html( get_the_title( $report ) ); ?></strong> — <?php echo esc_html( get_post_status_object( get_post_status( $report ) )->label ); ?>.</p>
		<?php if ( current_user_can( 'edit_post', $report ) ) : ?><p><a class="button" href="<?php echo esc_url( 'trash' === get_post_status( $report ) ? admin_url( 'edit.php?post_status=trash&post_type=post' ) : get_edit_post_link( $report ) ); ?>"><?php echo 'trash' === get_post_status( $report ) ? 'Retrouver le bilan dans la corbeille' : 'Modifier le bilan'; ?></a></p><?php endif; ?>
	<?php elseif ( $legacy ) : ?>
		<p>Un article est déjà lié : <a href="<?php echo esc_url( $legacy ); ?>"><?php echo esc_html( $legacy ); ?></a>.</p>
	<?php else : ?>
		<p><label for="rbc68_event_existing_report">Lier un article existant</label><br><select class="widefat" id="rbc68_event_existing_report" name="rbc68_event_existing_report"><option value="">Aucun</option>
		<?php foreach ( get_posts( array( 'post_type' => 'post', 'post_status' => array( 'publish', 'draft', 'pending', 'future', 'private' ), 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC' ) ) as $article ) : if ( ! current_user_can( 'edit_post', $article->ID ) || get_post_meta( $article->ID, 'rbc68_report_event_id', true ) ) { continue; } ?>
			<option value="<?php echo esc_attr( $article->ID ); ?>"><?php echo esc_html( $article->post_title . ' — ' . mysql2date( 'd/m/Y', $article->post_date ) ); ?></option>
		<?php endforeach; ?></select></p>
		<?php if ( current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) : ?><p><button class="button button-secondary" type="submit" name="rbc68_create_report" value="1">Enregistrer et créer le brouillon du bilan</button></p><?php endif; ?>
		<p class="description">Le brouillon porte le nom de l’événement. Si vous choisissez un article existant, il sera utilisé sans créer de brouillon. Le lien public apparaît uniquement après publication du bilan.</p>
	<?php endif;
}

/** Éditeurs hors des boîtes déplaçables : TinyMCE doit rester fixe dans le DOM. */
function rbc68_event_rich_editors( $post ) {
	if ( 'rbc68_event' !== $post->post_type ) { return; }
	echo '<div class="rbc68-event-editors"><h2>Textes de l’événement</h2><p>Utilisez « Ajouter un média » pour insérer des photos. L’annonce courte reste en texte simple.</p>';
	foreach ( rbc68_event_content_labels() as $key => $label ) {
		$name = 'rbc68_event_' . $key;
		echo '<h3><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label></h3>';
		wp_editor( get_post_meta( $post->ID, $name, true ), $name, array(
			'textarea_name' => $name,
			'media_buttons' => current_user_can( 'upload_files' ),
			'editor_height' => 'program' === $key ? 300 : 180,
			'quicktags' => true,
			'tinymce' => array(
				'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,undo,redo,removeformat',
				'toolbar2' => '',
				'block_formats' => 'Paragraphe=p;Titre 3=h3;Titre 4=h4;Titre 5=h5',
			),
		) );
	}
	echo '</div>';
}
add_action( 'edit_form_advanced', 'rbc68_event_rich_editors' );

function rbc68_save_event_content( $id ) {
	foreach ( rbc68_event_content_labels() as $key => $label ) {
		$name = 'rbc68_event_' . $key;
		if ( isset( $_POST[ $name ] ) && is_string( $_POST[ $name ] ) ) { update_post_meta( $id, $name, wp_slash( wp_kses_post( wp_unslash( $_POST[ $name ] ) ) ) ); }
	}
	if ( isset( $_POST['rbc68_event_registration_url'] ) && is_string( $_POST['rbc68_event_registration_url'] ) ) {
		update_post_meta( $id, 'rbc68_event_registration_url', esc_url_raw( wp_unslash( $_POST['rbc68_event_registration_url'] ), array( 'https', 'http' ) ) );
	}
	if ( isset( $_POST['rbc68_event_poster_id'] ) && is_scalar( $_POST['rbc68_event_poster_id'] ) ) {
		$poster = absint( $_POST['rbc68_event_poster_id'] );
		if ( ! $poster || rbc68_event_valid_poster( $poster ) ) { update_post_meta( $id, 'rbc68_event_poster_id', $poster ); }
	}
	$selected = isset( $_POST['rbc68_event_existing_report'] ) && is_scalar( $_POST['rbc68_event_existing_report'] ) ? absint( $_POST['rbc68_event_existing_report'] ) : 0;
	if ( $selected || ! empty( $_POST['rbc68_create_report'] ) ) {
		$result = rbc68_ensure_event_report( $id, $selected );
		if ( is_wp_error( $result ) ) { set_transient( 'rbc68_report_error_' . get_current_user_id(), $result->get_error_message(), 60 ); }
	}
}

/** INSERT IGNORE s’appuie sur l’index unique, sans écraser un verrou concurrent. */
function rbc68_report_lock( $key ) {
	global $wpdb;
	return 1 === $wpdb->query( $wpdb->prepare(
		"INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, '1', 'no')", $key
	) );
}
function rbc68_report_unlock( $key ) {
	global $wpdb;
	$wpdb->delete( $wpdb->options, array( 'option_name' => $key ), array( '%s' ) );
}

/** Verrou unique en base : deux clics/requêtes ne créent jamais deux brouillons. */
function rbc68_ensure_event_report( $id, $selected = 0 ) {
	$event = get_post( $id );
	if ( ! $event || 'rbc68_event' !== $event->post_type || ! current_user_can( 'edit_post', $id ) || ! current_user_can( get_post_type_object( 'post' )->cap->create_posts ) ) {
		return new WP_Error( 'forbidden', 'Création du bilan non autorisée.' );
	}
	$lock = 'rbc68_report_lock_' . $id;
	if ( ! rbc68_report_lock( $lock ) ) { return new WP_Error( 'busy', 'La création du bilan est déjà en cours. Rechargez la page.' ); }
	try {
		$existing = rbc68_event_report_id( $id );
		if ( $existing ) { return $existing; }
		if ( get_post_meta( $id, 'rbc68_event_article_url', true ) ) { return new WP_Error( 'linked', 'Un article est déjà lié à cet événement.' ); }
		if ( $selected ) {
			$article = get_post( $selected );
			if ( ! $article || 'post' !== $article->post_type || ! current_user_can( 'edit_post', $selected ) || get_post_meta( $selected, 'rbc68_report_event_id', true ) ) {
				return new WP_Error( 'invalid', 'Cet article ne peut pas être lié à cet événement.' );
			}
			// Un article ne peut être affecté simultanément à deux événements.
			if ( ! rbc68_report_lock( 'rbc68_report_article_lock_' . $selected ) ) { return new WP_Error( 'busy', 'Cet article est déjà en cours de liaison.' ); }
			try {
				if ( get_post_meta( $selected, 'rbc68_report_event_id', true ) ) { return new WP_Error( 'linked', 'Cet article est déjà lié.' ); }
				update_post_meta( $selected, 'rbc68_report_event_id', $id );
				update_post_meta( $id, 'rbc68_event_report_id', $selected );
			} finally { rbc68_report_unlock( 'rbc68_report_article_lock_' . $selected ); }
			return $selected;
		}
		$report = wp_insert_post( wp_slash( array(
			'post_type' => 'post', 'post_status' => 'draft', 'post_title' => $event->post_title,
			'post_author' => get_current_user_id(), 'post_content' => '',
			'meta_input' => array( 'rbc68_report_event_id' => $id ),
		) ), true );
		if ( is_wp_error( $report ) ) { return $report; }
		if ( ! $report ) { return new WP_Error( 'failed', 'Impossible de créer le bilan. Réessayez.' ); }
		update_post_meta( $id, 'rbc68_event_report_id', $report );
		return $report;
	} finally { rbc68_report_unlock( $lock ); }
}

function rbc68_report_notice() {
	$key = 'rbc68_report_error_' . get_current_user_id();
	$message = get_transient( $key );
	if ( $message ) { delete_transient( $key ); echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>'; }
}
add_action( 'admin_notices', 'rbc68_report_notice' );

function rbc68_event_valid_poster( $id ) {
	return 'attachment' === get_post_type( $id ) && ( wp_attachment_is_image( $id ) || 'application/pdf' === get_post_mime_type( $id ) );
}

function rbc68_event_content_assets() {
	$screen = get_current_screen();
	if ( $screen && 'rbc68_event' === $screen->post_type && 'post' === $screen->base ) {
		wp_enqueue_media();
		wp_enqueue_script( 'rbc68-event-admin', get_template_directory_uri() . '/assets/js/event-admin.js', array( 'media-views' ), RBC68_VERSION, true );
	}
}
add_action( 'admin_enqueue_scripts', 'rbc68_event_content_assets' );

function rbc68_event_content_display( $id ) {
	foreach ( rbc68_event_content_labels() as $key => $label ) {
		$value = get_post_meta( $id, 'rbc68_event_' . $key, true );
		if ( $value ) { echo '<section class="event-section"><h2>' . esc_html( $label ) . '</h2>' . '<div class="event-rich-text">' . wpautop( wp_kses_post( $value ) ) . '</div></section>'; }
	}
	$url = get_post_meta( $id, 'rbc68_event_registration_url', true );
	if ( $url && ! rbc68_event_is_past( $id ) ) { echo '<p><a class="event-action" href="' . esc_url( $url ) . '">S’inscrire →</a></p>'; }
	$poster = absint( get_post_meta( $id, 'rbc68_event_poster_id', true ) );
	$has_poster = $poster && rbc68_event_valid_poster( $poster );
	if ( $has_poster ) : $file_url = wp_get_attachment_url( $poster ); ?>
		<section class="event-section event-poster"><h2>Affiche</h2>
		<?php if ( wp_attachment_is_image( $poster ) ) : ?>
			<a href="<?php echo esc_url( $file_url ); ?>"><?php echo wp_get_attachment_image( $poster, 'large', false, array( 'alt' => 'Affiche — ' . get_the_title( $id ) ) ); ?></a>
		<?php else : ?>
			<object type="application/pdf" data="<?php echo esc_url( $file_url ); ?>"><p><a href="<?php echo esc_url( $file_url ); ?>">Ouvrir l’affiche PDF</a></p></object>
		<?php endif; ?>
		<?php rbc68_event_share_controls( $id, $file_url ); ?>
		</section>
	<?php endif;
	$report_url = rbc68_event_report_url( $id );
	if ( $report_url ) { echo '<p class="event-report"><a href="' . esc_url( $report_url ) . '">Voir le bilan et les photos →</a></p>'; }
	if ( ! $has_poster ) { rbc68_event_share_controls( $id ); }
}

function rbc68_event_share_controls( $id, $file_url = '' ) {
	$url = get_permalink( $id ); $title = get_the_title( $id ); ?>
	<div data-event-actions>
	<div class="event-share" data-event-url="<?php echo esc_url( $url ); ?>" data-event-title="<?php echo esc_attr( $title ); ?>">
		<button type="button" class="event-action" data-share-event hidden>Partager l’événement</button>
		<?php if ( $file_url ) : ?><a class="event-action" href="<?php echo esc_url( $file_url ); ?>" download>Télécharger l’affiche</a><?php endif; ?>
	</div><p class="event-share-status" role="status"></p>
	<p data-copy-fallback hidden><label>Lien à copier <input type="text" readonly value="<?php echo esc_url( $url ); ?>" data-copy-url></label></p>
	<noscript><p>Lien de l’événement : <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $url ); ?></a></p></noscript>
	</div>
	<?php
}

function rbc68_report_event_link( $id ) {
	$event_id = (int) get_post_meta( $id, 'rbc68_report_event_id', true );
	$event = $event_id ? get_post( $event_id ) : null;
	if ( $event && 'rbc68_event' === $event->post_type && in_array( $event->post_status, array( 'publish', 'rbc68_archived' ), true ) && ! $event->post_password ) {
		echo '<p><a href="' . esc_url( get_permalink( $event ) ) . '">Retrouver la fiche de l’événement →</a></p>';
	}
}

function rbc68_event_share_assets() {
	if ( is_singular( 'rbc68_event' ) ) { wp_enqueue_script( 'rbc68-event-share', get_template_directory_uri() . '/assets/js/event-share.js', array(), RBC68_VERSION . '.' . filemtime( get_template_directory() . '/assets/js/event-share.js' ), true ); }
}
add_action( 'wp_enqueue_scripts', 'rbc68_event_share_assets' );

function rbc68_event_social_meta() {
	if ( ! is_singular( 'rbc68_event' ) || post_password_required() ) { return; }
	$id = get_queried_object_id();
	$meta = array( 'og:type' => 'website', 'og:title' => get_the_title( $id ), 'og:url' => get_permalink( $id ), 'og:description' => get_post_meta( $id, 'rbc68_event_details', true ) );
	$poster = absint( get_post_meta( $id, 'rbc68_event_poster_id', true ) );
	$image = $poster ? wp_get_attachment_image_url( $poster, 'full' ) : false;
	if ( $image ) { $meta['og:image'] = $image; $meta['og:image:alt'] = 'Affiche — ' . get_the_title( $id ); }
	foreach ( $meta as $property => $value ) { if ( $value ) { echo '<meta property="' . esc_attr( $property ) . '" content="' . esc_attr( wp_strip_all_tags( $value ) ) . '">' . "\n"; } }
}
add_action( 'wp_head', 'rbc68_event_social_meta' );

/** Lien d’itinéraire : coordonnées en priorité, sinon adresse. */
function rbc68_event_directions_url( $id ) {
	$lat = get_post_meta( $id, 'rbc68_event_latitude', true );
	$lon = get_post_meta( $id, 'rbc68_event_longitude', true );
	$destination = get_post_meta( $id, 'rbc68_event_location', true );
	if ( is_numeric( $lat ) && is_numeric( $lon ) && abs( (float) $lat ) <= 90 && abs( (float) $lon ) <= 180 ) {
		$destination = (float) $lat . ',' . (float) $lon;
	}
	return $destination ? 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $destination ) : '';
}
