<?php
$member = $this->getUserInfo('');
$nowCode = $this->get('nowCode', ''); //当前分组邀请码
$updateCode=$this->get('updateCode',''); //修改后分组邀请码
$class_id=$this->get('class_id',''); //分组id
$groupMess = pdo_fetch("select grouping_passwd from " . tablename('myxs_fodder_grouping') . " where grouping_id=:grouping_id and uniacid = :uniacid", array(':grouping_id'=>intval($class_id),':uniacid'=>$this->uniacid));
$groupPass=pdo_fetch("select grouping_id from " . tablename('myxs_fodder_grouping') . " where grouping_passwd=:grouping_passwd and uniacid = :uniacid", array(':grouping_passwd'=>intval($updateCode),':uniacid'=>$this->uniacid));
if(!empty($groupPass)){
    $this->result(0, '', array('status'=>false,'mess'=>"当前邀请码已存在，请重新输入"));
}
if(empty($updateCode)){
    $this->result(0, '', array('status'=>false,'mess'=>"请输入邀请码"));
}
if($nowCode==$updateCode){
    $this->result(0, '', array('status'=>false,'mess'=>"原邀请码与修改后邀请码一致，请重新输入"));
}
if(empty($groupMess)){
    $this->result(0, '', array('status'=>false,'mess'=>"当前分组不存在，请刷新后重试"));
}
if($nowCode != $groupMess['grouping_passwd']){
    $this->result(0, '', array('status'=>false,'mess'=>"当前分组邀请码错误，请重试"));
}
$status=pdo_update('myxs_fodder_grouping',array("grouping_passwd"=>$updateCode,"update_time"=>time(),"update_member_id"=>$member['member_id']),array("grouping_id"=>intval($class_id)));
$this->result(0, '', array('status'=>$status,'mess'=>"修改成功"));