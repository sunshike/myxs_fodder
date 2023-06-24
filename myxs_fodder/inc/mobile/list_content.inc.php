<?php
$class_id= $this->get('class_id', '');
$class_id = !empty($id) ? $id :$class_id;
$catname=$this->get('catname', '');
$defaultLeftCatName=$this->get('defaultLeftCatName', '');
$catId=intval($this->get('catId', ''));
$start = $this->get('start','');
$end = $this->get('end','');
$type = $this->get('type','');
$memberInfo = $this->getUserInfo('');
$grouping = $memberInfo['grouping_id'];
$member_group=explode(",",$grouping);
$limit=" limit ".$start.",".$end;

$system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'system'));
$system_content = json_decode($system['system_content'],true);
$system_system = json_decode($system['system'],true);
foreach ($system_content as $key =>$value){
    if(strpos(toimage($value),'http://') !== false){
        $system_content[$key] = str_ireplace("http://","https://",toimage($value));
    }else{
        $system_content[$key] = toimage($value);
    }
}

$where = ' ';
if($system_system['video_is_show'] == 2){
    $where = ' and type = "img" ';
}

if(!empty($catname)){
    $order_by="order by `content_id` desc";
    switch ($catname){
        case "降序":
            $order_by="order by `content_id` desc";
            break;
        case "升序":
            $order_by="order by `content_id` ASC";
            break;
        case "收藏数量":
            $order_by="order by `clnb` desc";
            break;
        case "下载数量":
            $order_by="order by `donnb` desc";
            break;
        case "转发数量":
            $order_by="order by `sharenb` desc";
            break;
    }

    if($defaultLeftCatName=="专属排序"){
        $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where content_status = :content_status and content_class = :content_class and class_id = :class_id and grouping_id =:grouping_id and uniacid = :uniacid and is_check=1  ".$where.$order_by.$limit, array(':content_status' => 1,':content_class'=>1,':class_id'=>$class_id,':grouping_id'=>$catId,':uniacid'=>$this->uniacid));
    }else{
        $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where grouping_id in( ".$memberInfo['grouping_id'].") and  content_status = :content_status and content_class = :content_class and class_id = :class_id  and uniacid = :uniacid and is_check=1 ".$where.$order_by.$limit, array(':content_status' => 1,':content_class'=>1,':class_id'=>$class_id,':uniacid'=>$this->uniacid));
    }


}else{
    if ($type == 'my_content'){
        $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where content_status > :content_status and content_class = :content_class and class_id = :class_id  and uniacid = :uniacid  and member_id = :member_id ".$where." order by `content_id` desc".$limit, array(':content_status' => 0,':content_class'=>1,':class_id'=>$class_id,':member_id'=>$memberInfo['member_id'],':uniacid'=>$this->uniacid));
    }else{
        if($class_id==888){
            $memberInfogrouping=str_replace("0","-1",$memberInfo['grouping_id']);
            $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where grouping_id in ( ".$memberInfogrouping.") and content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and is_check=1 ".$where."  order by `content_id` desc ".$limit, array(':content_status' => 1,':content_class'=>1,':uniacid'=>$this->uniacid));
//                    $this->result(0, '', $memberInfogrouping);die;
        }else{
            $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where grouping_id in ( ".$memberInfo['grouping_id'].") and content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and  class_id = :class_id and is_check=1 ".$where." order by `content_id` desc".$limit, array(':content_status' => 1,':content_class'=>1,':class_id'=>$class_id,':uniacid'=>$this->uniacid));
        }
    }
}

