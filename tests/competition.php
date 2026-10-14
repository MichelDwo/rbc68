<?php
define('ABSPATH',__DIR__);
function add_action(...$args){}
function add_filter(...$args){}
function sanitize_text_field($s){return trim(strip_tags($s));}
function wp_date($f){return '2026-10-14';}
class WP_Query {public $args;function __construct($args){$this->args=$args;}}
require dirname(__DIR__).'/inc/competition.php';
function check($ok,$label){if(!$ok)throw new Exception($label);}
check(rbc68_comp_season_valid('2026-2027'),'saison');
foreach(['2026-2026','2026-2028','26-27','<script>'] as $s){check(!rbc68_comp_season_valid($s),'saison invalide');}
check(rbc68_comp_date_valid('2028-02-29'),'année bissextile');
check(!rbc68_comp_date_valid('2026-02-29'),'date invalide');
check(rbc68_comp_opponents("Colmar\nMulhouse\nColmar\n \n")===['Colmar','Mulhouse'],'adversaires uniques');
$q=rbc68_comp_query(['season'=>'2026-2027','team'=>'42','opponent'=>'Colmar'],2);
check($q->args['post_status']==='publish' && !$q->args['has_password'],'résultats publics seulement');
check($q->args['paged']===2 && $q->args['posts_per_page']===20,'pagination');
check(count($q->args['meta_query'])===5 && $q->args['meta_query']['relation']==='AND','trois filtres combinables');
check($q->args['meta_query'][0]['compare']==='<=','exclusion des dates futures');
check($q->args['orderby']['meta_value']==='DESC','dernière rencontre par date');
check(rbc68_comp_query([],1,1)->args['posts_per_page']===1,'accueil une rencontre');
echo "OK — saisons, dates, adversaires, filtres, pagination et dernière rencontre\n";
