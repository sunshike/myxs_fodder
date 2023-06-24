<?php
global $_GPC,$_W;
$type = trim($this->get('types'));
$content_id = intval($this->get('id'));
$is_index = intval($this->get('is_index'));
$counts= intval($this->get('counts'));
$memberInfo = $this->getUserInfo('');
$operationInfo = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$content_id,':operation'=>$type,':uniacid'=>$memberInfo['uniacid']));
$contentInfo = pdo_fetch("select member_id,clnb,content_id,content_class,donnb,likenum from ".tablename('myxs_fodder_content')." where content_id =:content_id",array(':content_id'=>$content_id));
$system = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'intergral'));
$system_seting = json_decode($system['system'],true);
$status = 0;
if ($type === 'sz'){
    if ($operationInfo['status'] == 1 || $operationInfo['status'] == 2 ){
        $status = $operationInfo['status'] == 1 ? 0 : 1;
        pdo_update('myxs_fodder_operation_log', array('status'=>$operationInfo['status'] == 1 ? 2 : 1),array('log_id'=>$operationInfo['log_id']));
        pdo_update('myxs_fodder_content', array('clnb'=>$operationInfo['status'] == 1 ? $contentInfo['clnb']-1 : $contentInfo['clnb']+1),array('content_id'=>$contentInfo['content_id']));
    }else{
        $data = array('identity'=>'user','operation'=>$type,'content_id'=>$content_id,'member_id'=>$memberInfo['member_id'],'content_class'=>$contentInfo['content_class'],'create_time'=>time(),'uniacid'=>$memberInfo['uniacid'],'status'=>1);
        pdo_insert('myxs_fodder_operation_log', $data);
        pdo_update('myxs_fodder_content', array('clnb'=>$contentInfo['clnb']+1),array('content_id'=>$contentInfo['content_id']));
        $status = 1;
    }
    $this->result(0, '', array('status'=>$status));
}elseif ($type === 'dz'){
    if ($operationInfo['status'] == 1 || $operationInfo['status'] == 2 ){
        $status = $operationInfo['status'] == 1 ? 0 : 1;
        pdo_update('myxs_fodder_operation_log', array('status'=>$operationInfo['status'] == 1 ? 2 : 1),array('log_id'=>$operationInfo['log_id']));
        pdo_update('myxs_fodder_content', array('likenum'=>$operationInfo['status'] == 1 ? $contentInfo['likenum']-1 : $contentInfo['likenum']+1),array('content_id'=>$contentInfo['content_id']));
    }else{
        $data = array('identity'=>'user','operation'=>$type,'content_id'=>$content_id,'member_id'=>$memberInfo['member_id'],'content_class'=>$contentInfo['content_class'],'create_time'=>time(),'uniacid'=>$memberInfo['uniacid'],'status'=>1);
        pdo_insert('myxs_fodder_operation_log', $data);
        pdo_update('myxs_fodder_content', array('likenum'=>$contentInfo['likenum']+1),array('content_id'=>$contentInfo['content_id']));
        $status = 1;
    }
    $this->result(0, '', array('status'=>$status));
}elseif ($type === 'xz'){
    if($contentInfo['member_id']!=$memberInfo['member_id']){
        if($memberInfo['intergral']==0 || $system_seting['takeOutIntergral']>$memberInfo['intergral']){
            $this->result(0, '', array('state'=>0,'mess'=>'您的积分不足，请联系管理员充值或转发素材获得积分','title'=>'积分不足提醒'));
            return;
        }
    }
    if(empty($operationInfo)){
        $data = array('identity'=>'user','operation'=>$type,'content_id'=>$content_id,'member_id'=>$memberInfo['member_id'],'content_class'=>$contentInfo['content_class'],'create_time'=>time(),'uniacid'=>$memberInfo['uniacid'],'status'=>1);
        pdo_insert('myxs_fodder_operation_log', $data);
    }
    if($is_index==2){
        if($counts==1){
            pdo_update('myxs_fodder_content', array('donnb'=>$contentInfo['donnb']+1),array('content_id'=>$contentInfo['content_id']));
            if($contentInfo['member_id']!=$memberInfo['member_id']) {
                pdo_update('myxs_fodder_member', array('intergral' => $memberInfo['intergral'] - $system_seting['takeOutIntergral']), array('member_id' => $memberInfo['member_id']));
                $intergral_log_data = array('uniacid' => $memberInfo['uniacid'], 'member_id' => $memberInfo['member_id'], 'text' => '素材下载', 'type' => 1, 'amount' => $system_seting['takeOutIntergral'], 'add_time' => time(), 'content_id' => $content_id, 'operation' => $type);
                pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
            }
        }
    }
    $this->result(0, '', array('state'=>1));
} else{
//                $shareTime=intval($system_seting['forwardIntergral']);
//                $today = strtotime(date("Y-m-d"),time());
//                $mingt=strtotime(date('Y-m-d') . ' +1 day');
//
//                $shareTimeArr = pdo_fetch("select count(*) as sharecount from ".tablename("myxs_fodder_member_intergral_log")." where member_id=:member_id and add_time>:start and add_time<:endtime and uniacid=:uniacid",array(':member_id'=>$memberInfo['member_id'],':start'=>$today,':endtime'=>$mingt,':uniacid'=>$memberInfo['uniacid']));
//                if(intval($shareTimeArr['sharecount'])>=$shareTime){
//                    $status = 0;
//                    $this->result(0, '', array('status'=>$status,'message'=>'当日获取分享积分已达上限'));
//                }else{
//                    $status = 1;
//                    $this->result(0, '', array('status'=>$status));
//                }
}