<?php
$class_id = $this->get('class_id', '');
$start = $this->get('start','');
$end = $this->get('end','');
$limit=" limit ".$start.",".$end;
$userList=pdo_fetchall("select member_head_portrait,member_name,update_time,member_id,open_id,avatar from ".tablename('myxs_fodder_member')." where uniacid = :uniacid and FIND_IN_SET(:class_id,grouping_id) order by update_time desc".$limit,array(':uniacid'=>$this->uniacid,':class_id'=>$class_id));
$userCounts=pdo_fetch("select count(*) as counts from ".tablename('myxs_fodder_member')." where uniacid = :uniacid and FIND_IN_SET(:class_id,grouping_id)",array(':uniacid'=>$this->uniacid,':class_id'=>$class_id));
foreach ($userList as $key => $value){
    $userList[$key]['update_time']=date("Y-m-d H:i:s",$value['update_time']);
    $userList[$key]['counts']=$userCounts['counts'];

    if(!empty($value['avatar'])){
        if(strpos(toimage($value['avatar']),'http://') !== false){
            $userList[$key]['avatar'] = str_ireplace("http://","https://",toimage($value['avatar']));
        }else{
            $userList[$key]['avatar'] = toimage($value['avatar']);
        }
    }else{
        $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$value['open_id'].'-'.$value['member_id'].".header.png";
        $avatar = $this->resize_image($filename,$value['member_head_portrait'],50,50);
        if(strpos(toimage($avatar),'http://') !== false){
            $userList[$key]['avatar'] = str_ireplace("http://","https://",toimage($avatar));
        }else{
            $userList[$key]['avatar'] = toimage($avatar);
        }
        pdo_update('myxs_fodder_member',array('avatar'=>$avatar),array('uniacid'=>$this->uniacid,'member_id'=>$value['member_id']));

    }
    $userList[$key]['member_head_portrait'] = $userList[$key]['avatar'];

}
$this->result(0, '', $userList);