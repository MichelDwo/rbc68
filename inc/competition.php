<?php
/** Équipes par saison et résultats des interclubs. */
defined( 'ABSPATH' ) || exit;

add_action( 'init', function() {
	foreach ( array( 'rbc68_result' => array( 'Compétition', 'Résultat', 'Ajouter un résultat' ), 'rbc68_team' => array( 'Équipes par saison', 'Équipe', 'Ajouter une équipe pour une saison' ) ) as $type => $labels ) {
		register_post_type( $type, array( 'labels' => array( 'name'=>$labels[0], 'singular_name'=>$labels[1], 'add_new_item'=>$labels[2], 'edit_item'=>'Modifier : ' . $labels[1], 'all_items'=>$labels[0] ), 'public'=>false, 'show_ui'=>true, 'show_in_menu'=>'rbc68_result' === $type ? true : 'edit.php?post_type=rbc68_result', 'menu_icon'=>'dashicons-awards', 'supports'=>array( 'title' ), 'rewrite'=>false ) );
	}
} );
function rbc68_comp_teams() { return get_posts( array( 'post_type'=>'rbc68_team', 'post_status'=>'publish', 'numberposts'=>-1, 'orderby'=>'title', 'order'=>'ASC' ) ); }
function rbc68_comp_meta( $id, $key ) { return get_post_meta( $id, '_rbc_comp_' . $key, true ); }
function rbc68_comp_season_valid( $season ) { return preg_match( '/^(\d{4})-(\d{4})$/', $season, $m ) && (int) $m[2] === (int) $m[1] + 1; }
function rbc68_comp_date_valid( $date ) { $d = DateTimeImmutable::createFromFormat( '!Y-m-d', $date ); return $d && $d->format( 'Y-m-d' ) === $date; }
function rbc68_comp_opponents( $text ) { return array_values( array_unique( array_filter( array_map( 'sanitize_text_field', preg_split( '/\R/u', $text ) ) ) ) ); }
function rbc68_comp_input( $key ) { return isset( $_POST['rbc_comp'][$key] ) && is_string( $_POST['rbc_comp'][$key] ) ? sanitize_text_field( wp_unslash( $_POST['rbc_comp'][$key] ) ) : ''; }
add_action( 'add_meta_boxes', function() {
	add_meta_box( 'rbc_comp_team', 'Saison et adversaires', 'rbc68_comp_team_fields', 'rbc68_team', 'normal', 'high' );
	add_meta_box( 'rbc_comp_result', 'Rencontre interclub', 'rbc68_comp_result_fields', 'rbc68_result', 'normal', 'high' );
} );
function rbc68_comp_team_fields( $post ) {
	wp_nonce_field( 'rbc_comp_save', 'rbc_comp_nonce' );
	echo '<p>Le titre est le nom de l’équipe (ex. RBC68 1). Créez une fiche par équipe et par saison pour conserver les archives.</p><p><label>Saison (ex. 2026-2027)<br><input name="rbc_comp[season]" value="' . esc_attr( rbc68_comp_meta( $post->ID, 'season' ) ) . '" pattern="[0-9]{4}-[0-9]{4}" required></label></p><p><label>Adversaires — un nom par ligne<br><textarea class="widefat" rows="8" name="rbc_comp[opponents]">' . esc_textarea( implode( "\n", (array) rbc68_comp_meta( $post->ID, 'opponents' ) ) ) . '</textarea></label></p>';
}
function rbc68_comp_result_fields( $post ) {
	wp_nonce_field( 'rbc_comp_save', 'rbc_comp_nonce' );
	$team_id = (int) rbc68_comp_meta( $post->ID, 'team' );
	echo '<p>Choisissez l’équipe et sa saison, puis un adversaire de sa liste. Le score est toujours présenté RBC68 – adversaire. Donnez à la rencontre un titre court dans le champ principal.</p><p><label>Équipe / saison<br><select id="rbc-comp-team" name="rbc_comp[team]" required><option value="">Choisir une équipe</option>';
	foreach ( rbc68_comp_teams() as $team ) {
		echo '<option value="' . esc_attr( $team->ID ) . '" ' . selected( $team_id, $team->ID, false ) . ' data-opponents="' . esc_attr( wp_json_encode( (array) rbc68_comp_meta( $team->ID, 'opponents' ) ) ) . '">' . esc_html( $team->post_title . ' — ' . rbc68_comp_meta( $team->ID, 'season' ) ) . '</option>';
	}
	echo '</select></label></p><p><label>Adversaire<br><select id="rbc-comp-opponent" name="rbc_comp[opponent]" required>';
	$current = rbc68_comp_meta( $post->ID, 'opponent' );
	$opponents = (array) rbc68_comp_meta( $team_id, 'opponents' );
	if ( $current && ! in_array( $current, $opponents, true ) ) { $opponents[] = $current; }
	echo '<option value="">Choisir un adversaire</option>';
	foreach ( $opponents as $name ) { echo '<option ' . selected( $current, $name, false ) . '>' . esc_html( $name ) . '</option>'; }
	echo '</select></label></p>';
	foreach ( array( 'date'=>array('Date de la rencontre','date'), 'score_for'=>array('Score RBC68','number'), 'score_against'=>array('Score adverse','number'), 'location'=>array('Lieu (facultatif)','text') ) as $key=>$field ) {
		echo '<p><label>' . esc_html( $field[0] ) . '<br><input type="' . esc_attr( $field[1] ) . '" name="rbc_comp[' . esc_attr( $key ) . ']" value="' . esc_attr( rbc68_comp_meta( $post->ID, $key ) ) . '" ' . ( 'number' === $field[1] ? 'min="0" max="99" step="1"' : '' ) . ( 'location' !== $key ? ' required' : '' ) . '></label></p>';
	}
}
function rbc68_comp_save( $id, $post ) {
	if ( ! in_array( $post->post_type, array('rbc68_team','rbc68_result'), true ) || wp_is_post_revision($id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || ! current_user_can('edit_post',$id) || ! isset($_POST['rbc_comp_nonce']) || ! is_string($_POST['rbc_comp_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rbc_comp_nonce'])),'rbc_comp_save') ) { return; }
	$error = '';
	if ( 'rbc68_team' === $post->post_type ) {
		$data = array('season'=>rbc68_comp_input('season'));
		$text = isset($_POST['rbc_comp']['opponents']) && is_string($_POST['rbc_comp']['opponents']) ? wp_unslash($_POST['rbc_comp']['opponents']) : '';
		$data['opponents'] = rbc68_comp_opponents($text);
		if (!rbc68_comp_season_valid($data['season']) || !trim($post->post_title)) { $error='Indiquez le nom de l’équipe et une saison valide, par exemple 2026-2027.'; }
	} else {
		$data = array();
		foreach(array('team','opponent','date','score_for','score_against','location') as $key) { $data[$key]=rbc68_comp_input($key); }
		$team=get_post(absint($data['team']));
		$allowed=(array) rbc68_comp_meta(absint($data['team']),'opponents');
		if ((string) rbc68_comp_meta($id,'team') === $data['team'] && rbc68_comp_meta($id,'opponent')) { $allowed[]=rbc68_comp_meta($id,'opponent'); }
		if (!$team || 'rbc68_team' !== $team->post_type || 'publish' !== $team->post_status || !in_array($data['opponent'],$allowed,true) || !$data['opponent']) { $error='Choisissez une équipe publiée et un adversaire de sa liste.'; }
		elseif (!rbc68_comp_date_valid($data['date']) || $data['date'] > wp_date('Y-m-d') || !preg_match('/^\d{1,2}$/',$data['score_for']) || !preg_match('/^\d{1,2}$/',$data['score_against'])) { $error='Indiquez une date passée ou aujourd’hui et deux scores entiers de 0 à 99.'; }
		else { $data['season']=rbc68_comp_meta($team->ID,'season'); $data['team_name']=$team->post_title; }
	}
	if ($error) {
		foreach($data as $key=>$value) { update_post_meta($id,'_rbc_comp_'.$key,$value); }
		set_transient('rbc_comp_error_'.get_current_user_id(),$error,60);
		remove_action('save_post','rbc68_comp_save',10);
		wp_update_post(array('ID'=>$id,'post_status'=>'draft'));
		add_action('save_post','rbc68_comp_save',10,2);
		return;
	}
	foreach($data as $key=>$value) { update_post_meta($id,'_rbc_comp_'.$key,$value); }
}
add_action('save_post','rbc68_comp_save',10,2);
add_action('admin_notices',function(){ $key='rbc_comp_error_'.get_current_user_id(); $error=get_transient($key); if($error){delete_transient($key);echo '<div class="notice notice-error"><p>'.esc_html($error).' Le contenu est conservé en brouillon ; corrigez les champs puis publiez.</p></div>';} });
add_action('admin_enqueue_scripts',function(){ $screen=get_current_screen(); if($screen && 'rbc68_result'===$screen->post_type){wp_enqueue_script('rbc-competition-admin',get_template_directory_uri().'/assets/js/competition-admin.js',array(),RBC68_VERSION,true);} });

