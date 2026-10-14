<?php
/** Cadrage non destructif de l’image à la une des actualités. */
defined( 'ABSPATH' ) || exit;
function rbc68_news_image_position( $value ) {
	return in_array( $value, array( 'full', 'left', 'center', 'right' ), true ) ? $value : 'center';
}
add_action( 'add_meta_boxes_post', function() {
	add_meta_box( 'rbc68_news_crop', 'Cadrage de l’illustration', 'rbc68_news_image_fields', 'post', 'side' );
} );
function rbc68_news_image_fields( $post ) {
	wp_nonce_field( 'rbc68_news_crop', 'rbc68_news_crop_nonce' );
	$position = rbc68_news_image_position( get_post_meta( $post->ID, '_rbc68_news_crop', true ) );
	echo '<p>Affichez l’image entière à la largeur disponible, ou choisissez la zone à conserver lors du recadrage. Ce choix s’applique aux tuiles et à l’illustration en tête d’article. Le fichier original reste intact.</p><div class="rbc68-crop-control">';
	foreach ( array( 'full'=>'Image entière, sans recadrage', 'left'=>'Gauche', 'center'=>'Centre', 'right'=>'Droite' ) as $key=>$label ) {
		echo '<p><label><input type="radio" name="rbc68_news_crop" value="' . esc_attr( $key ) . '" ' . checked( $position, $key, false ) . '> ' . esc_html( $label ) . '</label></p>';
	}
	if ( has_post_thumbnail( $post ) ) {
		echo '<p>Aperçu du cadrage (le cadre varie selon l’écran) :</p>';
		echo get_the_post_thumbnail( $post, 'full', array( 'class'=>'rbc68-crop-preview', 'style'=>'display:block;width:100%;height:' . ( 'full' === $position ? 'auto' : '140px' ) . ';object-fit:' . ( 'full' === $position ? 'contain' : 'cover' ) . ';object-position:' . ( 'full' === $position ? 'center' : $position ) . ' center;padding:8px;box-sizing:border-box;' ) );
	} else { echo '<p>Choisissez une image à la une, puis enregistrez l’article pour afficher l’aperçu.</p>'; }
	echo '</div>';
}
add_action( 'save_post_post', function( $id ) {
	if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || wp_is_post_revision($id) || !current_user_can('edit_post',$id) || !isset($_POST['rbc68_news_crop_nonce']) || !is_string($_POST['rbc68_news_crop_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rbc68_news_crop_nonce'])),'rbc68_news_crop') ) { return; }
	if ( isset($_POST['rbc68_news_crop']) && is_string($_POST['rbc68_news_crop']) ) {
		update_post_meta($id,'_rbc68_news_crop',rbc68_news_image_position(wp_unslash($_POST['rbc68_news_crop'])));
	}
} );
add_action( 'admin_enqueue_scripts', function() {
	$screen=get_current_screen();
	if($screen && 'post' === $screen->post_type && 'post' === $screen->base) {
		wp_enqueue_script('rbc68-news-crop',get_template_directory_uri().'/assets/js/news-crop.js',array(),RBC68_VERSION . '.' . filemtime(get_template_directory().'/assets/js/news-crop.js'),true);
	}
} );
function rbc68_news_image( $context = 'card' ) {
	$position = rbc68_news_image_position( get_post_meta( get_the_ID(), '_rbc68_news_crop', true ) );
	$context = 'article' === $context ? 'article' : 'card';
	echo '<div class="rbc68-news-photo rbc68-news-photo-' . esc_attr($context) . ( 'full' === $position ? ' rbc68-news-photo-full' : '' ) . '">';
	the_post_thumbnail( 'full' === $position ? 'full' : 'large', array( 'class'=>'rbc68-news-image rbc68-crop-' . $position, 'sizes'=>'article' === $context ? '(max-width: 960px) 100vw, 900px' : '(max-width: 600px) 100vw, (max-width: 1000px) 50vw, 400px' ) );
	echo '</div>';
}
