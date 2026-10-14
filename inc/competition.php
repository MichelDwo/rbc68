<?php
/** Équipes par saison et résultats des interclubs. */
defined( 'ABSPATH' ) || exit;

add_action( 'init', function() {
	foreach ( array( 'rbc68_result' => array( 'Compétition', 'Résultat', 'Ajouter un résultat' ), 'rbc68_team' => array( 'Équipes par saison', 'Équipe', 'Ajouter une équipe pour une saison' ) ) as $type => $labels ) {
		register_post_type( $type, array( 'labels' => array( 'name'=>$labels[0], 'singular_name'=>$labels[1], 'add_new_item'=>$labels[2], 'edit_item'=>'Modifier : ' . $labels[1], 'all_items'=>$labels[0] ), 'public'=>false, 'show_ui'=>false, 'show_in_menu'=>false, 'menu_icon'=>'dashicons-awards', 'supports'=>array( 'title' ), 'rewrite'=>false ) );
	}
} );
function rbc68_comp_teams() { return get_posts( array( 'post_type'=>'rbc68_team', 'post_status'=>'publish', 'numberposts'=>-1, 'orderby'=>'title', 'order'=>'ASC' ) ); }
function rbc68_comp_meta( $id, $key ) { return get_post_meta( $id, '_rbc_comp_' . $key, true ); }
function rbc68_comp_season_valid( $season ) { return preg_match( '/^(\d{4})-(\d{4})$/', $season, $m ) && (int) $m[2] === (int) $m[1] + 1; }
function rbc68_comp_date_valid( $date ) { $d = DateTimeImmutable::createFromFormat( '!Y-m-d', $date ); return $d && $d->format( 'Y-m-d' ) === $date; }
function rbc68_comp_opponents( $text ) { return array_values( array_unique( array_filter( array_map( 'sanitize_text_field', preg_split( '/\R/u', $text ) ) ) ) ); }
function rbc68_comp_input( $key ) { return isset( $_POST['rbc_comp'][$key] ) && is_string( $_POST['rbc_comp'][$key] ) ? sanitize_text_field( wp_unslash( $_POST['rbc_comp'][$key] ) ) : ''; }
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
/** Données historiques conservées, sans inventer le domicile des anciennes saisies. */
function rbc68_comp_data($id) {
 $d=array(); foreach(array('season','date','opponent','location','team_name','team','score_for','score_against','home','away','home_score','away_score','number','source') as $k){$d[$k]=rbc68_comp_meta($id,$k);}
 if(!$d['number'] && preg_match('/(?:RBC(?:68)?[- ]?|équipe\s*)([123])\b/iu',$d['team_name'],$m)){$d['number']=$m[1];}
 $d['orientation_known']=($d['home']!=='' && $d['away']!=='');
 if(!$d['orientation_known']){$d['home']=$d['team_name'];$d['away']=$d['opponent'];$d['home_score']=$d['score_for'];$d['away_score']=$d['score_against'];}
 return $d;
}
function rbc68_comp_week($date){$d=new DateTimeImmutable($date,wp_timezone());return $d->format('o-W');}
function rbc68_comp_latest_week($posts){
 $chosen=array();$week='';
 foreach($posts as $post){$d=rbc68_comp_data($post->ID);if(!rbc68_comp_date_valid($d['date'])){continue;} $w=rbc68_comp_week($d['date']);if(!$week){$week=$w;}if($w!==$week){break;}
 $key=$d['number'] ?: $d['team'];if(!isset($chosen[$key])){$chosen[$key]=$post;}if(count($chosen)>=3){break;}}
 return array_values($chosen);
}
function rbc68_comp_card($id){
 $d=rbc68_comp_data($id);
 echo '<article class="competition-line"><time datetime="'.esc_attr($d['date']).'">'.esc_html(wp_date('d/m/Y',strtotime($d['date'].' 12:00:00'))).'</time><span>'.esc_html($d['home']).'</span><strong class="competition-line-score">'.esc_html($d['home_score'].' – '.$d['away_score']).'</strong><span>'.esc_html($d['away']).'</span>';
 if(!$d['orientation_known']){echo '<small>Ordre domicile/extérieur à confirmer</small>';}
 echo '</article>';
}
function rbc68_comp_home(){
 echo '<section id="competition"><h2 class="section-title">Compétition</h2><p>Derniers résultats interclubs</p>';
 $q=rbc68_comp_query(array(),1,1);
 if($q->posts){$date=rbc68_comp_meta($q->posts[0]->ID,'date');$start=(new DateTimeImmutable($date))->modify('monday this week')->format('Y-m-d');
 $args=$q->query_vars;$args['posts_per_page']=-1;$args['meta_query'][]=array('key'=>'_rbc_comp_date','value'=>$start,'compare'=>'>=','type'=>'DATE');$week=new WP_Query($args);
 foreach(rbc68_comp_latest_week($week->posts) as $p){rbc68_comp_card($p->ID);}
 }else{echo '<p>Aucun résultat publié pour le moment.</p>';}
 echo '<p><a class="cta-button" href="'.esc_url(rbc68_comp_url()).'" target="_blank" rel="noopener">Tous les résultats <span class="screen-reader-text">(nouvel onglet)</span> ↗</a></p></section>';
}
require_once __DIR__.'/competition-admin.php';
require_once __DIR__.'/competition-import.php';
