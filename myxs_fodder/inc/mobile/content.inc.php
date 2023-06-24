<?php
global $_W,$_GPC;
$memberInfo = $this->getUserInfo('');
$type = trim($this->get('type'));
$content_id = trim($this->get('content_id'));
if($type=='delete'){
    $row = pdo_fetch("SELECT content_id FROM " . tablename('myxs_fodder_content') . " WHERE content_id = :content_id", array(':content_id' => $content_id));
    if (empty($row)) {
        $this->result(0,'抱歉，内容不存在或是已经被删除！', array('status'=>false));
    }
    $status = pdo_update('myxs_fodder_content',array('content_status'=>0),array('content_id'=>$content_id));
    if ($status){
        $this->result(0, '删除成功', array('status'=>true));
    }else{
        $this->result(0, '操作失败', array('status'=>false));
    }
}elseif ($type=='hide') {
    $row = pdo_fetch("SELECT content_id,content_status FROM " . tablename('myxs_fodder_content') . " WHERE content_id = :content_id", array(':content_id' => $content_id));
    if (empty($row)) {
        $this->result(0, '抱歉，内容不存在或是已经被删除！', array('status' => false));
    }
    $status = $row['content_status'] == 2 ? 1 : 2;
    $status = pdo_update('myxs_fodder_content', array('content_status' => $status), array('content_id' => $content_id));

    if ($status) {
        $this->result(0, '隐藏成功', array('status' => true));
    } else {
        $this->result(0, '隐藏失败', array('status' => false));
    }
}elseif ($type=='take'){
    // 获取当前用户水印设置
    $memberInfo = $this->getUserInfo('');



    $system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'system'));
    $system_content = json_decode($system['system_content'],true);
    $system_system = json_decode($system['system'],true);
    foreach ($system_content as $key =>$value){
        $system_content[$key] = toimage($value);
    }
    $a = array();   //{"text":"","colorIndex":"0","arrIndex":"0","erwei":"https:\/\/img.huamioo.com\/images\/0\/2019\/04\/axIQ6hDvyhi88x1mfmzOxGh1Xx8IZP.jpg"}
    $a["text"] = "";
    $a["colorIndex"] = "0";
    $a["arrIndex"] = "0";
    $a["erwei"] = $system_content['qr_bg'];

    if (!$system_system['watermark_status'] || empty($memberInfo['watermark'])){
        $watermark = json_encode($a);
    }else{
        $watermark = $memberInfo['watermark'];
    }




    $content = pdo_fetch("SELECT content,content_id,member_id FROM " . tablename('myxs_fodder_content') . " WHERE content_id = :content_id", array(':content_id' => $content_id));
    $content['content'] = json_decode($content['content'],true);
    foreach ($content['content'] as $k => $v){
        $content['content'][$k] = toimage($v);
    }

    $operation = pdo_fetch("select status from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$content['content_id'],':operation'=>'sz',':uniacid'=>$memberInfo['uniacid']));

    $content['clstate'] = $operation['status'] == 1 ? 1 : 0; //是否收藏，1收藏，0未收藏

    $member = $this->getUserInfo($content['member_id']);
    if (empty($member)){
        $content['member_name'] = $system_system['member_name'];
        $content['member_head_portrait'] = $system_content['logo_bg'];
    }else{
        $content['member_name'] = $member['member_name'];
        $content['member_head_portrait'] = toimage($member['member_head_portrait']);
    }

    $content['watermark'] = $watermark;
    $this->result(0, '', $content);
}else{
    $member=$this->getUserInfo('');
    $data=array();
    $data['class_id']=$this->get('class_id');
    $data['circle_id']=$this->get('circle_id',0);
    $data['member_id']=$member['member_id'];
    $data['grouping_id']=$this->get('grouping_id');
    $data['text']=$this->get('textDat');
    $data['clnb']=0;
    $data['donnb']=0;
    $data['sharenb']=0;
    $file = explode(',',$_GPC["files"]);
    foreach ($file as $k=>$v){
        $file[$k] = strstr($v,'upload/');
    }
    $data['content']=json_encode($file);

    $file2 = explode(',',$_GPC["files2"]);
    foreach ($file2 as $k=>$v){
        $file2[$k] = strstr($v,'upload/');
    }
    $data['content2']=json_encode($file2);

    $data['content_class']=1;
    $data['content_status']=$this->get('ShowBl') == 'true' ? 1 : 2;
    $data['create_time']=time();
    $data['update_time']=time();
    $data['type']=$_GPC['file_type'];
    if($data['type'] == 'video'){
        $data['is_check'] = 1;
        $video_img = strstr($_GPC['video_img'],'upload/');
        $data['video_img'] = $video_img;
    }
    $data['uniacid']=$this->uniacid;
    $status =pdo_insert('myxs_fodder_content',$data);
    $insertid = pdo_insertid();
    if ($status){
        $system = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid' => $_W['uniacid'], ':system_code' => 'intergral'));
        $system_seting = json_decode($system['system'], true);
        $releaseSendIntergral = $system_seting['releaseSendIntergral'];
        if($releaseSendIntergral>0){
            $addIntergral = intval($member['intergral']) + intval($releaseSendIntergral);
            $sta = pdo_update('myxs_fodder_member', array('intergral' => $addIntergral), array('member_id' => $member['member_id']));
            if ($sta) {
                $intergral_log_data = array('uniacid' => $this->uniacid, 'member_id' => $member['member_id'], 'text' => '发布素材赠送', 'type' => 2, 'amount' => $releaseSendIntergral, 'add_time' => time(), 'operation' => 'fbzs','content_id'=>$insertid);
                pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
            }
        }

        $this->result(0, '发布成功', array('status'=>true));
    }else{
        $this->result(0, '发布失败', array('status'=>false));
    }
}