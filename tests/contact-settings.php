<?php
// Vérifie le routage réel sans serveur mail ni WordPress.
define('ABSPATH', __DIR__);
function add_action(...$args){}
function get_theme_mod($key,$default=null){return $GLOBALS['mods'][$key] ?? $default;}
function sanitize_text_field($v){return trim(strip_tags($v));}
function sanitize_textarea_field($v){return trim(strip_tags($v));}
function sanitize_key($v){return preg_replace('/[^a-z0-9_-]/','',strtolower($v));}
function sanitize_email($v){return filter_var($v,FILTER_SANITIZE_EMAIL);}
function is_email($v){return filter_var($v,FILTER_VALIDATE_EMAIL);}
function esc_url_raw($v,$protocols=[]){return preg_match('~^https?://~',$v)?$v:'';}
function absint($v){return abs((int)$v);}
function wp_unslash($v){return $v;}
function wp_verify_nonce($v,$action){return $v==='valid';}
function get_option($key){return 'admin@example.org';}
function rbc68_contact_redirect($status){throw new RuntimeException($status);}
function wp_mail($recipient,$subject,$body,$headers){$GLOBALS['sent'][]=[$recipient,$subject];return true;}
function check($ok,$message){if(!$ok)throw new Exception($message);}
require dirname(__DIR__).'/inc/customizer-content.php';
$source=file_get_contents(dirname(__DIR__).'/functions.php');
$start=strpos($source,'function rbc68_handle_contact()');
$end=strpos($source,"add_action( 'admin_post_nopriv_rbc68_contact'",$start);
eval(substr($source,$start,$end-$start));
$GLOBALS['mods']=['rbc68_contact_routes'=>[['label'=>'Tournoi','emails'=>'first@example.org; second@example.org, invalid']]];
$routes=rbc68_contact_routes();$key=array_key_first($routes);
check(count($routes)===1 && $routes[$key]['emails']==='first@example.org, second@example.org','sanitisation destinataires');
$GLOBALS['mods']['rbc68_sponsor_1_name']='Ancien';$GLOBALS['mods']['rbc68_sponsor_1_logo']=4;
check(rbc68_sponsors()[0]['logo']===4,'sponsors hérités');
$GLOBALS['mods']['rbc68_sponsors_list']=[];check(rbc68_sponsors()===[],'retrait sponsors conservé');
check(count(rbc68_sanitize_sponsors(array_fill(0,8,['name'=>'Sponsor','logo'=>1])))===8,'pas de limite à deux');
$valid=['rbc68_contact_nonce'=>'valid','name'=>'Visiteur','email'=>'visitor@example.org','subject'=>$key,'message'=>'Message de test assez long','consent'=>'on','recipient'=>'intruder@example.org'];
function send_case($data,$expected){$_POST=$data;try{rbc68_handle_contact();}catch(RuntimeException $e){check($e->getMessage()===$expected,'statut du formulaire');}}
$GLOBALS['sent']=[];send_case($valid,'success');
check($GLOBALS['sent'][0][0]===['first@example.org','second@example.org'],'routage exclusivement serveur');
send_case(array_merge($valid,['website'=>'https://spam.example']),'success');check(count($GLOBALS['sent'])===1,'honeypot bloque envoi');
send_case(array_merge($valid,['subject'=>'inconnu']),'error');
send_case(array_merge($valid,['email'=>['malformed']]),'error');
send_case(array_merge($valid,['rbc68_contact_nonce'=>['malformed']]),'error');
check(count($GLOBALS['sent'])===1,'aucun envoi invalide');
echo "OK — destinataires, sujets, honeypot, données malformées, sponsors sans limite\n";
