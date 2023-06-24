<?php
$monthFirst= $this->get('monthFirst', '');
$monthEnd= $this->get('monthEnd', '');
$memberInfo = $this->getUserInfo('');

$contentList = pdo_fetchall("select add_time,text,type,amount,get_member_id from " . tablename('myxs_fodder_member_intergral_log') . " where add_time > :monthFirst and add_time <:monthEnd and member_id=:member_id and uniacid = :uniacid  order by `add_time` desc ", array(':monthFirst' => $monthFirst,':monthEnd'=>$monthEnd,':member_id'=>$memberInfo["member_id"],':uniacid'=>$this->uniacid));

$lessenIntergral = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where add_time > :monthFirst and add_time <:monthEnd and member_id=:member_id and uniacid = :uniacid  and type=1 ", array(':monthFirst' => $monthFirst,':monthEnd'=>$monthEnd,':member_id'=>$memberInfo["member_id"],':uniacid'=>$this->uniacid));
$addIntergral = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where add_time > :monthFirst and add_time <:monthEnd and member_id=:member_id and uniacid = :uniacid  and type=2 ", array(':monthFirst' => $monthFirst,':monthEnd'=>$monthEnd,':member_id'=>$memberInfo["member_id"],':uniacid'=>$this->uniacid));

if($lessenIntergral==null){
    $lessenIntergral=0;
}
if($addIntergral==null){
    $addIntergral=0;
}
foreach ($contentList as $key => $value){
    $contentList[$key]['add_time']=date("Y-m-d H:i:s",$value['add_time']);
//            if($value['get_member_id']!=0){
    $shareMemberInfo = pdo_fetch("select member_head_portrait from ".tablename('myxs_fodder_member')."where member_id =:member_id",array(':member_id'=>$value['get_member_id']));
    $contentList[$key]['getMemberImg']=$shareMemberInfo['member_head_portrait'];
    if($value['text']=='后台变更' || $value['text']=='系统赠送'){
        $contentList[$key]['logo_bg']=true;
    }else{
        $contentList[$key]['logo_bg']=false;
    }
//            }

}
$data = array();
$data['content'] = $contentList;
$data['lessenIntergral'] = $lessenIntergral;
$data['addIntergral'] = $addIntergral;
$this->result(0, '', $data);