<?php
/** Import explicite, borné au domaine ICBaD ; aucune requête lors des visites publiques. */
defined('ABSPATH') || exit;
function rbc68_icbad_url($url){return is_string($url) && preg_match('~^https://icbad\.ffbad\.org/equipe/([1-9][0-9]*)/?$~D',$url,$m)?$m[1]:false;}
function rbc68_icbad_fetch($path){
 $response=wp_safe_remote_get('https://icbad.ffbad.org/'.$path,array('timeout'=>15,'redirection'=>0,'limit_response_size'=>2000000));
 if(is_wp_error($response)){throw new RuntimeException('ICBaD ne répond pas. Réessayez plus tard.');}
 if(200!==wp_remote_retrieve_response_code($response)){throw new RuntimeException('ICBaD a renvoyé une erreur HTTP.');}
 return wp_remote_retrieve_body($response);
}
function rbc68_icbad_dom($html){
 if(!class_exists('DOMDocument')){throw new RuntimeException('L’extension PHP DOM doit être activée sur l’hébergement.');}
 $previous=libxml_use_internal_errors(true);$doc=new DOMDocument();$doc->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET);libxml_clear_errors();libxml_use_internal_errors($previous);return new DOMXPath($doc);
}
function rbc68_icbad_text($node){return $node?trim(preg_replace('/\s+/u',' ',$node->textContent)):'';}
function rbc68_icbad_rows($html,$number){
 $xp=rbc68_icbad_dom($html);$table=$xp->query('//h2[normalize-space(.)="Rencontres"]/following-sibling::table[1]')->item(0);
 if(!$table){throw new RuntimeException('Tableau Rencontres introuvable : aucune donnée modifiée.');}
 $rows=array();
 foreach($xp->query('.//tr[td]',$table) as $tr){$td=$xp->query('./td',$tr);$a=$xp->query('.//a[contains(@href,"/rencontre/")]',$tr)->item(0);if(!$a||$td->length<6){continue;}
 if(!preg_match('~/rencontre/([0-9]+)$~',$a->getAttribute('href'),$m)){continue;}$source=$m[1];
 $home=rbc68_icbad_text($td->item(3));$away=rbc68_icbad_text($td->item(5));$score=rbc68_icbad_text($td->item(4));
 if(!preg_match('/^(\d{1,2})\s*[-–]\s*(\d{1,2})$/u',$score,$m)){continue;}
 if((int)$m[1]===0 && (int)$m[2]===0){continue;}
 $pattern='/\(68-RBC-'.(int)$number.'\)/i';$is_home=(bool)preg_match($pattern,$home);$is_away=(bool)preg_match($pattern,$away);
 if($is_home===$is_away){throw new RuntimeException('Le numéro RBC choisi ne correspond pas à cette page ICBaD.');}
 $rows[]=array('source'=>$source,'number'=>(int)$number,'home'=>$is_home?rbc68_comp_rbc($number):$home,'away'=>$is_away?rbc68_comp_rbc($number):$away,'opponent'=>$is_home?$away:$home,'home_score'=>$m[1],'away_score'=>$m[2],'location'=>rbc68_icbad_text($td->item(2)));
 }
 if(count($rows)>100){throw new RuntimeException('Plus de 100 rencontres : import interrompu.');}return $rows;
}
function rbc68_icbad_date($html){
 $xp=rbc68_icbad_dom($html);$text=rbc68_icbad_text($xp->query('//dt[normalize-space(.)="Date"]/following-sibling::dd[1]')->item(0));
 $months=array('janvier'=>1,'février'=>2,'mars'=>3,'avril'=>4,'mai'=>5,'juin'=>6,'juillet'=>7,'août'=>8,'septembre'=>9,'octobre'=>10,'novembre'=>11,'décembre'=>12);
 if(preg_match('/\b(\d{1,2})\s+([a-zéû]+)\s+(\d{4})\b/u',strtolower($text),$m)&&isset($months[$m[2]])){$date=sprintf('%04d-%02d-%02d',$m[3],$months[$m[2]],$m[1]);if(rbc68_comp_date_valid($date)){return $date;}}
 throw new RuntimeException('Date complète ICBaD introuvable : rencontre ignorée.');
}
function rbc68_icbad_candidate($row,$date,$season){
 $year=(int)substr($date,0,4);$month=(int)substr($date,5,2);$start=(int)substr($season,0,4);
 if($date>wp_date('Y-m-d')){throw new RuntimeException('Rencontre future ignorée.');}
 if(($month>=9?$year:$year-1)!==$start){throw new RuntimeException('Date '.$date.' hors de la saison '.$season.' : rencontre ignorée.');}
 $row['date']=$date;$row['season']=$season;$row['team']='rbc-'.$row['number'];$row['team_name']=rbc68_comp_rbc($row['number']);$home=$row['home']===$row['team_name'];$row['score_for']=$home?$row['home_score']:$row['away_score'];$row['score_against']=$home?$row['away_score']:$row['home_score'];return $row;
}
add_action('wp_ajax_rbc_icbad',function(){
 if(!current_user_can('manage_options')){wp_send_json_error(array('message'=>'Accès refusé.'),403);}check_ajax_referer('rbc_icbad','nonce');
 try{
 $step=rbc68_comp_post('step');$key='rbc_icbad_'.get_current_user_id().'_'.rbc68_comp_post('token');
 if('start'===$step){$team=rbc68_icbad_url(rbc68_comp_post('url'));$season=rbc68_comp_post('season');$n=(int)rbc68_comp_post('number');$seasons=rbc68_comp_seasons();
 if(!$team||!isset($seasons[$season])||!in_array($n,array(1,2,3),true)){throw new RuntimeException('Vérifiez la saison, le numéro RBC et l’URL https://icbad.ffbad.org/equipe/…');}
 $rows=rbc68_icbad_rows(rbc68_icbad_fetch('equipe/'.$team),$n);$token=wp_generate_password(24,false,false);set_transient('rbc_icbad_'.get_current_user_id().'_'.$token,array('season'=>$season,'rows'=>$rows,'ready'=>array()),HOUR_IN_SECONDS);wp_send_json_success(array('token'=>$token,'count'=>count($rows)));}
 $state=get_transient($key);if(!$state){throw new RuntimeException('Session expirée. Relancez l’import.');}
 $index=absint(rbc68_comp_post('index'));
 if('prepare'===$step){if(!isset($state['rows'][$index])){throw new RuntimeException('Rencontre inconnue.');}$row=$state['rows'][$index];$row=rbc68_icbad_candidate($row,rbc68_icbad_date(rbc68_icbad_fetch('rencontre/'.$row['source'])),$state['season']);$state['ready'][$index]=$row;set_transient($key,$state,HOUR_IN_SECONDS);wp_send_json_success(array('row'=>$row,'exists'=>(bool)rbc68_comp_existing($row)));}
 if('save'===$step){if(!isset($state['ready'][$index])){throw new RuntimeException('Préparez cette rencontre avant de l’importer.');}$row=$state['ready'][$index];
 // Verrou global très court : protège aussi deux imports simultanés du même match.
 $lock='rbc_icbad_lock';if(!add_option($lock,time(),'','no')){if((int)get_option($lock)<time()-60){delete_option($lock);}throw new RuntimeException('Un import est en cours. Réessayez cette ligne.');}
 try{$exists=rbc68_comp_existing($row);if(!$exists){$id=rbc68_comp_store($row);if(is_wp_error($id)){throw new RuntimeException($id->get_error_message());}
 $seasons=rbc68_comp_seasons();$s=$row['season'];$n=$row['number'];$seasons[$s][$n]=array_values(array_unique(array_merge($seasons[$s][$n]??array(),array($row['opponent']))));update_option('rbc68_comp_seasons',$seasons);}}finally{delete_option($lock);}
 wp_send_json_success(array('skipped'=>(bool)$exists));}
 throw new RuntimeException('Action inconnue.');
 }catch(Throwable $e){wp_send_json_error(array('message'=>$e->getMessage()));}
});
