<?php
/** Tri du comité : variantes de rôles et séparation du bureau. */
define('ABSPATH', __DIR__);
function add_action(...$a) {}
function add_filter(...$a) {}
function remove_accents($s) { return strtr($s, ['é'=>'e','É'=>'E','è'=>'e','È'=>'E','à'=>'a']); }
function get_post_meta($id,$key,$single) { return $GLOBALS['meta'][$id][$key] ?? ''; }
require dirname(__DIR__).'/inc/committee.php';
function check($ok,$message) { if(!$ok) throw new Exception($message); }
foreach ([ 'Secrétaire adjoint'=>1,'SECRÉTAIRE ADJOINTE'=>1,'Secrétaire'=>2,'Trésorier adjoint'=>3,'Trésorière adjointe'=>3,'Trésorier'=>4,'Trésorière'=>4,'Vice-Président'=>5,'Vice‑présidente'=>5,'Président'=>6,'Présidente'=>6,'Directeur Technique'=>0,''=>0 ] as $role=>$expected) {
 check(rbc68_committee_role_rank($role)===$expected,$role);
}
$members=[];
foreach ([['Zorro','Président'],['Denni','Trésorier'],['Beta','Bénévole'],['Walter','Vice-Présidente'],['Rachel','Secrétaire'],['Alpha','Directrice technique'],['Adjoint T','Trésorière adjointe'],['Adjoint S','Secrétaire adjoint']] as $id=>$data) {
 $members[]=(object)['ID'=>$id,'post_title'=>$data[0]];
 $GLOBALS['meta'][$id]['rbc68_member_role']=$data[1];
}
$groups=rbc68_committee_groups($members);
check(array_column($groups['members'],'post_title')===['Alpha','Beta'],'ordre alphabétique des membres');
check(array_column($groups['office'],'post_title')===['Adjoint S','Rachel','Adjoint T','Denni','Walter','Zorro'],'ordre du bureau');
check(rbc68_committee_groups([])===['members'=>[],'office'=>[]],'comité vide');
echo "OK — rôles masculins/féminins, accents/tirets, ordre et groupes du comité\n";
