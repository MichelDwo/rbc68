<?php
define('ABSPATH',__DIR__);define('HOUR_IN_SECONDS',3600);
function add_action(...$args){} function add_filter(...$args){}
function sanitize_text_field($s){return trim(strip_tags($s));}
function wp_date($f,$t=null){return $t?date($f,$t):'2026-10-14';}
function wp_timezone(){return new DateTimeZone('Europe/Paris');}
function get_post_meta($id,$key,$single){return $GLOBALS['meta'][$id][$key]??'';}
class WP_Query{public $args;function __construct($args){$this->args=$args;}}
require dirname(__DIR__).'/inc/competition.php';
function check($ok,$msg){if(!$ok)throw new Exception($msg);}
$rows=rbc68_icbad_rows(file_get_contents(__DIR__.'/fixtures/icbad-results.html'),1);
check(count($rows)===10,'dix rencontres réelles');check($rows[0]['home']===rbc68_comp_rbc(1),'RBC domicile');check($rows[1]['away']===rbc68_comp_rbc(1),'RBC extérieur');check($rows[7]['home_score']==='3' && $rows[7]['away_score']==='5','scores domicile respectés');
$date=rbc68_icbad_date(file_get_contents(__DIR__.'/fixtures/icbad-date.html'));check($date==='2025-10-08','année réelle');
$d=rbc68_icbad_candidate($rows[0],$date,'2025-2026');check($d['score_for']==='8','score RBC');
try{rbc68_icbad_candidate($rows[0],$date,'2026-2027');throw new Exception('mauvaise saison acceptée');}catch(RuntimeException $e){}
try{rbc68_icbad_rows('<h2>Erreur</h2>',1);throw new Exception('HTML cassé accepté');}catch(RuntimeException $e){}
check(rbc68_icbad_url('https://icbad.ffbad.org/equipe/62452')==='62452','URL valide');foreach(['http://icbad.ffbad.org/equipe/1','https://evil.test/equipe/1','https://icbad.ffbad.org@evil.test/equipe/1','https://icbad.ffbad.org/equipe/1?url=x'] as $url){check(!rbc68_icbad_url($url),'URL interdite');}
$seasons=['2025-2026'=>[1=>['Colmar']]];
$manual=['season'=>'2025-2026','date'=>'2025-10-08','home'=>'Colmar','away'=>rbc68_comp_rbc(1),'home_score'=>'3','away_score'=>'5','location'=>''];
$valid=rbc68_comp_validate_manual($manual,$seasons);check(is_array($valid)&&$valid['score_for']==='5'&&$valid['number']===1,'saisie extérieure');
$bad=$manual;$bad['away']='Autre';check(is_string(rbc68_comp_validate_manual($bad,$seasons)),'RBC obligatoire');
$bad=$manual;$bad['home_score']='-2';check(is_string(rbc68_comp_validate_manual($bad,$seasons)),'score invalide');
$posts=[];foreach([[1,'2026-01-04',1],[2,'2026-01-03',2],[3,'2026-01-02',1],[4,'2025-12-29',3],[5,'2025-12-28',2]] as $r){$posts[]=(object)['ID'=>$r[0]];foreach(['date'=>$r[1],'number'=>$r[2]] as $k=>$v){$GLOBALS['meta'][$r[0]]['_rbc_comp_'.$k]=$v;}}
check(array_column(rbc68_comp_latest_week($posts),'ID')===[1,2,4],'semaine ISO, nouvel an et une ligne par équipe');
check(rbc68_comp_latest_week([])===[],'aucun résultat');
echo "OK — extraction réelle, domicile/extérieur, saison, SSRF, saisie et semaine ISO\n";
function get_posts($args){
 if(($args['meta_key']??'')==='_rbc_comp_source'){return $GLOBALS['source_hit']??[];}
 return $GLOBALS['candidates']??[];
}
$GLOBALS['source_hit']=[42];check(rbc68_comp_existing($d)===42,'identifiant déjà importé');
$GLOBALS['source_hit']=[];$GLOBALS['candidates']=[43];
foreach(['season'=>'2025-2026','date'=>'2025-10-08','number'=>1,'team_name'=>'RBC68 1','opponent'=>'Autre libellé','score_for'=>8,'score_against'=>0] as $k=>$v){$GLOBALS['meta'][43]['_rbc_comp_'.$k]=$v;}
check(rbc68_comp_existing($d)===43,'saisie manuelle protégée même avec un libellé différent');
$GLOBALS['candidates']=[];check(rbc68_comp_existing($d)===0,'nouvelle rencontre');
echo "OK — déduplication source et protection de la saisie manuelle\n";
