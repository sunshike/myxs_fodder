<?php
global $_W,$_GPC;
$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
$sql = 'select sign_img from '.tablename('myxs_fodder_day_sign').'where display_time >= :stat_time and display_time <= :end_time and uniacid = :uniacid and sign_status = :sign_status';
$ListDaySign = pdo_fetch($sql,array(':stat_time'=>$beginToday,':end_time'=>$endToday,':uniacid'=>$this->uniacid,':sign_status'=>1));

$system = pdo_fetch("select system_content from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'system'));
$system_content = json_decode($system['system_content'],true);
foreach ($system_content as $key =>$value){
    $system_content[$key] = toimage($value);
}
$day = date("d");
$ListDaySign['day_url'] = $_W['siteroot'].'addons/myxs_fodder/img/'.$day.'.jpg';
$ListDaySign['qr_img'] = $system_content['qr_bg'];
$ListDaySign['sign_img'] = toimage($ListDaySign['sign_img']);
$this->result(0, '', $ListDaySign);