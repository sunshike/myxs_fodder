<?php
$class_id= $this->get('class_id', '');
$memberInfo = $this->getUserInfo('');
$limit=" limit 0,10";

$system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'system'));
$system_content = json_decode($system['system_content'],true);
$system_system = json_decode($system['system'],true);
foreach ($system_content as $key =>$value){
    $system_content[$key] = toimage($value);
}
if($system_system['video_is_show'] == 1){
    $where = ' content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and  class_id = :class_id';
}else{
    $where = ' content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and  class_id = :class_id and type ="img"';
}
$param = array(':content_status' => 1,':content_class'=>1,':class_id'=>$class_id,':uniacid'=>$this->uniacid);
$contentList = pdo_fetchall("select content_id,class_id,member_id,create_time,text,type,uniacid from " . tablename('myxs_fodder_content') . " where grouping_id in ( ".$memberInfo['grouping_id'].") and ".$where." order by `fictitious_donnb` desc,`donnb` desc".$limit, $param);
if(!empty($contentList)){
    foreach ($contentList as $key=>$v){
        $member = $this->getUserInfo($contentList[$key]['member_id']);
        if (empty($member)){
            $contentList[$key]['member_name'] = $system_system['member_name'];
            $contentList[$key]['member_head_portrait'] = $system_content['logo_bg'];
        }else{
            $contentList[$key]['member_name'] = $member['member_name'];
            $contentList[$key]['member_head_portrait'] = toimage($member['member_head_portrait']);
        }
    }
}
$this->result(0, '', $contentList);