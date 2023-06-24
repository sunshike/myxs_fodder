<?php
$user_id = $this->get('user_id', '');
$fz_id=$this->get('fz_id', '');
$user=pdo_fetch("select grouping_id from ".tablename('myxs_fodder_member')." where member_id=:member_id",array(':member_id'=>$user_id));
$grouping = explode(',',$user['grouping_id']);
foreach ($grouping as $k=>$v){
    if($v==$fz_id){
        unset($grouping[$k]);
    }
}
$userNowGroup=implode(',',$grouping);
$state=pdo_update('myxs_fodder_member',array('grouping_id'=>$userNowGroup),array('member_id'=>$user_id));
$this->result(0, '', $state);