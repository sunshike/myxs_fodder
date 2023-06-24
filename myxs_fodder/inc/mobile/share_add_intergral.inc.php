<?php
global $_GPC,$_W;
$shareId = intval($this->get('shareId'));
$content_id = intval($this->get('content_id'));

$memberInfo = $this->getUserInfo('');
$shareMemberInfo = pdo_fetch("select member_id,uniacid,intergral from ".tablename('myxs_fodder_member')."where member_id =:member_id",array(':member_id'=>$shareId));

$contentInfo = pdo_fetch("select content_id,sharenb,content_class from ".tablename('myxs_fodder_content')."where content_id =:content_id",array(':content_id'=>$content_id));
$system = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'intergral'));
$system_seting = json_decode($system['system'],true);

$datas = array('sharenb'=>$contentInfo['sharenb']+1);
$update_state=pdo_update('myxs_fodder_content', $datas,array('content_id'=>$contentInfo['content_id']));
if($update_state){
    $operationData = array('identity'=>'user','operation'=>'fx','content_id'=>$content_id,'member_id'=>$shareMemberInfo['member_id'],'content_class'=>$contentInfo['content_class'],'create_time'=>time(),'uniacid'=>$shareMemberInfo['uniacid'],'status'=>1);
    pdo_insert('myxs_fodder_operation_log', $operationData);
    if($shareId!=$memberInfo['member_id']){
        $shareTimeArr = pdo_fetch("select inter_id  from ".tablename("myxs_fodder_member_intergral_log")." where member_id=:member_id and get_member_id=:get_member_id and content_id=:content_id and  uniacid=:uniacid",array(':member_id'=>$shareMemberInfo['member_id'],':get_member_id'=>$memberInfo['member_id'],'content_id'=>$content_id,':uniacid'=>$memberInfo['uniacid']));
        if(empty($shareTimeArr)){
            pdo_update('myxs_fodder_member', array('intergral' => $shareMemberInfo['intergral'] + $system_seting['rewardIntergral']), array('member_id' => $shareId));
            $intergral_log_data = array('uniacid' => $shareMemberInfo['uniacid'],'get_member_id'=>$memberInfo['member_id'], 'member_id' => $shareMemberInfo['member_id'], 'text' => '素材分享', 'type' => 2, 'amount' => $system_seting['rewardIntergral'], 'add_time' => time(), 'content_id' => $content_id, 'operation' => 'fx');
            pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
            $status = 1;
            $this->result(0, '', array('status'=>$status));
        }
    }
}