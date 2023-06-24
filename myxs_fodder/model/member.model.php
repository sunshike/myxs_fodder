<?php

class MemberModelClass {

    public function getUserInfo($data){
        global $_W, $_GPC;
        if (empty($data['member_id'])){
            $memberInfo = pdo_fetch( "select * from " . tablename('myxs_fodder_member') . " where open_id = :openid and uniacid = :uniacid", array(':openid' => $data['uid'], ':uniacid'=>$_W['uniacid']));
        }else{
            $memberInfo = $memberInfo = pdo_fetch("select * from " . tablename('myxs_fodder_member') . " where member_id = :member_id and uniacid = :uniacid", array(':member_id' => $data['member_id'], ':uniacid'=>$_W['uniacid']));
        }
        if(!empty($memberInfo)){
            $memberInfo['intergral'] = floatval($memberInfo['intergral']);
        }
        return $memberInfo;
    }

    public function index(){
        global $_W, $_GPC;
        $system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'system'));
        $system_content = json_decode($system['system_content'],true);

        $system_intergral = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'intergral'));
        $system_intergral_content = json_decode($system_intergral['system'],true);
        foreach ($system_content as $key =>$value){
            if(strpos(toimage($value),'http://') !== false){
                $system_content[$key]  = str_ireplace("http://","https://",toimage($value));
            }else{
                $system_content[$key] = toimage($value);
            }
        }
        if(strpos(toimage($system_intergral_content['adminErWeiImg']),'http://') !== false){
            $system_intergral_content['adminErWeiImg'] = str_ireplace("http://","https://",toimage($system_intergral_content['adminErWeiImg']));
        }else{
            $system_intergral_content['adminErWeiImg'] = toimage($system_intergral_content['adminErWeiImg']);
        }
        $system['system_content'] = $system_content;
        $system['system_basic'] = json_decode($system['system'],true);

        $data = array();
        $data['system'] = $system;
        $data['intergral']=$system_intergral_content;
        $data['member'] = $this->member(array('isReturnData'=>true));
        $s = json_decode($system['system'],true);
        $a = array();
        $a["text"] = "";
        $a["colorIndex"] = "0";
        $a["arrIndex"] = "0";
        $a["erwei"] = $system_content['qr_bg'];

        if (!$s['watermark_status'] || empty($data['member']['watermark'])){
            $data['member']['watermark'] = json_encode($a);
        }

        $data['class'] = $this->doPageListClass(true);
//        $data['class'][888]=array(
//            'class_id'=>888,
//            'class_name'=>'专属素材'
//        );
        $data['member_advert'] = array('member'=>$this->advert('member',1),'member_set'=>$this->advert('member_set',1));

        $WaterMess = pdo_fetch("select stat_bg from " . tablename('myxs_fodder_water_bg') . " where uniacid = :uniacid and uid=:uid", array(":uid"=> $data['member']['member_id'],':uniacid'=>$_W['uniacid']));
        $data['member_water'] =$WaterMess['stat_bg'];
        if(strpos(toimage($WaterMess['stat_bg']),'http://') !== false){
            $data['member_water']  = str_ireplace("http://","https://",toimage($WaterMess['stat_bg']));
        }else{
            $data['member_water'] = toimage($WaterMess['stat_bg']);
        }



        $todayStart= strtotime(date('Y-m-d 00:00:00', time())); //2019-01-17 00:00:00
        $todayEnd= strtotime(date('Y-m-d 23:59:59', time())); //2019-01-17 23:59:59

        $intergralAllToday = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and uniacid=:uniacid and type =2 and add_time>=:time1 and add_time<=:time2", array(':member_id' => $data['member']['member_id'], ':uniacid' =>$this->uniacid,':time1'=>$todayStart,':time2'=>$todayEnd));
        $data['today_intergral'] = floatval($intergralAllToday);

        $Login = pdo_fetch("select inter_id from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and operation=:operation and uniacid=:uniacid and add_time>=:time1 and add_time<=:time2", array(':member_id' => $data['member']['member_id'], ':operation' => 'login', ':uniacid' => $this->uniacid,':time1'=>$todayStart,':time2'=>$todayEnd));
        if(!empty($Login)){
            $data['is_login_send'] = true;
        }else{
            $data['is_login_send'] = false;
        }
        $data['content_advert'] = $this->advertAll('content',1);

        $data['intergral_advert'] = $this->advert('intergral',1);

        $this->result(0,'',$data);
    }
    public function member($data){
        global $_W, $_GPC;
        $member = $this->getUserInfo('');
        if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
            $member['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
        }else{
            $member['member_head_portrait'] = toimage($member['member_head_portrait']);
        }
        if(empty($member['avatar'])){
            $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$member['open_id'].'-'.$member['member_id'].".header.png";
            $avatar = $this->resize_image($filename,$member['member_head_portrait'],50,50);
            if(strpos(toimage($avatar),'http://') !== false){
                $member['avatar'] = str_ireplace("http://","https://",toimage($avatar));
            }else{
                $member['avatar'] = toimage($avatar);
            }
            pdo_update('myxs_fodder_member',array('avatar'=>$avatar),array('uniacid'=>$_W['uniacid'],'member_id'=>$member['member_id']));
        }else{
            if(strpos(toimage($member['avatar']),'http://') !== false){
                $member['avatar'] = str_ireplace("http://","https://",toimage($member['avatar']));
            }else{
                $member['avatar'] = toimage($member['avatar']);
            }
        }


        if ($data['isReturnData']){
            return $member;
        }
        $this->result(0, '', array('status'=>$member));
    }
    /**
     * @param $filename
     * @param $tmpname
     * @param $xmax
     * @param $ymax
     * @return bool|false|resource|string
     * 压缩图片
     */
    public function resize_image($filename, $tmpname, $xmax, $ymax)
    {
        global $_W,$_GPC;
        $ext = explode(".", $filename);
        $ext = $ext[count($ext)-1];

        if($ext == "jpg" || $ext == "jpeg")
            $im = imagecreatefromjpeg($tmpname);
        elseif($ext == "png")
            $im = imagecreatefrompng($tmpname);
        elseif($ext == "gif")
            $im = imagecreatefromgif($tmpname);

        $x = imagesx($im);
        $y = imagesy($im);

        if($x <= $xmax && $y <= $ymax)
            return $im;

        if($x >= $y) {
            $newx = $xmax;
            $newy = $newx * $y / $x;
        }
        else {
            $newy = $ymax;
            $newx = $x / $y * $newy;
        }

        $im2 = imagecreatetruecolor($newx, $newy);
        imagecopyresized($im2, $im, 0, 0, 0, 0, floor($newx), floor($newy), $x, $y);
        $res = imagejpeg($im2, $filename, 90); //保存到本地

        $background = strstr($filename,'upload/');

        if(empty($_W['setting']['remote']['type'])){
            return $background;
        }else{
            @require_once (IA_ROOT . '/framework/function/file.func.php');
            @$filename = $background;
            $remotestatus = file_remote_upload($background);
            if (is_error($remotestatus)) {
                file_delete($background);
                return false;
            } else {
                $url = $background;
                file_delete($background);
                return $url;
            }
        }
    }
    public function result($errno, $message, $data = '') {
        exit(json_encode(array(
            'errno' => $errno,
            'message' => $message,
            'data' => $data,
        )));
    }
}
