<?php
/** Membres du comité, administrables dans WordPress. */
defined( 'ABSPATH' ) || exit;

function rbc68_register_committee() {
	register_post_type( 'rbc68_member', array(
		'labels' => array(
			'name' => 'Comité', 'singular_name' => 'Membre du comité',
			'add_new' => 'Ajouter un membre', 'add_new_item' => 'Ajouter un membre du comité',
			'edit_item' => 'Modifier le membre', 'new_item' => 'Nouveau membre',
			'all_items' => 'Tous les membres', 'search_items' => 'Rechercher un membre',
			'not_found' => 'Aucun membre trouvé', 'not_found_in_trash' => 'Aucun membre dans la corbeille',
		),
		'public' => false, 'show_ui' => true, 'show_in_rest' => false,
		'menu_icon' => 'dashicons-groups', 'supports' => array( 'title' ),
		'rewrite' => false, 'query_var' => false,
	) );
}
add_action( 'init', 'rbc68_register_committee' );

function rbc68_member_title_placeholder( $title, $post ) {
	return 'rbc68_member' === $post->post_type ? 'Nom de famille' : $title;
}
add_filter( 'enter_title_here', 'rbc68_member_title_placeholder', 10, 2 );

function rbc68_member_meta_boxes() {
	add_meta_box( 'rbc68_member_details', 'Informations du membre', 'rbc68_member_fields', 'rbc68_member', 'normal', 'high' );
}
add_action( 'add_meta_boxes_rbc68_member', 'rbc68_member_meta_boxes' );

function rbc68_member_fields( $post ) {
	wp_nonce_field( 'rbc68_save_member', 'rbc68_member_nonce' );
	foreach ( array( 'first_name' => 'Prénom', 'role' => 'Rôle (texte libre)' ) as $key => $label ) {
		$name = 'rbc68_member_' . $key;
		echo '<p><label for="' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label><br>';
		echo '<input class="widefat" type="text" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( get_post_meta( $post->ID, $name, true ) ) . '"></p>';
	}
	echo '<p>Indiquez le nom de famille dans le titre. Les membres sont classés par nom de famille. Seuls les membres publiés apparaissent sur le site.</p>';
}

function rbc68_save_member( $post_id ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['rbc68_member_nonce'] ) || ! is_string( $_POST['rbc68_member_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rbc68_member_nonce'] ) ), 'rbc68_save_member' ) ) {
		return;
	}
	foreach ( array( 'first_name', 'role' ) as $key ) {
		$name = 'rbc68_member_' . $key;
		if ( isset( $_POST[ $name ] ) && is_string( $_POST[ $name ] ) ) {
			update_post_meta( $post_id, $name, sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) );
		}
	}
}
add_action( 'save_post_rbc68_member', 'rbc68_save_member' );

function rbc68_member_columns( $columns ) {
	$columns['title'] = 'Nom de famille';
	$columns['rbc68_member_first_name'] = 'Prénom';
	$columns['rbc68_member_role'] = 'Rôle';
	return $columns;
}
add_filter( 'manage_rbc68_member_posts_columns', 'rbc68_member_columns' );

function rbc68_member_column( $column, $post_id ) {
	if ( in_array( $column, array( 'rbc68_member_first_name', 'rbc68_member_role' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
	}
}
add_action( 'manage_rbc68_member_posts_custom_column', 'rbc68_member_column', 10, 2 );

function rbc68_member_admin_order( $query ) {
	if ( is_admin() && $query->is_main_query() && 'rbc68_member' === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'rbc68_member_admin_order' );

function rbc68_get_committee() {
	$members = get_posts( array( 'post_type' => 'rbc68_member', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	usort( $members, function ( $a, $b ) {
		$a_name = remove_accents( $a->post_title . ' ' . get_post_meta( $a->ID, 'rbc68_member_first_name', true ) );
		$b_name = remove_accents( $b->post_title . ' ' . get_post_meta( $b->ID, 'rbc68_member_first_name', true ) );
		return strcasecmp( $a_name, $b_name );
	} );
	return $members;
}

/** Migration unique : conserve les membres de l'ancien thème, sans fallback à l'affichage. */
function rbc68_migrate_committee() {
	if ( ! current_user_can( 'manage_options' ) || get_option( 'rbc68_committee_migrated' ) ) {
		return;
	}
	$members = array(
		'geiger-julien' => array( 'GEIGER', 'Julien', 'Président' ),
		'walter-yves' => array( 'WALTER', 'Yves', 'Vice-Président' ),
		'baumann-rachel' => array( 'BAUMANN', 'Rachel', 'Secrétaire' ),
		'denni-jean-christophe' => array( 'DENNI', 'Jean-Christophe', 'Trésorier' ),
		'notter-pierre' => array( 'NOTTER', 'Pierre', 'Directeur Technique' ),
	);
	$imported = get_option( 'rbc68_committee_imported', array() );
	foreach ( $members as $key => $member ) {
		if ( isset( $imported[ $key ] ) ) {
			continue;
		}
		$id = wp_insert_post( array(
			'post_type' => 'rbc68_member', 'post_status' => 'publish', 'post_title' => $member[0],
			'meta_input' => array( 'rbc68_member_first_name' => $member[1], 'rbc68_member_role' => $member[2] ),
		), true );
		if ( is_wp_error( $id ) || ! $id ) {
			return;
		}
		$imported[ $key ] = $id;
		update_option( 'rbc68_committee_imported', $imported, false );
	}
	update_option( 'rbc68_committee_migrated', 1, false );
}
add_action( 'admin_init', 'rbc68_migrate_committee' );
