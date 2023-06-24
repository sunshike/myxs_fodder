<?php
$messkey= $this->get('messkey', '');
$memberInfo = $this->getUserInfo('');
$start = $this->get('start','');
$end = $this->get('end','');
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
if($system_system['video_is_show'] == 1){
    $where = ' and content_status = :content_status and content_class = :content_class and text LIKE :text and uniacid = :uniacid and is_check=1 ';
}else{
    $where = ' and content_status = :content_status and content_class = :content_class and text LIKE :text and uniacid = :uniacid and type = "img" and is_check=1 ';
}

if(!empty($messkey)){
    $param = array(':content_status' => 1,':content_class'=>1,':text'=>'%'.$messkey.'%',':uniacid'=>$this->uniacid);
    $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where 1 ".$where." order by `content_id` desc".$limit, $param);
    foreach ($contentList as $key => $value){
        $contentList[$key]['content'] = json_decode($value['content'],true);
        $contentList[$key]['content2'] = json_decode($value['content2'],true);
        $contentList[$key]['clnb'] = $contentList[$key]['clnb']+$contentList[$key]['fictitious_clnb'];
        $contentList[$key]['donnb'] = $contentList[$key]['donnb']+$contentList[$key]['fictitious_donnb'];
        $contentList[$key]['sharenb'] = $contentList[$key]['sharenb']+$contentList[$key]['fictitious_sharenb'];
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
        if(strpos(toimage($value['video_img']),'http://') !== false){
            $contentList[$key]['video_img'] = str_ireplace("http://","https://",toimage($value['video_img']));
        }else{
            $contentList[$key]['video_img'] = toimage($value['video_img']);
        }
        $operationInfo = pdo_fetch("select status from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$value['content_id'],':operation'=>'sz',':uniacid'=>$memberInfo['uniacid']));

        $contentList[$key]['clstate'] = $operationInfo['status'] == 1 ? 1 : 0; //是否收藏，1收藏，0未收藏
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

    }

    $where2 = ' and uniacid = :uniacid and group_status = :group_status and group_name LIKE :group_name ';
    $parem2 = array(':uniacid'=>$this->uniacid,':group_status'=>1,':group_name'=>'%'.$messkey.'%');
    $communityAll = pdo_fetchall("select * from " . tablename('myxs_fodder_community') . " where 1 ".$where2." order by `id` desc ".$limit,$parem2);
    if(!empty($communityAll)){
        foreach ($communityAll as $k=>$v){

            if(strpos(toimage($v['group_logo']),'http://') !== false){
                $communityAll[$k]['group_logo'] = str_ireplace("http://","https://",toimage($v['group_logo']));
            }else{
                $communityAll[$k]['group_logo'] = toimage($v['group_logo']);
            }
            $num = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_content') . " where  uniacid = :uniacid  and circle_id = :circle_id", array(':uniacid'=>$this->uniacid,':circle_id'=>$v['id']));

            $communityAll[$k]['group_num'] = intval($num);
        }
    }

    $data = array();
    $data['content'] = $contentList;
    $data['community'] = $communityAll;
    $data['advert'] = $this->advert('content',1);
    $this->result(0, '', $data);
}else{
    $data = array();
    $data['content'] = '';
    $this->result(0, '', $data);
}