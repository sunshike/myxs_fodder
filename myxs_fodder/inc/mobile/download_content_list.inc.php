<?php
$type = $this->get('type','');
$memberInfo = $this->getUserInfo('');
$log_id = $this->get('log_id','');
$start = $this->get('start','');
$end = $this->get('end','');
$limit=" limit ".$start.",".$end;
$operationInfo = pdo_fetchall("select content_id,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and operation = :operation and status = :status and uniacid = :uniacid order by `log_id` desc".$limit, array(':member_id' => $memberInfo['member_id'],':status'=>1,':operation'=>'xz',':uniacid'=>$memberInfo['uniacid']));

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

$DownloadContentList = array();
foreach ($operationInfo as $key =>$value){
    $condition = array(':content_status' => 1,':content_id'=>$value['content_id'],':uniacid'=>$this->uniacid);
    $DownloadContentList[$key] = pdo_fetch("select * from " . tablename('myxs_fodder_content') . " where content_status = :content_status and content_id = :content_id  and uniacid = :uniacid ", $condition);

    if(empty($DownloadContentList[$key])){
        unset($DownloadContentList[$key]);
        continue;
    }
    if($system_system['video_is_show'] == 2){
        if($DownloadContentList[$key]['type'] == 'video'){
            unset($DownloadContentList[$key]);
            continue;
        }

    }

    $DownloadContentList[$key]['clnb'] = $DownloadContentList[$key]['clnb']+$DownloadContentList[$key]['fictitious_clnb'];
    $DownloadContentList[$key]['donnb'] = $DownloadContentList[$key]['donnb']+$DownloadContentList[$key]['fictitious_donnb'];
    $DownloadContentList[$key]['sharenb'] = $DownloadContentList[$key]['sharenb']+$DownloadContentList[$key]['fictitious_sharenb'];
    $DownloadContentList[$key]['likenum'] = $DownloadContentList[$key]['likenum']+$DownloadContentList[$key]['fictitious_likenum'];

    if(strpos(toimage($value['video_img']),'http://') !== false){
        $DownloadContentList[$key]['video_img'] = str_ireplace("http://","https://",toimage($value['video_img']));
    }else{
        $DownloadContentList[$key]['video_img'] = toimage($value['video_img']);
    }


    $DownloadContentList[$key]['content'] = json_decode($DownloadContentList[$key]["content"],true);
    $DownloadContentList[$key]['content2'] = json_decode($DownloadContentList[$key]["content2"],true);
    $DownloadContentList[$key]['content_siz'] = sizeof($DownloadContentList[$key]['content']);

    foreach ($DownloadContentList[$key]['content'] as $k => $v){
        if(strpos(toimage($v),'http://') !== false){
            $DownloadContentList[$key]['content'][$k] = str_ireplace("http://","https://",toimage($v));
        }else{
            $DownloadContentList[$key]['content'][$k] = toimage($v);
        }
    }
    foreach ($DownloadContentList[$key]['content2'] as $keys2 => $values2){
        if(strpos(toimage($values2),'http://') !== false){
            $DownloadContentList[$key]['content2'][$keys2] = str_ireplace("http://","https://",toimage($values2));
        }else{
            $DownloadContentList[$key]['content2'][$keys2] = toimage($values2);
        }
    }

    $operation = pdo_fetch("select * from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$value['content_id'],':operation'=>'sz',':uniacid'=>$memberInfo['uniacid']));

    $DownloadContentList[$key]['clstate'] = $operation['status'] == 1 ? 1 : 0; //是否收藏，1收藏，0未收藏

    $member = $this->getUserInfo($DownloadContentList[$key]['member_id']);
    if (empty($member)){
        $DownloadContentList[$key]['member_name'] = $system_system['member_name'];
        $DownloadContentList[$key]['member_head_portrait'] = $system_content['logo_bg'];
    }else{
        $DownloadContentList[$key]['member_name'] = $member['member_name'];
        if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
            $DownloadContentList[$key]['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
        }else{
            $DownloadContentList[$key]['member_head_portrait'] = toimage($member['member_head_portrait']);
        }
        if(empty($member['avatar'])){
            $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$member['open_id'].'-'.$member['member_id'].".header.jpg";
            $avatar = $this->resize_image($filename,toimage($member['member_head_portrait']),50,50);
            if(strpos(toimage($avatar),'http://') !== false){
                $DownloadContentList[$key]['avatar'] = str_ireplace("http://","https://",toimage($avatar));
            }else{
                $DownloadContentList[$key]['avatar'] = toimage($avatar);
            }
            pdo_update('myxs_fodder_member',array('avatar'=>$avatar),array('uniacid'=>$this->uniacid,'member_id'=>$member['member_id']));
        }else{
            if(strpos(toimage($member['avatar']),'http://') !== false){
                $DownloadContentList[$key]['avatar'] = str_ireplace("http://","https://",toimage($member['avatar']));
            }else{
                $DownloadContentList[$key]['avatar'] = toimage($member['avatar']);
            }
        }
    }

    $DownloadContentList[$key]['log_id'] = $value['log_id'];

    if($DownloadContentList[$key]['circle_id'] > 0){
        $community = pdo_fetch("select id,group_name,group_class from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$this->uniacid,':id'=>$DownloadContentList[$key]['circle_id']));
        $DownloadContentList[$key]['circle'] = $community['group_name'];
    }else{
        $DownloadContentList[$key]['circle'] = '';
    }
    if($DownloadContentList[$key]['grouping_id'] > 0){
        $grouping= pdo_fetch("select grouping_name from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid and grouping_id=:grouping_id", array(':uniacid'=>$this->uniacid,':grouping_id'=>$DownloadContentList[$key]['grouping_id']));
        $DownloadContentList[$key]['grouping_name'] = $grouping['grouping_name'];
    }else{
        $DownloadContentList[$key]['grouping_name'] = '';
    }

    unset($DownloadContentList[$key]['fictitious_clnb']);
    unset($DownloadContentList[$key]['fictitious_donnb']);
    unset($DownloadContentList[$key]['fictitious_sharenb']);
    unset($DownloadContentList[$key]['fictitious_likenum']);
    unset($DownloadContentList[$key]['class_id']);
    unset($DownloadContentList[$key]['content_class']);
    unset($DownloadContentList[$key]['grouping_id']);
    unset($DownloadContentList[$key]['uniacid']);
    unset($DownloadContentList[$key]['content_siz']);
}

$data = array();
if(!empty($DownloadContentList)){
    if($start%5==0){
        array_push( $DownloadContentList,$this->advert('content',1));
    }
}

//        $list = array();
//        if(!empty($DownloadContentList)){
//            foreach ($DownloadContentList as $k=>$v){
//
//            }
//        }

$data['content'] = array_merge($DownloadContentList);

$this->result(0, '', $data);