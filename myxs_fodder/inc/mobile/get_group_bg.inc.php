<?php
$member = $this->getUserInfo('');
$group_id=$this->get('group_id','');
$files=$this->get('files','');
$file_type=$this->get('file_type','');
$type=$this->get('type','');

if($type=="update"){
    $groupBgMess = pdo_fetch("select bg_id from " . tablename('myxs_fodder_grouping_bg') . " where grouping_id=:grouping_id and uniacid = :uniacid and admin_id=:admin_id", array(':grouping_id'=>intval($group_id),":admin_id"=>$member['member_id'],':uniacid'=>$this->uniacid));
    if(empty($groupBgMess)){
        $data=array();
        $data['grouping_id']=$group_id;
        $data['admin_id']=$member['member_id'];
        $data['create_time']=time();
        $data['stat_bg']=$files;
        $data['uniacid']=$this->uniacid;
        pdo_insert('myxs_fodder_grouping_bg', $data);
    }else{
        $update_data=array();
        $update_data['stat_bg']=$files;
        $data['create_time']=time();
        pdo_update('myxs_fodder_grouping_bg',$update_data,array('grouping_id'=>$group_id,'uniacid'=>$this->uniacid));
    }
    $groupBgNowMess = pdo_fetch("select stat_bg from " . tablename('myxs_fodder_grouping_bg') . " where grouping_id=:grouping_id and uniacid = :uniacid and admin_id=:admin_id", array(':grouping_id'=>intval($group_id),":admin_id"=>$member['member_id'],':uniacid'=>$this->uniacid));
    if(strpos(toimage($groupBgNowMess['stat_bg']),'http://') !== false){
        $groupBgNowMess['stat_bg']  = str_ireplace("http://","https://",toimage($groupBgNowMess['stat_bg']));
    }else{
        $groupBgNowMess['stat_bg'] = toimage($groupBgNowMess['stat_bg']);
    }
    $this->result(0, '',$groupBgNowMess);
}elseif($type=="look"){
    $groupBgMess = pdo_fetch("select stat_bg from " . tablename('myxs_fodder_grouping_bg') . " where grouping_id=:grouping_id and uniacid = :uniacid", array(':grouping_id'=>intval($group_id),':uniacid'=>$this->uniacid));
    if(strpos($groupBgMess['stat_bg'],'http://') !== false){
        $groupBgMess['stat_bg']  = str_ireplace("http://","https://",$groupBgMess['stat_bg']);
    }
    $this->result(0, '',$groupBgMess);
}