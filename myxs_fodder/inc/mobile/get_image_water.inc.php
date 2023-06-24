<?php
$member = $this->getUserInfo('');
$files=$this->get('files','');
$files2=$this->get('files2','');
$file_type=$this->get('file_type','');
$type=$this->get('type','');

if($type=="update"){
    $WaterMess = pdo_fetch("select stat_bg,stat_bg_s from " . tablename('myxs_fodder_water_bg') . " where uniacid = :uniacid and uid=:uid", array(":uid"=>$member['member_id'],':uniacid'=>$this->uniacid));
    if(empty($WaterMess)){
        $data=array();
        $data['create_time']=time();
        $data['stat_bg']= strstr($files,'upload/');
        $data['stat_bg_s']= strstr($files2,'upload/');
        $data['uniacid']=$this->uniacid;
        $data['uid'] = $member['member_id'];
        pdo_insert('myxs_fodder_water_bg', $data);
    }else{
        $update_data=array();
        $update_data['stat_bg']=strstr($files,'upload/');
        $update_data['stat_bg_s']=strstr($files2,'upload/');
        $data['create_time']=time();
        pdo_update('myxs_fodder_water_bg',$update_data,array('uid'=>$member['member_id'],'uniacid'=>$this->uniacid));
    }
    $WaterMess = pdo_fetch("select stat_bg,stat_bg_s from " . tablename('myxs_fodder_water_bg') . " where uniacid = :uniacid and uid=:uid", array(":uid"=>$member['member_id'],':uniacid'=>$this->uniacid));
    if(strpos(toimage($WaterMess['stat_bg']),'http://') !== false){
        $WaterMess['stat_bg']  = str_ireplace("http://","https://",toimage($WaterMess['stat_bg']));
    }else{
        $WaterMess['stat_bg'] = toimage($WaterMess['stat_bg']);
    }
    if(strpos(toimage($WaterMess['stat_bg_s']),'http://') !== false){
        $WaterMess['stat_bg_s']  = str_ireplace("http://","https://",toimage($WaterMess['stat_bg_s']));
    }else{
        $WaterMess['stat_bg_s'] = toimage($WaterMess['stat_bg_s']);
    }
    $this->result(0, '',$WaterMess);
}elseif($type=="look"){
    $WaterMess = pdo_fetch("select stat_bg,stat_bg_s from " . tablename('myxs_fodder_water_bg') . " where uniacid = :uniacid and uid=:uid", array(":uid"=>$member['member_id'],':uniacid'=>$this->uniacid));
    if(strpos(toimage($WaterMess['stat_bg']),'http://') !== false){
        $WaterMess['stat_bg']  = str_ireplace("http://","https://",toimage($WaterMess['stat_bg']));
    }else{
        $WaterMess['stat_bg'] = toimage($WaterMess['stat_bg']);
    }
    if(strpos(toimage($WaterMess['stat_bg_s']),'http://') !== false){
        $WaterMess['stat_bg_s']  = str_ireplace("http://","https://",toimage($WaterMess['stat_bg_s']));
    }else{
        $WaterMess['stat_bg_s'] = toimage($WaterMess['stat_bg_s']);
    }
    $this->result(0, '',$WaterMess);
}