foreach ($contentList as $key => $value){
    $contentList[$key]['content'] = json_decode($value['content'],true);
    $contentList[$key]['content2'] = json_decode($value['content2'],true);
    $contentList[$key]['clnb'] = $contentList[$key]['clnb']+$contentList[$key]['fictitious_clnb'];
    $contentList[$key]['donnb'] = $contentList[$key]['donnb']+$contentList[$key]['fictitious_donnb'];
    $contentList[$key]['sharenb'] = $contentList[$key]['sharenb']+$contentList[$key]['fictitious_sharenb'];
    $contentList[$key]['likenum'] = $contentList[$key]['likenum']+$contentList[$key]['fictitious_likenum'];
    if(strpos(toimage($value['video_img']),'http://') !== false){
        $contentList[$key]['video_img'] = str_ireplace("http://","https://",toimage($value['video_img']));
    }else{
        $contentList[$key]['video_img'] = toimage($value['video_img']);
    }

    $contentList[$key]['is_member_content'] = $value['member_id'] === $memberInfo['member_id'] ? 1 : 0;
//            if ($contentList[$key]['member_id'] == 1){
    foreach ($contentList[$key]['content'] as $keys => $values){
        if(strpos(toimage($values),'http://') !== false){
            $contentList[$key]['content'][$keys] = str_ireplace("http://","https://",toimage($values));
        }else{
            $contentList[$key]['content'][$keys] = toimage($values);
        }
    }
    foreach ($contentList[$key]['content2'] as $keys2 => $values2){
        if(strpos(toimage($values2),'http://') !== false){
            $contentList[$key]['content2'][$keys2] = str_ireplace("http://","https://",toimage($values2));
        }else{
            $contentList[$key]['content2'][$keys2] = toimage($values2);
        }
    }
//            }

    $operationInfo = pdo_fetch("select status from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$value['content_id'],':operation'=>'sz',':uniacid'=>$memberInfo['uniacid']));
    $contentList[$key]['clstate'] = $operationInfo['status'] == 1 ? 1 : 0; //是否收藏，1收藏，0未收藏

    $operationInfoDz = pdo_fetch("select status from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$value['content_id'],':operation'=>'dz',':uniacid'=>$memberInfo['uniacid']));
    $contentList[$key]['is_like'] = $operationInfoDz['status'] == 1 ? 1 : 0; //是否点赞



    $contentList[$key]['content_siz'] = sizeof($contentList[$key]['content']);
    $member = $this->getUserInfo($contentList[$key]['member_id']);
    if (empty($member)){
        $contentList[$key]['member_name'] = $system_system['member_name'];
        $contentList[$key]['member_head_portrait'] = $system_content['logo_bg'];
    }else{
        $contentList[$key]['member_name'] = $member['member_name'];
        if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
            $contentList[$key]['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
        }else{
            $contentList[$key]['member_head_portrait'] = toimage($member['member_head_portrait']);
        }

        if(empty($member['avatar'])){
            $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$member['open_id'].'-'.$member['member_id'].".header.jpg";
            $avatar = $this->resize_image($filename,toimage($member['member_head_portrait']),50,50);
            if(strpos(toimage($avatar),'http://') !== false){
                $contentList[$key]['avatar'] = str_ireplace("http://","https://",toimage($avatar));
            }else{
                $contentList[$key]['avatar'] = toimage($avatar);
            }
            pdo_update('myxs_fodder_member',array('avatar'=>$avatar),array('uniacid'=>$this->uniacid,'member_id'=>$member['member_id']));
        }else{
            if(strpos(toimage($member['avatar']),'http://') !== false){
                $contentList[$key]['avatar'] = str_ireplace("http://","https://",toimage($member['avatar']));
            }else{
                $contentList[$key]['avatar'] = toimage($member['avatar']);
            }
        }
    }

    $total = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and a.discuss_type=0 ",array(':content_id'=>$value['content_id'],':uniacid'=>$this->uniacid));
    $contentList[$key]['discuss'] = intval($total);

    if($value['circle_id'] > 0){
        $community = pdo_fetch("select id,group_name,group_class from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$this->uniacid,':id'=>$value['circle_id']));
        $contentList[$key]['circle'] = $community['group_name'];
    }else{
        $contentList[$key]['circle'] = '';
    }
    if($value['grouping_id'] > 0){
        $grouping= pdo_fetch("select grouping_name from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid and grouping_id=:grouping_id", array(':uniacid'=>$this->uniacid,':grouping_id'=>$value['grouping_id']));
        $contentList[$key]['grouping_name'] = $grouping['grouping_name'];
    }else{
        $contentList[$key]['grouping_name'] = '';
    }


    unset($contentList[$key]['fictitious_clnb']);
    unset($contentList[$key]['fictitious_donnb']);
    unset($contentList[$key]['fictitious_sharenb']);
    unset($contentList[$key]['fictitious_likenum']);
    unset($contentList[$key]['class_id']);
    unset($contentList[$key]['content_class']);
    unset($contentList[$key]['grouping_id']);
    unset($contentList[$key]['uniacid']);
    unset($contentList[$key]['content_siz']);

}
$data = array();
if(!empty($contentList)){
    if($start%5==0){
        array_push( $contentList,$this->advert('content',1));
    }
}
$data['content'] = $contentList;

$this->result(0, '', $data);