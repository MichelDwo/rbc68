<?php
 define('ABSPATH', __DIR__);
 function add_action(...$a){}
 function wp_timezone(){return new DateTimeZone('Europe/Paris');}
 function get_theme_mod($key,$default=false){return $GLOBALS['mods'][$key] ?? $default;}
 function set_theme_mod($key,$value){$GLOBALS['mods'][$key]=$value;}
 function rbc68_event_kind($id){return $GLOBALS['kinds'][$id] ?? 'public';}
 function absint($n){return abs((int)$n);}
 function wp_attachment_is_image($id){return in_array($id,[1,2,3],true);}
 function rbc68_get_upcoming_events($n){return $GLOBALS['events'];}
 function post_password_required($post){return !empty($post->protected);}
 function get_post_meta($id,$key,$single){return $GLOBALS['dates'][$id];}
 class WP_Query { public $posts; public function __construct($args){$GLOBALS['query']=$args; $this->posts=$GLOBALS['news'];} }
 require dirname(__DIR__) . '/inc/home-visuals.php';
 function check($yes,$msg){if(!$yes)throw new Exception($msg);}
 $now=new DateTimeImmutable('2026-10-12 22:00:00',wp_timezone());
 foreach(['2026-10-11'=>false,'2026-10-12'=>true,'2026-11-11'=>true,'2026-11-12'=>false,'2026-02-30'=>false] as $date=>$expected)check((bool)rbc68_event_in_notice_window($date,$now)===$expected,$date);
 $GLOBALS['mods']=['rbc68_hero_image'=>1,'rbc68_club_image'=>1];check(rbc68_home_backgrounds()===[1],'fallback sans doublons');
 $GLOBALS['mods']['rbc68_background_2']=2;check(rbc68_home_backgrounds()===[2],'fonds spécifiques prioritaires');
 $GLOBALS['mods']['rbc68_background_enabled']=false;check(rbc68_home_backgrounds()===[],'désactivation');
 $GLOBALS['news']=[];check(!rbc68_menu_has_news(),'aucune actualité');
 $GLOBALS['news']=[2];check(rbc68_menu_has_news(),'actualité présente');
 check($GLOBALS['query']['post_status']==='publish' && !$GLOBALS['query']['has_password'],'public seulement');
 check($GLOBALS['query']['date_query'][0]['column']==='post_date_gmt' && !$GLOBALS['query']['date_query'][0]['inclusive'],'publication UTC strictement récente');
 $GLOBALS['mods']=['rbc68_hero_image'=>1,'rbc68_club_image'=>2];
 rbc68_migrate_backgrounds();check(rbc68_home_backgrounds()===[1,2],'migration des fonds');
 $GLOBALS['mods']['rbc68_background_1']=0;$GLOBALS['mods']['rbc68_background_2']=0;
 check(rbc68_home_backgrounds()===[],'suppression définitive des fonds hérités');
 $GLOBALS['mods']['rbc68_background_4']=3;check(rbc68_home_backgrounds()===[3],'quatrième fond');
 $GLOBALS['events']=[(object)['ID'=>42]];$GLOBALS['dates'][42]=(new DateTimeImmutable('now',wp_timezone()))->format('Y-m-d');
 foreach(['internal'=>false,'interclub'=>false,'training'=>false,'public'=>true] as $kind=>$expected){$GLOBALS['kinds'][42]=$kind;check(rbc68_menu_has_events()===$expected,'filtre '.$kind);}
 echo "OK — bornes J/J+30/J+31, date invalide, sélection des fonds, filtre actualités\n";
