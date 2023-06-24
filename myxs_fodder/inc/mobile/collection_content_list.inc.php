<?php
$type = $this->get('type','');
$memberInfo = $this->getUserInfo('');
$log_id = $this->get('log_id','');
$start = $this->get('start','');
$end = $this->get('end','');
$limit=" limit ".$start.",".$end;
$operationInfo = pdo_fetchall("select content_id,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and operation = :operation and status = :status and uniacid = :uniacid order by `log_id` desc".$limit, array(':member_id' => $memberInfo['member_id'],':status'=>1,':operation'=>'sz',':uniacid'=>$memberInfo['uniacid']));

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

$CollectionContentList = array();
foreach ($operationInfo as $key =>$value){
    $condition = array(':content_status' => 1,':content_id'=>$value['content_id'],':uniacid'=>$this->uniacid);
    $operationInfo[$key] = pdo_fetch("select * from " . tablename('myxs_fodder_content') . " where content_status = :content_status and content_id = :content_id  and uniacid = :uniacid ", $condition);
    if(empty($operationInfo[$key])){
        unset($operationInfo[$key]);
        continue;
    }

    if($system_system['video_is_show'] == 2){
        if($operationInfo[$key]['type'] == 'video'){
            unset($operationInfo[$key]);
            continue;
        }

    }

    $operationInfo[$key]['clnb'] = $operationInfo[$key]['clnb']+$operationInfo[$key]['fictitious_clnb'];
    $operationInfo[$key]['donnb'] = $operationInfo[$key]['donnb']+$operationInfo[$key]['fictitious_donnb'];
    $operationInfo[$key]['sharenb'] = $operationInfo[$key]['sharenb']+$operationInfo[$key]['fictitious_sharenb'];
    $operationInfo[$key]['likenum'] = $operationInfo[$key]['likenum']+$operationInfo[$key]['fictitious_likenum'];
    if(strpos(toimage($value['video_img']),'http://') !== false){
        $operationInfo[$key]['video_img'] = str_ireplace("http://","https://",toimage($value['video_img']));
    }else{
        $operationInfo[$key]['video_img'] = toimage($value['video_img']);
    }

    $operationInfo[$key]['content'] = json_decode($operationInfo[$key]["content"],true);
    $operationInfo[$key]['content2'] = json_decode($operationInfo[$key]["content2"],true);
    $operationInfo[$key]['content_siz'] = sizeof($operationInfo[$key]['content']);



//            if ($CollectionContentList[$key]['member_id'] == 1){
    foreach ($operationInfo[$key]['content'] as $k => $v){
        if(strpos(toimage($v),'http://') !== false){
            $operationInfo[$key]['content'][$k] = str_ireplace("http://","https://",toimage($v));
        }else{
            $operationInfo[$key]['content'][$k] = toimage($v);
        }
    }
    foreach ($operationInfo[$key]['content2'] as $keys2 => $values2){
        if(strpos(toimage($values2),'http://') !== false){
            $operationInfo[$key]['content2'][$keys2] = str_ireplace("http://","https://",toimage($values2));
        }else{
            $operationInfo[$key]['content2'][$keys2] = toimage($values2);
        }
    }
//            }
    $operationInfo[$key]['clstate'] = 1; //是否收藏，1收藏，0未收藏

    $member = $this->getUserInfo($operationInfo[$key]['member_id']);
    if (empty($member)){
        $operationInfo[$key]['member_name'] = $system_system['member_name'];
        $operationInfo[$key]['member_head_portrait'] = $system_content['logo_bg'];
    }else{
        $operationInfo[$key]['member_name'] = $member['member_name'];
        if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
            $operationInfo[$key]['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
        }else{
            $operationInfo[$key]['member_head_portrait'] = toimage($member['member_head_portrait']);
        }
        if(empty($member['avatar'])){
            $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$member['open_id'].'-'.$member['member_id'].".header.jpg";
            $avatar = $this->resize_image($filename,toimage($member['member_head_portrait']),50,50);
            if(strpos(toimage($avatar),'http://') !== false){
                $operationInfo[$key]['avatar'] = str_ireplace("http://","https://",toimage($avatar));
            }else{
                $operationInfo[$key]['avatar'] = toimage($avatar);
            }
            pdo_update('myxs_fodder_member',array('avatar'=>$avatar),array('uniacid'=>$this->uniacid,'member_id'=>$member['member_id']));
        }else{
            if(strpos(toimage($member['avatar']),'http://') !== false){
                $operationInfo[$key]['avatar'] = str_ireplace("http://","https://",toimage($member['avatar']));
            }else{
                $operationInfo[$key]['avatar'] = toimage($member['avatar']);
            }

        }

    }

    if($operationInfo[$key]['circle_id'] > 0){
        $community = pdo_fetch("select id,group_name,group_class from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$this->uniacid,':id'=>$operationInfo[$key]['circle_id']));
        $operationInfo[$key]['circle'] = $community['group_name'];
    }else{
        $operationInfo[$key]['circle'] = '';
    }
    if($operationInfo[$key]['grouping_id'] > 0){
        $grouping= pdo_fetch("select grouping_name from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid and grouping_id=:grouping_id", array(':uniacid'=>$this->uniacid,':grouping_id'=>$operationInfo[$key]['grouping_id']));
        $operationInfo[$key]['grouping_name'] = $grouping['grouping_name'];
    }else{
        $operationInfo[$key]['grouping_name'] = '';
    }

    $operationInfo[$key]['log_id'] = $value['log_id'];

    unset($operationInfo[$key]['fictitious_clnb']);
    unset($operationInfo[$key]['fictitious_donnb']);
    unset($operationInfo[$key]['fictitious_sharenb']);
    unset($operationInfo[$key]['fictitious_likenum']);
    unset($operationInfo[$key]['class_id']);
    unset($operationInfo[$key]['content_class']);
    unset($operationInfo[$key]['grouping_id']);
    unset($operationInfo[$key]['uniacid']);
    unset($operationInfo[$key]['content_siz']);
}

$data = array();
if(!empty($operationInfo)){
    if($start%5==0){
        array_push( $operationInfo,$this->advert('content',1));
    }
}
$data['content'] = $operationInfo;

$this->result(0, '', $data);