/** Page dédiée créée une fois, sans toucher aux pages déjà présentes. */
add_action('admin_init',function(){
	if(!current_user_can('manage_options')){return;}
	$id=(int)get_option('rbc68_competition_page');
	if($id && get_post($id)){return;}
	$id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Résultats des Interclubs','post_name'=>'resultats-interclubs','meta_input'=>array('_wp_page_template'=>'template-competition.php')),true);
	if(!is_wp_error($id)){update_option('rbc68_competition_page',$id);}
});
function rbc68_comp_url(){return get_permalink((int)get_option('rbc68_competition_page')) ?: home_url('/');}
function rbc68_comp_query($filters=array(),$page=1,$limit=20){
	$meta=array('relation'=>'AND',array('key'=>'_rbc_comp_date','value'=>wp_date('Y-m-d'),'compare'=>'<=','type'=>'DATE'));
	foreach(array('season','team','opponent') as $key){if(!empty($filters[$key])){$meta[]=array('key'=>'_rbc_comp_'.$key,'value'=>$filters[$key],'compare'=>'=');}}
	return new WP_Query(array('post_type'=>'rbc68_result','post_status'=>'publish','has_password'=>false,'posts_per_page'=>$limit,'paged'=>$page,'meta_key'=>'_rbc_comp_date','orderby'=>array('meta_value'=>'DESC','ID'=>'DESC'),'meta_query'=>$meta));
}
function rbc68_comp_card($id){
	$a=(int)rbc68_comp_meta($id,'score_for');$b=(int)rbc68_comp_meta($id,'score_against');
	echo '<article class="competition-card"><p>'.esc_html(rbc68_comp_meta($id,'season')).' · <time datetime="'.esc_attr(rbc68_comp_meta($id,'date')).'">'.esc_html(wp_date('d/m/Y',strtotime(rbc68_comp_meta($id,'date').' 12:00:00'))).'</time></p><h3>'.esc_html(rbc68_comp_meta($id,'team_name')).' — '.esc_html(rbc68_comp_meta($id,'opponent')).'</h3><p class="competition-score">'.esc_html($a.' – '.$b).'</p><p>'.esc_html($a>$b?'Victoire RBC68':($a<$b?'Défaite RBC68':'Match nul')).'</p>';
	if(rbc68_comp_meta($id,'location')){echo '<p>'.esc_html(rbc68_comp_meta($id,'location')).'</p>';}
	echo '</article>';
}
function rbc68_comp_home(){
	echo '<section id="competition"><h2 class="section-title">Compétition</h2><p>Dernière rencontre interclub</p>';
	$q=rbc68_comp_query(array(),1,1);if($q->posts){rbc68_comp_card($q->posts[0]->ID);}else{echo '<p>Aucun résultat publié pour le moment.</p>';}
	echo '<p><a class="cta-button" href="'.esc_url(rbc68_comp_url()).'" target="_blank" rel="noopener">Tous les résultats <span class="screen-reader-text">(nouvel onglet)</span> ↗</a></p></section>';
}

add_filter('manage_rbc68_result_posts_columns',function($columns){return array('cb'=>$columns['cb'],'title'=>'Rencontre','comp_season'=>'Saison','comp_team_name'=>'Équipe','comp_opponent'=>'Adversaire','comp_date'=>'Date de rencontre','comp_score'=>'Score RBC68 – adverse');});
add_filter('manage_rbc68_team_posts_columns',function($columns){$columns['comp_season']='Saison';$columns['comp_opponents']='Adversaires';return $columns;});
function rbc68_comp_column($column,$id){
	if(0!==strpos($column,'comp_')){return;}
	$key=substr($column,5);
	if('score'===$key){$value=rbc68_comp_meta($id,'score_for').' – '.rbc68_comp_meta($id,'score_against');}
	elseif('opponents'===$key){$value=implode(', ',(array)rbc68_comp_meta($id,$key));}
	else{$value=rbc68_comp_meta($id,$key);}
	echo esc_html($value);
}
add_action('manage_rbc68_result_posts_custom_column','rbc68_comp_column',10,2);
add_action('manage_rbc68_team_posts_custom_column','rbc68_comp_column',10,2);
