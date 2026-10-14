<?php
defined('ABSPATH') || exit;
function rbc68_comp_rbc($n){return 'Riedisheim Badminton Club — Équipe '.(int)$n;}
function rbc68_comp_seasons(){
 $s=get_option('rbc68_comp_seasons',array());if(!is_array($s)){$s=array();}$saved=$s;
 // Reprise non destructive des anciennes fiches équipe/saison.
 foreach(rbc68_comp_teams() as $team){$season=rbc68_comp_meta($team->ID,'season');if(!rbc68_comp_season_valid($season)){continue;}
 if(!isset($s[$season])){$s[$season]=array();}
 if(preg_match('/(?:RBC(?:68)?[- ]?|équipe\s*)([123])\b/iu',$team->post_title,$m) && !isset($s[$season][$m[1]])){$s[$season][$m[1]]=array_values(array_filter((array)rbc68_comp_meta($team->ID,'opponents')));}}
 foreach(get_posts(array('post_type'=>'rbc68_result','post_status'=>array('publish','draft'),'numberposts'=>-1)) as $result){$d=rbc68_comp_data($result->ID);if(!rbc68_comp_season_valid($d['season'])){continue;}if(!isset($s[$d['season']])){$s[$d['season']]=array();}if($d['number'] && $d['opponent'] && !array_key_exists($d['number'],$saved[$d['season']]??array())){$n=$d['number'];$s[$d['season']][$n]=array_values(array_unique(array_merge($s[$d['season']][$n]??array(),array($d['opponent']))));}}
 krsort($s);return $s;
}
function rbc68_comp_post($key){return isset($_POST[$key]) && is_string($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';}
function rbc68_comp_key($d){$names=array(strtolower(trim($d['home'])),strtolower(trim($d['away'])));sort($names);return hash('sha256',$d['season'].'|'.$d['date'].'|'.implode('|',$names));}
function rbc68_comp_existing($d){
 if(!empty($d['source'])){$ids=get_posts(array('post_type'=>'rbc68_result','post_status'=>array('publish','draft','private','pending','future','trash'),'numberposts'=>1,'fields'=>'ids','meta_key'=>'_rbc_comp_source','meta_value'=>$d['source']));if($ids){return $ids[0];}}
 $ids=get_posts(array('post_type'=>'rbc68_result','post_status'=>array('publish','draft','private','pending','future','trash'),'numberposts'=>-1,'fields'=>'ids','meta_query'=>array(array('key'=>'_rbc_comp_season','value'=>$d['season']),array('key'=>'_rbc_comp_date','value'=>$d['date']))));
 foreach($ids as $id){$old=rbc68_comp_data($id);if((!empty($d['source']) && $old['number']==$d['number']) || rbc68_comp_key($old)===rbc68_comp_key($d) || ($old['number']==$d['number'] && strtolower($old['opponent'])===strtolower($d['opponent']))){return $id;}}
 return 0;
}
function rbc68_comp_store($d,$id=0){
 $meta=array();foreach($d as $k=>$v){$meta['_rbc_comp_'.$k]=$v;}
 return wp_insert_post(array('ID'=>$id,'post_type'=>'rbc68_result','post_status'=>'publish','post_title'=>$d['date'].' : '.$d['home'].' – '.$d['away'],'meta_input'=>$meta),true);
}
function rbc68_comp_validate_manual($d,$seasons){
 if(!isset($seasons[$d['season']]) || !rbc68_comp_date_valid($d['date']) || $d['date']>wp_date('Y-m-d')){return 'Sélectionnez une saison existante et une date passée ou aujourd’hui.';}
 $start=(int)substr($d['season'],0,4);if($d['date']<$start.'-09-01' || $d['date']>($start+1).'-08-31'){return 'La date doit appartenir à la saison sélectionnée (septembre à août).';}
 $home_n=array_search($d['home'],array(1=>rbc68_comp_rbc(1),2=>rbc68_comp_rbc(2),3=>rbc68_comp_rbc(3)),true);
 $away_n=array_search($d['away'],array(1=>rbc68_comp_rbc(1),2=>rbc68_comp_rbc(2),3=>rbc68_comp_rbc(3)),true);
 if((bool)$home_n===(bool)$away_n){return 'La rencontre doit opposer une équipe RBC à une équipe adverse.';}
 $n=$home_n ?: $away_n;$opp=$home_n ? $d['away'] : $d['home'];
 if(!in_array($opp,$seasons[$d['season']][$n]??array(),true)){return 'Cet adversaire ne figure pas dans la liste de cette équipe RBC pour la saison.';}
 foreach(array('home_score','away_score') as $k){if(!preg_match('/^\d{1,2}$/',$d[$k])){return 'Les scores doivent être des entiers de 0 à 99.';}}
 $d['number']=$n;$d['opponent']=$opp;$d['team_name']=rbc68_comp_rbc($n);$d['team']='rbc-'.$n;
 $d['score_for']=$home_n?$d['home_score']:$d['away_score'];$d['score_against']=$home_n?$d['away_score']:$d['home_score'];return $d;
}
add_action('admin_menu',function(){add_menu_page('Compétition','Compétition','manage_options','rbc-competition','rbc68_comp_admin','dashicons-awards',26);});
function rbc68_comp_admin(){
 if(!current_user_can('manage_options')){return;}$seasons=rbc68_comp_seasons();$notice='';$error='';
 if('POST'===($_SERVER['REQUEST_METHOD']??'') && isset($_POST['comp_action'])){
 check_admin_referer('rbc_comp_admin');$action=rbc68_comp_post('comp_action');$season=rbc68_comp_post('season');
 if('season'===$action){if(!rbc68_comp_season_valid($season)){$error='Saison invalide : utilisez 2026-2027.';}else{$seasons[$season]=$seasons[$season]??array();update_option('rbc68_comp_seasons',$seasons);$notice='Saison ajoutée.';}}
 if('opponents'===$action && isset($seasons[$season])){for($n=1;$n<=3;$n++){$raw=isset($_POST['opponents_'.$n])&&is_string($_POST['opponents_'.$n])?wp_unslash($_POST['opponents_'.$n]):'';$seasons[$season][$n]=rbc68_comp_opponents($raw);}update_option('rbc68_comp_seasons',$seasons);$notice='Adversaires enregistrés.';}
 if('match'===$action){$d=array();foreach(array('season','date','home','home_score','away','away_score','location') as $k){$d[$k]=rbc68_comp_post($k);}$d=rbc68_comp_validate_manual($d,$seasons);$id=absint(rbc68_comp_post('result_id'));
 if(is_string($d)){$error=$d;}elseif($id && ('rbc68_result'!==get_post_type($id)||!current_user_can('edit_post',$id))){$error='Rencontre non modifiable.';}elseif(($exists=rbc68_comp_existing($d)) && $exists!==$id){$error='Cette rencontre existe déjà. Modifiez-la depuis la liste ci-dessous.';}else{$saved=rbc68_comp_store($d,$id);if(is_wp_error($saved)){$error=$saved->get_error_message();}else{$notice='Rencontre enregistrée.';}}}
 }
 $season=isset($_GET['season'])&&is_string($_GET['season'])?sanitize_text_field(wp_unslash($_GET['season'])):(array_key_first($seasons)??'');
 if(isset($_POST['season']) && isset($seasons[rbc68_comp_post('season')])){$season=rbc68_comp_post('season');}
 $id=isset($_GET['edit_result'])&&is_scalar($_GET['edit_result'])?absint($_GET['edit_result']):0;$edit=$id && 'rbc68_result'===get_post_type($id)?rbc68_comp_data($id):array();if($edit){$season=$edit['season'];}
 echo '<div class="wrap"><h1>Compétition</h1>';if($notice){echo '<div class="notice notice-success"><p>'.esc_html($notice).'</p></div>';}if($error){echo '<div class="notice notice-error"><p>'.esc_html($error).'</p></div>';}
 ?>
 <h2>1. Saisons et adversaires</h2>
 <form method="post"><?php wp_nonce_field('rbc_comp_admin'); ?><input type="hidden" name="comp_action" value="season"><label>Nouvelle saison <input name="season" placeholder="2026-2027" pattern="[0-9]{4}-[0-9]{4}" required></label> <button class="button">Ajouter la saison</button></form>
 <form method="get"><input type="hidden" name="page" value="rbc-competition"><p><label>Saison <select name="season"><?php foreach($seasons as $s=>$v){echo '<option '.selected($season,$s,false).'>'.esc_html($s).'</option>';} ?></select></label> <button class="button">Afficher</button></p></form>
 <?php if(isset($seasons[$season])): ?>
 <form method="post"><?php wp_nonce_field('rbc_comp_admin'); ?><input type="hidden" name="comp_action" value="opponents"><input type="hidden" name="season" value="<?php echo esc_attr($season); ?>"><p>Un nom par équipe adverse, une ligne par nom. Précisez leur numéro d’équipe si nécessaire.</p><div style="display:flex;flex-wrap:wrap;gap:20px">
 <?php for($n=1;$n<=3;$n++): ?><label><strong><?php echo esc_html(rbc68_comp_rbc($n)); ?></strong><br><textarea name="opponents_<?php echo $n; ?>" rows="7" cols="38"><?php echo esc_textarea(implode("\n",$seasons[$season][$n]??array())); ?></textarea></label><?php endfor; ?></div><p><button class="button button-primary">Enregistrer les adversaires</button></p></form>
 <h2>2. <?php echo $edit?'Modifier la rencontre':'Saisir une rencontre'; ?></h2>
 <?php if($edit && !$edit['orientation_known']): ?><p>Cette ancienne saisie ne précisait pas le domicile : vérifiez qui accueille avant d’enregistrer.</p><?php endif; ?>
 <form method="post"><?php wp_nonce_field('rbc_comp_admin'); ?><input type="hidden" name="comp_action" value="match"><input type="hidden" name="result_id" value="<?php echo $id; ?>"><input type="hidden" name="season" value="<?php echo esc_attr($season); ?>">
 <p><label>Date <input type="date" name="date" required max="<?php echo esc_attr(wp_date('Y-m-d')); ?>" value="<?php echo esc_attr($edit['date']??''); ?>"></label></p>
 <?php $clubs=array(rbc68_comp_rbc(1),rbc68_comp_rbc(2),rbc68_comp_rbc(3));foreach($seasons[$season] as $names){$clubs=array_merge($clubs,$names);}$clubs=array_unique($clubs);
 foreach(array('home'=>'Équipe qui accueille','away'=>'Équipe visiteuse') as $key=>$label): ?>
 <p><label><?php echo esc_html($label); ?> <select name="<?php echo esc_attr($key); ?>" required><option value="">Choisir</option><?php foreach($clubs as $name){echo '<option '.selected($edit[$key]??'',$name,false).'>'.esc_html($name).'</option>';} ?></select></label> <label>Score <input type="number" name="<?php echo esc_attr($key); ?>_score" min="0" max="99" required value="<?php echo esc_attr($edit[$key.'_score']??''); ?>"></label></p><?php endforeach; ?>
 <p><label>Lieu (facultatif) <input name="location" value="<?php echo esc_attr($edit['location']??''); ?>"></label></p><button class="button button-primary">Enregistrer la rencontre</button></form>
 <h2>3. Import ICBaD</h2><div id="rbc-import" data-season="<?php echo esc_attr($season); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('rbc_icbad')); ?>"><p><label>Équipe RBC <select id="rbc-import-number"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select></label> <label>URL de l’équipe ICBaD <input type="url" id="rbc-import-url" size="55" placeholder="https://icbad.ffbad.org/equipe/62452"></label></p><button class="button" type="button" data-import="batch">Préparer l’import en lot</button> <button class="button" type="button" data-import="diff">Import différentiel</button><p>L’import différentiel ajoute seulement les résultats absents. Les résultats existants, y compris ceux saisis à la main, ne sont jamais réécrits. Les rencontres sans score ou futures sont ignorées. En lot, vous choisissez les lignes à importer après aperçu.</p><p role="status" id="rbc-import-status"></p><div id="rbc-import-preview"></div><button type="button" class="button button-primary" id="rbc-import-confirm" hidden>Importer les lignes sélectionnées</button></div>
 <?php endif; ?>
 <h2>Rencontres enregistrées</h2><table class="widefat striped"><thead><tr><th>Saison</th><th>Date</th><th>Rencontre (domicile en premier)</th><th>Action</th></tr></thead><tbody>
 <?php $page=isset($_GET['comp_page'])?max(1,absint($_GET['comp_page'])):1;$q=rbc68_comp_query($season?array('season'=>$season):array(),$page,30);foreach($q->posts as $post){$d=rbc68_comp_data($post->ID);echo '<tr><td>'.esc_html($d['season']).'</td><td>'.esc_html($d['date']).'</td><td>'.esc_html($d['home'].' '.$d['home_score'].' – '.$d['away_score'].' '.$d['away']).(!$d['orientation_known']?' (domicile à confirmer)':'').'</td><td><a href="'.esc_url(add_query_arg(array('page'=>'rbc-competition','edit_result'=>$post->ID),admin_url('admin.php'))).'">Modifier</a></td></tr>';} ?></tbody></table>
 <?php echo paginate_links(array('base'=>add_query_arg(array('page'=>'rbc-competition','season'=>$season,'comp_page'=>'%#%'),admin_url('admin.php')),'format'=>'','current'=>$page,'total'=>$q->max_num_pages)); ?></div><?php
}
add_action('admin_enqueue_scripts',function($hook){if('toplevel_page_rbc-competition'===$hook){wp_enqueue_script('rbc-icbad',get_template_directory_uri().'/assets/js/competition-import.js',array(),RBC68_VERSION.'.'.filemtime(__DIR__.'/../assets/js/competition-import.js'),true);}});
