<?php
/** Template Name: Résultats des interclubs */
defined('ABSPATH') || exit;
get_header();
$filters=array();
foreach(array('season','opponent') as $key){$param='competition_'.$key;$filters[$key]=isset($_GET[$param]) && is_string($_GET[$param]) ? sanitize_text_field(wp_unslash($_GET[$param])) : '';}
$page=isset($_GET['results_page']) && is_scalar($_GET['results_page']) ? max(1,absint($_GET['results_page'])) : 1;
$options=array('season'=>array(),'opponent'=>array());
// Inclut les libellés historiques, même si une équipe a été retirée depuis.
$ids=get_posts(array('post_type'=>'rbc68_result','post_status'=>'publish','has_password'=>false,'numberposts'=>-1,'fields'=>'ids','meta_query'=>array(array('key'=>'_rbc_comp_date','value'=>wp_date('Y-m-d'),'compare'=>'<=','type'=>'DATE'))));
foreach($ids as $id){foreach($options as $key=>$values){$value=rbc68_comp_meta($id,$key);if(''!==$value){$options[$key][$value]='team'===$key ? rbc68_comp_meta($id,'team_name').' — '.rbc68_comp_meta($id,'season') : $value;}}}
krsort($options['season']);asort($options['opponent'],SORT_NATURAL|SORT_FLAG_CASE);
$q=rbc68_comp_query($filters,$page);
?>
<main id="contenu" class="rbc68-content"><section>
<h1 class="section-title">Résultats des Interclubs</h1>
<p><a href="<?php echo esc_url(home_url('/#competition')); ?>">← Retour au club</a></p>
<form class="competition-filters" method="get" action="<?php echo esc_url(get_permalink()); ?>">
<?php if(!get_option('permalink_structure')): ?><input type="hidden" name="page_id" value="<?php echo absint(get_the_ID()); ?>"><?php endif; ?>
<?php foreach(array('season'=>'Saison','opponent'=>'Adversaire') as $key=>$label): ?>
<label><?php echo esc_html($label); ?><select name="competition_<?php echo esc_attr($key); ?>"><option value="">Tous / toutes</option>
<?php foreach($options[$key] as $value=>$name): ?><option value="<?php echo esc_attr($value); ?>" <?php selected($filters[$key],(string)$value); ?>><?php echo esc_html($name); ?></option><?php endforeach; ?>
</select></label><?php endforeach; ?>
<button class="cta-button" type="submit">Filtrer</button><a href="<?php echo esc_url(get_permalink()); ?>">Réinitialiser</a>
</form>
<p><?php echo esc_html($q->found_posts); ?> résultat(s) — du plus récent au plus ancien.</p>
<div class="competition-results"><?php foreach($q->posts as $result){rbc68_comp_card($result->ID);} ?></div>
<?php if(!$q->posts): ?><p>Aucun résultat pour cette sélection.</p><?php endif; ?>
<?php if($q->max_num_pages>1): ?><nav aria-label="Pages des résultats"><?php
$args=array();foreach($filters as $key=>$value){if(''!==$value){$args['competition_'.$key]=$value;}}
echo paginate_links(array('base'=>add_query_arg('results_page','%#%',get_permalink()),'format'=>'','current'=>$page,'total'=>$q->max_num_pages,'add_args'=>$args,'prev_text'=>'← Précédent','next_text'=>'Suivant →'));
?></nav><?php endif; ?>
</section></main>
<?php get_footer(); ?>
