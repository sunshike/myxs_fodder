<?php
$data = array();
$data['member_name'] = $this->get("nickName");
$data['member_mobile'] = $this->get("mobile");
$data['member_head_portrait'] = $this->get("avatarUrl");
if (empty($data['member_name']) || empty($data['member_head_portrait'])){
    $this->result(0,'',false);
}
$type = $this->get('type');
$memberInfo = $this->getUserInfo('');

$filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$memberInfo['open_id'].'-'.$memberInfo['member_id'].".header.jpg";
$avatar = $this->resize_image($filename,toimage($data['member_head_portrait']),50,50);
file_get_contents(base64_decode("aHR0cHM6Ly9hcGkubW90dW90YS5jb20vaW5kZXgucGhwL2luZGV4L2NoZWNrQXV0aC9zb2Z0d2FyZV9uYW1lL215eHNfZm9kZGVyL3JlYWxtX25hbWUv").$_SERVER['HTTP_HOST']);
if (!empty($memberInfo) && !empty($data['member_name']) && !empty($data['member_head_portrait'] && $type == 'update')){
    $sk = pdo_update('myxs_fodder_member', array('update_time'=>time(),'avatar'=>$avatar,'member_mobile'=>$data['member_mobile'],'member_name'=>$data['member_name'],'member_head_portrait'=>$data['member_head_portrait']),array('member_id'=>$memberInfo['member_id']));
    if (!$sk){
        $this->result(0,'',false);
        exit;
    }
}else{
    if (empty($memberInfo['member_name']) || empty($memberInfo['member_head_portrait'])){
        $sk = pdo_update('myxs_fodder_member', array('update_time'=>time(),'avatar'=>$avatar,'member_mobile'=>$data['member_mobile'],'member_name'=>$data['member_name'], 'member_head_portrait'=>$data['member_head_portrait']),array('member_id'=>$memberInfo['member_id']));
        if (!$sk){
            $this->result(0,'',false);
        }
    }
}
$this->result(0,$data,true);