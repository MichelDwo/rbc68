<?php
/** Gestion chronologique, archives et duplication des événements. */
defined( 'ABSPATH' ) || exit;

function rbc68_event_archive_status() {
	register_post_status( 'rbc68_archived', array(
		'label' => 'Archivé', 'public' => true,
		'show_in_admin_all_list' => true, 'show_in_admin_status_list' => true,
		'label_count' => _n_noop( 'Archives <span class="count">(%s)</span>', 'Archives <span class="count">(%s)</span>', 'rbc68' ),
	) );
	if ( ! wp_next_scheduled( 'rbc68_archive_events' ) ) {
		wp_schedule_event( time(), 'hourly', 'rbc68_archive_events' );
	}
}
add_action( 'init', 'rbc68_event_archive_status' );

function rbc68_event_archive_cleanup() { wp_clear_scheduled_hook( 'rbc68_archive_events' ); }
add_action( 'switch_theme', 'rbc68_event_archive_cleanup' );

function rbc68_sync_event_archive( $id ) {
	static $updating = false;
	$post = get_post( $id );
	if ( $updating || ! $post || 'rbc68_event' !== $post->post_type || ! in_array( $post->post_status, array( 'publish', 'rbc68_archived' ), true ) ) {
		return;
	}
	$status = rbc68_event_is_past( $id ) ? 'rbc68_archived' : 'publish';
	if ( $status !== $post->post_status ) {
		$updating = true;
		wp_update_post( array( 'ID' => $id, 'post_status' => $status ) );
		$updating = false;
	}
}
// Après la sauvegarde des champs pratiques, y compris une nouvelle date.
add_action( 'save_post_rbc68_event', 'rbc68_sync_event_archive', 30 );

function rbc68_archive_events() {
	$ids = get_posts( array( 'post_type' => 'rbc68_event', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids' ) );
	foreach ( $ids as $id ) { rbc68_sync_event_archive( $id ); }
}
add_action( 'rbc68_archive_events', 'rbc68_archive_events' );
function rbc68_archive_events_on_admin() {
	$screen = get_current_screen();
	if ( $screen && 'rbc68_event' === $screen->post_type && current_user_can( 'edit_posts' ) ) { rbc68_archive_events(); }
}
add_action( 'load-edit.php', 'rbc68_archive_events_on_admin' );

function rbc68_event_admin_columns( $columns ) {
	$columns['rbc68_when'] = 'Date de l’événement';
	return $columns;
}
add_filter( 'manage_rbc68_event_posts_columns', 'rbc68_event_admin_columns' );
function rbc68_event_admin_column( $column, $id ) {
	if ( 'rbc68_when' === $column ) { echo esc_html( rbc68_event_when_label( $id ) ?: 'Date à préciser' ); }
}
add_action( 'manage_rbc68_event_posts_custom_column', 'rbc68_event_admin_column', 10, 2 );
function rbc68_event_sortable_columns( $columns ) { $columns['rbc68_when'] = 'rbc68_when'; return $columns; }
add_filter( 'manage_edit-rbc68_event_sortable_columns', 'rbc68_event_sortable_columns' );

/** Sous-requêtes : les brouillons sans date restent visibles. */
function rbc68_event_admin_order( $clauses, $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'rbc68_event' !== $query->get( 'post_type' ) || ( $query->get( 'orderby' ) && 'rbc68_when' !== $query->get( 'orderby' ) ) ) { return $clauses; }
	global $wpdb;
	$direction = 'rbc68_when' === $query->get( 'orderby' ) && 'DESC' === strtoupper( $query->get( 'order' ) ) ? 'DESC' : 'ASC';
	$date = "(SELECT MIN(em.meta_value) FROM {$wpdb->postmeta} em WHERE em.post_id = {$wpdb->posts}.ID AND em.meta_key = 'rbc68_event_date')";
	$time = "(SELECT MIN(et.meta_value) FROM {$wpdb->postmeta} et WHERE et.post_id = {$wpdb->posts}.ID AND et.meta_key = 'rbc68_event_start_time')";
	$clauses['orderby'] = "CASE WHEN {$date} IS NULL OR {$date} = '' THEN 1 ELSE 0 END ASC, {$date} {$direction}, {$time} {$direction}, {$wpdb->posts}.ID ASC";
	return $clauses;
}
add_filter( 'posts_clauses', 'rbc68_event_admin_order', 10, 2 );

function rbc68_event_post_states( $states, $post ) {
	if ( 'rbc68_event' === $post->post_type && 'rbc68_archived' === $post->post_status ) { $states['rbc68_archived'] = 'Archivé'; }
	return $states;
}
add_filter( 'display_post_states', 'rbc68_event_post_states', 10, 2 );

function rbc68_duplicate_event_action( $actions, $post ) {
	$type = get_post_type_object( 'rbc68_event' );
	if ( 'rbc68_event' === $post->post_type && current_user_can( 'edit_post', $post->ID ) && current_user_can( $type->cap->create_posts ) ) {
		$url = wp_nonce_url( add_query_arg( array( 'action' => 'rbc68_duplicate_event', 'post' => $post->ID ), admin_url( 'admin-post.php' ) ), 'rbc68_duplicate_event_' . $post->ID );
		$actions['rbc68_duplicate'] = '<a href="' . esc_url( $url ) . '">Dupliquer</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'rbc68_duplicate_event_action', 10, 2 );

function rbc68_duplicate_event() {
	$id = isset( $_GET['post'] ) && is_string( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	check_admin_referer( 'rbc68_duplicate_event_' . $id );
	$post = get_post( $id );
	$type = get_post_type_object( 'rbc68_event' );
	if ( ! $post || 'rbc68_event' !== $post->post_type || ! current_user_can( 'edit_post', $id ) || ! current_user_can( $type->cap->create_posts ) ) {
		wp_die( 'Duplication non autorisée.', '', array( 'response' => 403 ) );
	}
	$meta = array();
	foreach ( get_post_meta( $id ) as $key => $values ) {
		if ( 0 === strpos( $key, 'rbc68_event_' ) && ! in_array( $key, array( 'rbc68_event_report_id', 'rbc68_event_article_url' ), true ) ) { $meta[ $key ] = get_post_meta( $id, $key, true ); }
	}
	$copy = wp_insert_post( wp_slash( array(
		'post_type' => 'rbc68_event', 'post_status' => 'draft', 'post_author' => get_current_user_id(),
		'post_title' => $post->post_title . ' — dupliqué', 'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt, 'meta_input' => $meta,
	) ), true );
	if ( is_wp_error( $copy ) || ! $copy ) { wp_die( 'Impossible de dupliquer cet événement.' ); }
	wp_safe_redirect( get_edit_post_link( $copy, 'raw' ) );
	exit;
}
add_action( 'admin_post_rbc68_duplicate_event', 'rbc68_duplicate_event' );

function rbc68_event_status_editor() {
	$screen = get_current_screen();
	if ( ! $screen || 'rbc68_event' !== $screen->post_type ) { return; }
	global $post;
	$archived = $post && 'rbc68_archived' === $post->post_status;
	?>
	<script>
	document.querySelectorAll('select[name="post_status"], select[name="_status"]').forEach(function (select) {
		if (!select.querySelector('option[value="rbc68_archived"]')) select.add(new Option('Archivé', 'rbc68_archived'));
	});
	<?php if ( $archived && 'post' === $screen->base ) : ?>
	document.getElementById('post_status').value = 'rbc68_archived';
	document.getElementById('post-status-display').textContent = 'Archivé';
	<?php endif; ?>
	</script>
	<?php
}
add_action( 'admin_footer', 'rbc68_event_status_editor' );
