<?php
global $_W, $_GPC;
$system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'system'));
$system_content = json_decode($system['system_content'],true);

$system_intergral = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'intergral'));
$system_intergral_content = json_decode($system_intergral['system'],true);
foreach ($system_content as $key =>$value){
    if(strpos(toimage($value),'http://') !== false){
        $system_content[$key]  = str_ireplace("http://","https://",toimage($value));
    }else{
        $system_content[$key] = toimage($value);
    }
}
if(strpos(toimage($system_intergral_content['adminErWeiImg']),'http://') !== false){
    $system_intergral_content['adminErWeiImg'] = str_ireplace("http://","https://",toimage($system_intergral_content['adminErWeiImg']));
}else{
    $system_intergral_content['adminErWeiImg'] = toimage($system_intergral_content['adminErWeiImg']);
}
$system['system_content'] = $system_content;
$system['system_basic'] = json_decode($system['system'],true);

$data = array();
$data['system'] = $system;
$data['intergral']=$system_intergral_content;
$data['member'] = $this->doPageMember(true);
$s = json_decode($system['system'],true);
$a = array();
$a["text"] = "";
$a["colorIndex"] = "0";
$a["arrIndex"] = "0";
$a["erwei"] = $system_content['qr_bg'];

if (!$s['watermark_status'] || empty($data['member']['watermark'])){
    $data['member']['watermark'] = json_encode($a);
}

$data['class'] = $this->doPageListClass(true);
//        $data['class'][888]=array(
//            'class_id'=>888,
//            'class_name'=>'专属素材'
//        );
$data['member_advert'] = array('member'=>$this->advert('member',1),'member_set'=>$this->advert('member_set',1));

$WaterMess = pdo_fetch("select stat_bg from " . tablename('myxs_fodder_water_bg') . " where uniacid = :uniacid and uid=:uid", array(":uid"=> $data['member']['member_id'],':uniacid'=>$_W['uniacid']));
$data['member_water'] =$WaterMess['stat_bg'];
if(strpos(toimage($WaterMess['stat_bg']),'http://') !== false){
    $data['member_water']  = str_ireplace("http://","https://",toimage($WaterMess['stat_bg']));
}else{
    $data['member_water'] = toimage($WaterMess['stat_bg']);
}



$todayStart= strtotime(date('Y-m-d 00:00:00', time())); //2019-01-17 00:00:00
$todayEnd= strtotime(date('Y-m-d 23:59:59', time())); //2019-01-17 23:59:59

$intergralAllToday = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and uniacid=:uniacid and type =2 and add_time>=:time1 and add_time<=:time2", array(':member_id' => $data['member']['member_id'], ':uniacid' =>$this->uniacid,':time1'=>$todayStart,':time2'=>$todayEnd));
$data['today_intergral'] = floatval($intergralAllToday);

$Login = pdo_fetch("select inter_id from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and operation=:operation and uniacid=:uniacid and add_time>=:time1 and add_time<=:time2", array(':member_id' => $data['member']['member_id'], ':operation' => 'login', ':uniacid' => $this->uniacid,':time1'=>$todayStart,':time2'=>$todayEnd));
if(!empty($Login)){
    $data['is_login_send'] = true;
}else{
    $data['is_login_send'] = false;
}
$data['content_advert'] = $this->advertAll('content',1);

$data['intergral_advert'] = $this->advert('intergral',1);

$this->result(0,'',$data);