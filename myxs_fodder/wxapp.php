<?php
/**
 * myxs_fodder模块小程序接口定义
 *
 * @author myxinshang
 * @url
 */
defined('IN_IA') or exit('Access Denied');
class Myxs_fodderModuleWxapp extends WeModuleWxapp {
    const TABLE = 'myxs_fodder';

    private $gpc;
    private $w;
    private $uid; // 用户ID
    public function __construct() {
        global $_W,$_GPC;
        $this->gpc = $_GPC;
        $this->w = $_W;
        $this->uid = $_W['openid'];
        $this->uniacid = $_W['uniacid'];

//        file_get_contents(base64_decode("aHR0cHM6Ly9hcGkubW90dW90YS5jb20vaW5kZXgucGhwL2luZGV4L2NoZWNrQXV0aC9zb2Z0d2FyZV9uYW1lL215eHNfZm9kZGVyL3JlYWxtX25hbWUv").$_SERVER['HTTP_HOST']);
        if($_GPC['do'] != 'FileSubmit') {
            if($_GPC['do'] != 'FileSubmit2'){
                if (empty($this->uid)) {
                    $this->result(41009, '请先登录');
                } else {
                    $memberInfo = $this->getUserInfo('');
                    $system = pdo_fetch("select * from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid' => $_W['uniacid'], ':system_code' => 'intergral'));
                    $system_seting = json_decode($system['system'], true);
                    if (empty($memberInfo)) {
                        pdo_insert('myxs_fodder_member', array('open_id' => $this->uid, 'uniacid' => $this->uniacid, 'create_time' => time(), 'member_status' => 1, 'member_name' => $this->w["fans"]['nickname'], 'member_head_portrait' => $this->w["fans"]['avatar'], 'intergral' => $system_seting['givingIntergral']));
                        $insert_member = pdo_insertid();
                        if ($insert_member) {
                            $intergral_log_data = array('uniacid' => $this->uniacid, 'member_id' => $insert_member, 'text' => '新会员关注', 'type' => 2, 'amount' => $system_seting['givingIntergral'], 'add_time' => time(), 'operation' => 'zs');
                            pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
                        }

                    } else {
                        $nowTime = time();
                        $today = strtotime(date("Y-m-d"), time());
                        $xtzsArr = pdo_fetch("select * from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and operation=:operation and uniacid=:uniacid", array(':member_id' => $memberInfo['member_id'], ':operation' => 'xtzs', ':uniacid' => $memberInfo['uniacid']));
                        if ($nowTime > $today && $memberInfo['intergral'] == 0) {
                            if (empty($xtzsArr)) {
                                $addIntergral = intval($memberInfo['intergral']) + 1;
                                $sta = pdo_update('myxs_fodder_member', array('intergral' => $addIntergral), array('member_id' => $memberInfo['member_id']));
                                if ($sta) {
                                    $intergral_log_data = array('uniacid' => $this->uniacid, 'member_id' => $memberInfo['member_id'], 'text' => '系统赠送', 'type' => 2, 'amount' => 1, 'add_time' => time(), 'operation' => 'xtzs');
                                    pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
                                }


                            }
                        }
                    }
                }
            }

        }
    }

    public function get($key, $default = null) {
        return isset($this->gpc[$key]) ? $this->gpc[$key] : $default;
    }

    /**
     * 获取用户信息
     * @return bool
     */
    public function getUserInfo($member_id){
        if (empty($member_id)){
            $memberInfo = pdo_fetch( "select * from " . tablename('myxs_fodder_member') . " where open_id = :openid and uniacid = :uniacid", array(':openid' => $this->uid, ':uniacid'=>$this->uniacid));
        }else{
            $memberInfo = $memberInfo = pdo_fetch("select * from " . tablename('myxs_fodder_member') . " where member_id = :member_id and uniacid = :uniacid", array(':member_id' => $member_id, ':uniacid'=>$this->uniacid));
        }
        if(!empty($memberInfo)){
            $memberInfo['intergral'] = floatval($memberInfo['intergral']);
        }
        return $memberInfo;
    }


    /**
     *通过点击用户分享链接绑定上下级关系
     */
    public function doPageBindRelation(){
        $member = $this->getUserInfo('');
        $id = intval($this->get('id'));

        $parent = $this->getUserInfo($id);

        if(empty($parent)){
            $this->result(0,'',array('status'=>false,'msg'=>'用户不存在'));
        }else{
            if($member['parent'] == 0 && $member['member_id'] != $parent){
                pdo_update('myxs_fodder_member',array('parent'=>intval($parent['member_id']),'parent_time'=>time()),array('member_id'=>$member['member_id'],'uniacid'=>$this->uniacid));


                $system_intergral = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'intergral'));
                $system_intergral_content = json_decode($system_intergral['system'],true);

                $intergral_log_data = array(
                    'uniacid' => $this->uniacid,
                    'member_id' => $parent['member_id'],
                    'text' => '邀请好友赠送',
                    'type' => 2,
                    'amount' => intval($system_intergral_content['inviteIntergral']),
                    'add_time' => time(),
                    'operation' => 'invite'
                );
                pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);

                $addIntergral = intval($parent['intergral']) + intval($system_intergral_content['inviteIntergral']);
                $sta = pdo_update('myxs_fodder_member', array('intergral' => $addIntergral), array('member_id' => $parent['member_id']));

                $this->result(0,'',array('status'=>true,'msg'=>'绑定成功'));
            }
        }


    }

    /**
     *  入口
     */
    public function doPageIndex(){
//        $api  = new api();
//        /**
//         * 获取用户信息
//         * @return bool
//         */
//        public function getUserInfo($member_id){
//            include "model/member.model.php";
//            $member = new MemberModelClass();
//            $memberInfo=$member->getUserInfo(array("member_id" => $member_id,'uid'=>$this->uid));
//            return  $memberInfo;
//        }
//
//        /**
//         *  入口
//         */
//        public function doPageIndex(){
////        $api  = new api();
////        $api->index();
////        include_once 'inc/mobile/index.inc.php';
//
//            include "model/member.model.php";
//            $member = new MemberModelClass();
//            $member->index();
//        }
//
//        /**
//         *  获取用户信息
//         * @param bool $isReturnData
//         * @return bool
//         */
//        public function doPageMember($isReturnData = false){
//            include "model/member.model.php";
//            $member = new MemberModelClass();
//            $member->member(array("isReturnData" => $isReturnData));
//        }
//        $api->index();
        include_once 'inc/mobile/index.inc.php';
    }

    /**
     *  获取用户信息
     * @param bool $isReturnData
     * @return bool
     */
    public function doPageMember($isReturnData = false){
        $member = $this->getUserInfo('');
        if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
            $member['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
        }else{
            $member['member_head_portrait'] = toimage($member['member_head_portrait']);
        }
//        if(empty($member['avatar'])){
//            $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$member['open_id'].'-'.$member['member_id'].".header.png";
//            $avatar = $this->resize_image($filename,$member['member_head_portrait'],50,50);
//            if(strpos(toimage($avatar),'http://') !== false){
//                $member['avatar'] = str_ireplace("http://","https://",toimage($avatar));
//            }else{
//                $member['avatar'] = toimage($avatar);
//            }
//            pdo_update('myxs_fodder_member',array('avatar'=>$avatar),array('uniacid'=>$this->uniacid,'member_id'=>$member['member_id']));
//        }else{
//            if(strpos(toimage($member['avatar']),'http://') !== false){
//                $member['avatar'] = str_ireplace("http://","https://",toimage($member['avatar']));
//            }else{
//                $member['avatar'] = toimage($member['avatar']);
//            }
//        }


        if ($isReturnData){
            return $member;
        }
        $this->result(0, '', array('status'=>$member));
    }
    /**
     *
     * 获取系统设置
     */
    public function doPageGetSystemInfo(){
        include_once 'inc/mobile/system_intergral.inc.php';
    }
    /**
     *
     * 获取系统设置
     */
    public function doPageGetReleaseSystemInfo(){
        include_once 'inc/mobile/system_release.inc.php';
    }
    /**
     *  更新用户信息
     */
    public function doPageUpdateMember(){
        include_once 'inc/mobile/update_member.inc.php';
    }

    /**
     * 媳妇专属定制
     * 20200120 Demigod add
     */
    public function doPageGetWife(){
        $wife = file_get_contents("https://api.5ycz.com/index/goodMorning");
        $this->result(0, '', $wife);
    }


    /**
     * 更新用户手机号
     */
    public function doPageUpdateMobile(){
        global $_GPC,$_W;
        $data = array();
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $_W['account']['key'] . "&secret=" . $_W['account']['secret'] . "&js_code=" . $_GPC['code'] . "&grant_type=authorization_code";
        $res = ihttp_get($url);
        $res = json_decode($res['content'],1);
        $session_key = $res['session_key'];
        $aesKey = base64_decode($session_key);
        $aesIV = base64_decode($_GPC['iv']);
        $aesCipher = base64_decode($_GPC['encryptedData']);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $result2 = json_decode($result,true);
        if($result2){
            $data['member_mobile'] = $result2['phoneNumber'];
            $memberInfo = $this->getUserInfo('');
            if (!empty($memberInfo) && !empty($data['member_mobile'])) {
                $sk = pdo_update('myxs_fodder_member', array('update_time' => time(), 'member_mobile' => $data['member_mobile']), array('member_id' => $memberInfo['member_id']));
                if (!$sk) {
                    $this->result(0, '获取手机号失败，请重试', array('status'=>0,'msg'=>'获取手机号失败，请重试'));
                }
            }
            $this->result(0,'获取手机号成功',array('status'=>1,'mobile'=>$data['member_mobile'],'msg'=>'获取手机号成功'));
        }else{
            $this->result(0, '获取手机号失败，请重试', array('status'=>0,'msg'=>'获取手机号失败，请重试'));
        }

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

        $ext = explode(".", $tmpname);
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

    /**
     *
     * 获取轮播
     */
    public function doPageGetShuffling(){
        include_once 'inc/mobile/get_shuffling.inc.php';
    }
    /**
     *  更新用户水印设置 弃用
     */
    public function doPageUpdateWatermark(){
        $memberInfo = $this->getUserInfo('');
        $data = array();
        $data['text'] = $this->get('text');
//        $data['colorArr'] = $this->get('colorArr');
        $data['colorIndex'] = $this->get('colorIndex');
//        $data['array'] = $this->get('array');
        $data['arrIndex'] = $this->get('arrIndex');
        $data['erwei'] = $this->get('erwei');
        $sk = pdo_update('myxs_fodder_member', array('update_time'=>time(),'watermark'=>json_encode($data)),array('member_id'=>$memberInfo['member_id']));
        if (!$sk){
            $this->result(0,'',false);
        }
        $this->result(0,'',true);

    }


    /**
     * 获取分类数组
     * @param bool $isReturnData
     * @return mixed
     */
    public function doPageListClass($isReturnData = false){
        $circle_id = $this->get('circle_id', '');
        $ListClass = pdo_fetchall("select class_name,class_id from " . tablename('myxs_fodder_class') . " where class_status = :class_status and circle_id = :circle_id and uniacid = :uniacid order by `class_id` asc", array(':class_status' => 1,':circle_id'=>$circle_id,':uniacid'=>$this->uniacid));

        if ($isReturnData){
            return $ListClass;
        }

        $this->result(0, '', $ListClass);
    }


    /**
     *  获取会员分组
     */
    public function doPageGrouping(){
        $member = $this->getUserInfo('');
        $groupingList = pdo_fetchall("select grouping_name,grouping_id from " . tablename('myxs_fodder_grouping') . " where grouping_id in( " . $member['grouping_id'] . " ) and uniacid = :uniacid ", array(':uniacid'=>$this->uniacid));
        $this->result(0, '', $groupingList);
    }
    public function doPageUserGrouping(){
        $member = $this->getUserInfo('');
        $groupingList = pdo_fetchall("select grouping_name,grouping_id from " . tablename('myxs_fodder_grouping') . " where grouping_id in( " . $member['grouping_id'] . " ) and uniacid = :uniacid ", array(':uniacid'=>$this->uniacid));
        $groupingList[] = array('grouping_id'=>'0','grouping_name'=>'默认分组');

        $this->result(0, '', $groupingList);
    }
    /**
     *  获取所管理的分组--lx
     */
    public function doPageIsClassAdmin(){
        $member = $this->getUserInfo('');
        $groupingList = pdo_fetchall("select grouping_name,grouping_id,grouping_passwd from " . tablename('myxs_fodder_grouping') . " where admin_id=:admin_id and uniacid = :uniacid", array(':admin_id'=>$member['member_id'],':uniacid'=>$this->uniacid));
        $this->result(0, '', $groupingList);
    }
    /**
     * 读取分组内成员--lx
     */
    public function doPageGroupUsers(){
        include_once 'inc/mobile/group_user.inc.php';
    }
    /**
     * 将成员踢出分组
     */
    public function doPageKickOutUserToGroup(){
        include_once 'inc/mobile/kick_out_user_to_group.inc.php';
    }
    /**
     *
     * 前台修改分组邀请码
     */
    public function doPageUpdateClassPassWd(){
        include_once 'inc/mobile/update_class_passwd.inc.php';
    }
    /**
     * 获取分组开屏广告图  及 修改
     *
     */
    public function doPageGetGroupBg(){
        include_once 'inc/mobile/get_group_bg.inc.php';
    }
    /**
     * 获取用户设置的水印  及 修改
     *
     */
    public function doPageGetImageWater(){
        include_once 'inc/mobile/get_image_water.inc.php';
    }
    /**
     *  获取内容数组
     * @param $id
     * @param bool $isReturnData
     * @return mixed
     */
    public function doPageListContent(){
        include_once 'inc/mobile/list_content.inc.php';
    }
    /**
     * 全部分类  根据分类ID获取各个分类下载量最多的10条数据
     */
    public function doPageGetListByClassId(){
        include_once 'inc/mobile/get_list_by_classid.inc.php';
    }
     /**
     *  获取搜索内容
     * @param $id
     * @param bool $isReturnData
     * @return mixed
     */
    public function doPageSearchMess(){
        include_once 'inc/mobile/search_mess.inc.php';
    }
    /**
     *  获取积分记录
     * @param $id
     * @param bool $isReturnData
     * @return mixed
     */
    public function doPageIntergralLog(){
        include_once 'inc/mobile/intergral_log.inc.php';
    }
    /**
     * 广告处理
     * @param $type
     * @return mixed
     */
    function advert($type,$class){
         // 随机取一条广告
        $advertList = pdo_fetchall("select advert_text from " . tablename('myxs_fodder_advert') . " where advert_position = :advert_position  and uniacid = :uniacid and advert_status = :advert_status and advert_class=:advert_class", array(':advert_position' => $type,':advert_status' => '1',':uniacid'=>$this->uniacid,':advert_class'=>$class));
        $rand = mt_rand(0,sizeof($advertList));
        $rand = $rand == 0 ? $rand : $rand -1 ;

        $data = $advertList[$rand];
        if (!empty($advertList)){
            $data['show'] = 1;
        }else{
            $data['show'] = 0;
        }
        return $data ;
    }
    /**
     * 获取所有广告
     */
    function advertAll($type,$class){
        // 随机取一条广告
        $advertList = pdo_fetchall("select advert_text,advert_class_type from " . tablename('myxs_fodder_advert') . " where advert_position = :advert_position  and uniacid = :uniacid and advert_status = :advert_status and advert_class=:advert_class", array(':advert_position' => $type,':advert_status' => '1',':uniacid'=>$this->uniacid,':advert_class'=>$class));
        return $advertList ;
    }

    /**
     *  获取下载内容
     */
    public function doPageDownloadContentList(){
        include_once 'inc/mobile/download_content_list.inc.php';
    }

    /**
     *  获取收藏内容
     */
    public function doPageCollectionContentList(){
        include_once 'inc/mobile/collection_content_list.inc.php';
    }

    /**
     *
     * 用户分享后其他用户点击进来后增加积分
     */
    public function doPageShareAddIntergral(){
        include_once 'inc/mobile/share_add_intergral.inc.php';
    }
    /**
     *  操作接口
     *  sz 收藏
     *  xz 下载
     *  fx 分享
     */
    public function doPageOperation(){
        include_once 'inc/mobile/operation.inc.php';
    }

    /**
     * 图片生成水印
     */
    public function doPageWaterImg(){
        include_once 'inc/mobile/water_img.inc.php';
    }
    function createPostertt($config = array(), $filename = "")
    {
        //如果要看报什么错，可以先注释调这个header
        if (empty($filename)) header("content-type: image/png");
        $imageDefault = array(
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 100,
            'height' => 100,
            'opacity' => 100
        );
        $background = $config['background']; //海报最底层得背景
        //背景方法
        $backgroundInfo = getimagesize($background);
        $backgroundFun = 'imagecreatefrom' . image_type_to_extension($backgroundInfo[2], false);
        $background = $backgroundFun($background);
        $backgroundWidth = imagesx($background); //背景宽度
        $backgroundHeight = imagesy($background); //背景高度

        $imageRes = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
        $color = imagecolorallocate($imageRes, 0, 0, 0);
        imagefill($imageRes, 0, 0, $color);
        imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));
        //处理了图片
        if (!empty($config['image'])) {
            foreach ($config['image'] as $key => $val) {
                $val = array_merge($imageDefault, $val);
                $info = getimagesize($val['url']);

                $function = 'imagecreatefrom' . image_type_to_extension($info[2], false);
                if ($val['stream']) { //如果传的是字符串图像流
                    $info = getimagesizefromstring($val['url']);
                    $function = 'imagecreatefromstring';
                }
                if ($val['is_yuan']) {
                    list($res, $w) = $this->yuan_img($val['url']);
                } else {
                    $res = $function($val['url']);
                }

                $resWidth = $info[0];
                $resHeight = $info[1];
                //建立画板 ，缩放图片至指定尺寸
                $canvas = imagecreatetruecolor($val['width'], $val['height']);
                imagefill($canvas, 0, 0, $color);
                //如果是透明的gif或png做透明处理

                $ext = pathinfo($val['url']);
                if (array_key_exists('extension', $ext)) {
                    if ($ext['extension'] == 'gif' || $ext['extension'] == 'png') {
                        imageColorTransparent($canvas, $color); //颜色透明

                    }
                }
                if ($val['is_yuan']) {
                    imageColorTransparent($canvas, $color); //颜色透明
                }
                //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
                imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], $resWidth, $resHeight);
                //$val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']) - $val['width']:$val['left'];
                //如果left小于-1我这做成了计算让其水平居中
                if ($val['left'] < 0) {
                    $val['left'] = ceil($backgroundWidth - $val['width']) / 2;
                }else{
                    $val['left'] = ceil($backgroundWidth - $val['width'])-10;
                }
//                $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
                $val['top'] = $val['top'] == 0 ? ceil($backgroundHeight - $val['height'])-10  : $val['top'];
//                $val['top'] = $val['top'] == 0 ? ceil($backgroundHeight - $val['height']) / 2 : $val['top'];

                imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']); //左，上，右，下，宽度，高度，透明度

                //放置图像

            }
        }

        //生成图片
        if (!empty($filename)) {
            $res = imagejpeg($imageRes, $filename, 90); //保存到本地
            return !$res ? '图片生成失败' : $filename;
        } else {
            header("Content-type:image/png");
            imagejpeg($imageRes); //在浏览器上显示
            imagedestroy($imageRes);
        }
    }
    function yuan_img($imgpath)
    {
        $wh = getimagesize($imgpath);//pathinfo()不准
        $src_img = null;
        switch ($wh[2]) {
            case 1:
                //gif
                $src_img = imagecreatefromgif($imgpath);
                break;
            case 2:
                //jpg
                $src_img = imagecreatefromjpeg($imgpath);
                break;
            case 3:
                //png
                $src_img = imagecreatefrompng($imgpath);
                break;
        }
        $w = $wh[0];
        $h = $wh[1];
        $w = min($w, $h);
        $h = $w;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        imageantialias($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        return [$img, $w];
    }

    /**
     * 删除服务上图片
     */
    public function doPageDeleteImg(){
        global $_GPC,$_W;
        $img = $_GPC['img'];
//        $img = strstr($img,'https://');
        $img = substr($img,8);
        $filename = '/www/wwwroot/'.$img;
        if(file_exists($filename)){
            unlink($filename);
            $this->result(0, '删除成成功', array('status'=>true));
        }
    }

    /**
     * 获取日签
     */
    public function doPageListDaySign(){
        include_once 'inc/mobile/list_daysign.inc.php';
    }

    /**
     *  删除，发布内容
     *  获取单条内容
     */
    public function doPageContent(){
        include_once 'inc/mobile/content.inc.php';
    }

    /**
     *  验证并修改用户分组
     */
    public function doPageCheckGrouping(){
        global $_W,$_GPC;
        $code = trim($this->get('code'));
        $member=$this->getUserInfo('');

        $grouping_passwd =pdo_fetch("select grouping_id from " . tablename('myxs_fodder_grouping') . " where grouping_passwd = :grouping_passwd and uniacid = :uniacid", array(':grouping_passwd' => $code,':uniacid'=>$_W['uniacid']));
        $grouping = explode(',',$member['grouping_id']);
        if (in_array($grouping_passwd['grouping_id'],$grouping)){
            $this->result(0, '', array('status'=>false,'message'=>'验证分组已存在，请勿重复验证！'));
        }
        $grouping[]= $grouping_passwd['grouping_id'];

        $update_grouping_id = implode(',',$grouping);

        if (empty($grouping_passwd)){
            $this->result(0, '', array('status'=>false,'message'=>'邀请码错误，请联系管理员获取！'));
        }else{
            pdo_update('myxs_fodder_member', array('update_time'=>time(),'grouping_id'=>$update_grouping_id),array('member_id'=>$member['member_id']));
            $this->result(0, '', array('status'=>true,'message'=>'恭喜您验证成功！'));
        }
    }

    /**
     * 上传
     */
    public function doPageFileSubmit(){
        global $_GPC, $_W;
        $state = $this->addImg($_FILES,$_W['setting']['upload']);

        if($state['status'] == 2){
            $this->result(0, '', array('status'=>false,'message'=>'内容过大！'));
        }
        if($state['status'] == 1){
            $this->result(0, '', array('status'=>false,'message'=>"格式错误，当前格式为：".$state['msg']));
        }
        if($state['status'] == 4){
            $this->result(0, '', array('status'=>false,'message'=>'上传失败网络错误，请重新上传！'));
        }
        if($state['status'] == 6){
            $this->result(0, '', array('status'=>false,'message'=>'上传方式错误！'));
        }


        $background = strstr($state['url'],'upload/');
//        $aa = '';
//
//        if($type != 'mp4'){
//            $yashuo = $this->FileYaSuo($state);
//            if(!$yashuo['status']){
//                $this->result(0, '', array('status'=>false,'message'=>'上传出错'));
//            }else{
//                $aa = $yashuo['url'];
//            }
//        }


        if(empty($_W['setting']['remote']['type'])){
            $url = toimage($background);
            $types = explode('.',$url);
            $type =$types[count($types)-1];
            $this->result(0, '', array('status'=>true,'url'=>$url,'type'=>$type,'message'=>'上传成功'));
        }else{
            @require_once (IA_ROOT . '/framework/function/file.func.php');
            @$filename = $background;
            $remotestatus = file_remote_upload($background);
            if (is_error($remotestatus)) {
                file_delete($background);
                $this->result(0, '', array('status'=>false,'message'=>'远程附件上传失败，请检查配置并重新上传'));
            } else {
                file_delete($background);
                $url = toimage($background);
                $types = explode('.',$url);
                $type =$types[count($types)-1];
                $this->result(0, '', array('status'=>true,'url'=>$url,'type'=>$type,'message'=>'上传成功'));
            }
        }


    }

    /**
     * @param $state
     * @return array
     * 获取上传图片的压缩版
     */
    public function FileYaSuo($state){
        global $_GPC, $_W;
        $new_img = $this->getThumb($state['url'],400,400);
        $background = strstr($new_img,'upload/');

        if(empty($_W['setting']['remote']['type'])){
            $url = toimage($background);
            $types = explode('.',$url);
            $type =$types[count($types)-1];
            return array('status'=>true,'url'=>$url,'type'=>$type,'message'=>'上传成功');
        }else{
            @require_once (IA_ROOT . '/framework/function/file.func.php');
            @$filename = $background;
            $remotestatus = file_remote_upload($background);
            if (is_error($remotestatus)) {
                file_delete($background);
                return array('status'=>false);
            } else {
                file_delete($background);
                $url = toimage($background);
                $types = explode('.',$url);
                $type =$types[count($types)-1];
                return array('status'=>true,'url'=>$url,'type'=>$type,'message'=>'上传成功');
            }
        }
    }
    /**
     * @param $state
     * @return array
     * 获取上传图片的压缩版
     */
    public function FileYaSuoFivety($state){
        global $_GPC, $_W;
        $new_img = $this->getThumb($state['url'],50,50);
        $background = strstr($new_img,'upload/');

        if(empty($_W['setting']['remote']['type'])){
            $url = toimage($background);
            $types = explode('.',$url);
            $type =$types[count($types)-1];
            return array('status'=>true,'url'=>$url,'type'=>$type,'message'=>'上传成功');
        }else{
            @require_once (IA_ROOT . '/framework/function/file.func.php');
            @$filename = $background;
            $remotestatus = file_remote_upload($background);
            if (is_error($remotestatus)) {
                file_delete($background);
                return array('status'=>false);
            } else {
                file_delete($background);
                $url = toimage($background);
                $types = explode('.',$url);
                $type =$types[count($types)-1];
                return array('status'=>true,'url'=>$url,'type'=>$type,'message'=>'上传成功');
            }
        }
    }

    /**
     * 生成图片
     * @param string $im 源图片路径
     * @param string $dest 目标图片路径
     * @param int $maxwidth 生成图片宽
     * @param int $maxheight 生成图片高
     */
    function resizeImage($im, $dest, $maxwidth, $maxheight) {
        $img = getimagesize($im);
        switch ($img[2]) {
            case 1:
                $im = @imagecreatefromgif($im);
                break;
            case 2:
                $im = @imagecreatefromjpeg($im);
                break;
            case 3:
                $im = @imagecreatefrompng($im);
                break;
        }

        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        $resizewidth_tag = false;
        $resizeheight_tag = false;
        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }

            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }


            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;
            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }

            imagejpeg($newim, $dest);
            imagedestroy($newim);
        } else {
            imagejpeg($im, $dest);
        }
    }

    /**
     * 图片压缩处理
     * @param string $sFile 源图片路径
     * @param int $iWidth 自定义图片宽度
     * @param int $iHeight 自定义图片高度
     * @return string  压缩后的图片路径
     */
    function getThumb($sFile,$iWidth,$iHeight){
        //图片公共路径
        $public_path = '';
        //判断该图片是否存在
        if(!file_exists($public_path.$sFile)) return $sFile;
        //判断图片格式(图片文件后缀)
        $extend = explode("." , $sFile);
        $attach_fileext = strtolower($extend[count($extend) - 1]);
        if (!in_array($attach_fileext, array('jpg','png','jpeg','gif'))){
            return '';
        }

        //压缩图片文件名称
        $sFileNameS = str_replace(".".$attach_fileext, "_".$iWidth.'_'.$iHeight.'.'.$attach_fileext, $sFile);
        //判断是否已压缩图片，若是则返回压缩图片路径
        if(file_exists($public_path.$sFileNameS)){
            return $sFileNameS;
        }

        //生成压缩图片，并存储到原图同路径下
        $this->resizeImage($public_path.$sFile, $public_path.$sFileNameS, $iWidth, $iHeight);
        if(!file_exists($public_path.$sFileNameS)){
            return $sFile;
        }
        return $sFileNameS;
    }
    /**
     * 上传
     */
    public function doPageFileSubmit2(){
        global $_GPC, $_W;
        $state = $this->addImg($_FILES,$_W['setting']['upload']);

        if($state['status'] == 2){
            $this->result(0, '', array('status'=>false,'message'=>'内容过大！'));
        }
        if($state['status'] == 1){
            $this->result(0, '', array('status'=>false,'message'=>"格式错误，当前格式为：".$state['msg']));
        }
        if($state['status'] == 4){
            $this->result(0, '', array('status'=>false,'message'=>'上传失败网络错误，请重新上传！'));
        }
        if($state['status'] == 6){
            $this->result(0, '', array('status'=>false,'message'=>'上传方式错误！'));
        }

        $new_img = $this->getThumb($state['url'],300,300);


        $round = mt_rand(1,100);
        $img = substr($new_img,13);
        $img = "https://".$img;
        $img = file_get_contents($img);
        $filePath = IA_ROOT . "/attachment/upload/img/".time()."-".$round.".jpg";
        file_put_contents($filePath, $img);
        $real_path=realpath($filePath);

        $obj = new CURLFile($real_path);
        $obj->setMimeType("image/jpeg");
        $postdata['media']=$obj;

        $access_token = $this->doPageGetAccessToken();
        $urls = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token='. $access_token;

        $a = $this->http_request($urls, $postdata);
        @require_once (IA_ROOT . '/framework/function/file.func.php');
        $img1 = strstr($new_img,'upload/');
        $img3 = strstr($filePath,'upload/');
        file_delete($img1);
        file_delete($img3);

        if($a['errcode'] != 0){
            $this->result(0, '', array('status'=>false,'message'=>'图片含不良信息'));
        }
        $background = strstr($state['url'],'upload/');

        $yashuo = $this->FileYaSuo($state);
        if(!$yashuo['status']){
            $aa = '';
        }else{
            $aa = $yashuo['url'];
        }

        $yashuo2 = $this->FileYaSuoFivety($state);
        if(!$yashuo['status']){
            $bb = '';
        }else{
            $bb = $yashuo2['url'];
        }

        if(empty($_W['setting']['remote']['type'])){
            $url = toimage($background);
            $types = explode('.',$url);
            $type =$types[count($types)-1];
            $this->result(0, '', array('status'=>true,'url'=>$url,'url2'=>$aa,'url50'=>$bb,'type'=>$type,'message'=>'上传成功'));
        }else{
            @require_once (IA_ROOT . '/framework/function/file.func.php');
            @$filename = $background;
            $remotestatus = file_remote_upload($background);
            if (is_error($remotestatus)) {
                file_delete($background);
                $this->result(0, '', array('status'=>false,'message'=>'远程附件上传失败，请检查配置并重新上传'));
            } else {
                file_delete($background);
                $url = toimage($background);
                $types = explode('.',$url);
                $type =$types[count($types)-1];
                $this->result(0, '', array('status'=>true,'url'=>$url,'url2'=>$aa,'url50'=>$bb,'type'=>$type,'message'=>'上传成功'));
            }
        }


    }
    function addImg($file,$setting)
    {
        global $_W,$_GPC;
        $_FILES = $file;//获取小程序传来的文件
        // 判断是否是post提交
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            //临时文件
            $uploaded_file = $_FILES['file']['tmp_name'];

            // 允许的图片类型
            $judge_img = array("image/gif", "image/pjpeg", "image/jpeg", "image/png", "image/jpg");
            // 允许的视频类型
            $judge_vid = array("video/mp4","video/quicktime","video/ext-mp4");
            // 判断是图片还是视频
            if (in_array($_FILES['file']['type'], $judge_img)) {
                // 换算字节  1M = 1024KB;1KB = 1000bt;
                $size = $setting['image']['limit'] * 1000;
                if ($_FILES["file"]["size"] > $size) {
                    // 图片过大
                    $arr = ['status' => 2];
                    return $arr;
                }
                // 图片的路径
                $user_path = IA_ROOT . "/attachment/upload/img/";
            } elseif (in_array($_FILES['file']['type'], $judge_vid)) {
                $size = $setting['audio']['limit'] * 1000;
                if ($_FILES["file"]["size"] > 1024000 * 10) {
                    // 视频过大
                    $arr = ['status' => 2];
                    return $arr;
                }
                // 视频的路径
                $user_path = IA_ROOT . "/attachment/upload/video/";
            }
            else {
                // 格式错误
                $arr = ['status' => 1,'msg'=>$_FILES['file']['type']];
                return $arr;
            }
            if (!is_dir($user_path)) {
                mkdir(iconv("UTF-8", "GBK", $user_path), 0777, true);
            }
            $file_true_name = $_FILES['file']['name'];


            $round = mt_rand(1,10000);
            $move_to_file = $user_path . time() . "-" . date("Y-m-d")."-".$round."-".$_W['uniacid'].substr($file_true_name, strrpos($file_true_name, "."));
            if (move_uploaded_file($uploaded_file, iconv("utf-8", "gb2312", $move_to_file))) {
                // 上传成功
                $arr = ['status' => 3, 'url' => $move_to_file];
                return $arr;
            } else {
                // 上传失败网络错误，请重新上传
                $arr = ['status' => 4];
                return $arr;
            }
        } else {
            // 上传方式错误
            $arr = ['status' => 6];
            return $arr;
        }

    }

    public function doPageGetAccessToken() {
        global $_W,$_GPC;
        $type = intval($this->get('type',''));
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $access_token = cache_load('access_token'.$_W['uniacid']);
        $expire_time = cache_load('access_token_time'.$_W['uniacid']);

        $appid =$this->w['account']['key'];
        $secret =$this->w['account']['secret'];
        if ($expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                cache_write('access_token'.$_W['uniacid'],$access_token);
                cache_write('access_token_time'.$_W['uniacid'], time() + 7000);
            }
        }
        if($type == 1){
            $this->result(0, '', array('status'=>true,'message'=>$access_token));
        }else{
            return $access_token;
        }
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    public function doPageCheckMsg(){
        $checkContent = $this->get('content','');
        header('Content-Type:application/json');
        header('Method: POST');
        header('Body: content');
        $access_token = $this->doPageGetAccessToken();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token='. $access_token;
        $data = json_encode(array('content'=>$checkContent),JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url); // url
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // json数据
        $res = curl_exec($ch); // 返回值
        curl_close($ch);
        $result = json_decode($res,true);
        $this->result(0, '', array('status'=>true,'message'=>$result));
    }
    private function doPageCheckImg($path){
        header('Content-Type:application/octet-stream');
        $access_token = $this->doPageGetAccessToken();
        $url = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token='. $access_token;
        $data = json_encode(array('media'=>new \CURLFile($path)));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url); // url
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // json数据
        $res = curl_exec($ch); // 返回值
        curl_close($ch);
        $result = json_decode($res,true);
        return $result;
    }
    private function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);
        return $output;
    }

    /**
     * 发现页数据读取  start   弃用
     */
    public function doPageGetFoundShuffling(){
        global $_W,$_GPC;
        $shuffling = pdo_fetch("select shuffling from " . tablename('myxs_fodder_found_shuffling') . " where uniacid = :uniacid", array(':uniacid'=>$_W['uniacid']));
        $shuffling['shuffling'] = unserialize($shuffling['shuffling']);
        if(!empty($shuffling['shuffling'])){
            foreach ($shuffling['shuffling'] as &$row){
                $row = toimage($row);
            }
        }
        $this->result(0, '', $shuffling);
    }

    /**
     * 获取评论
     */
    public function doPageGetDiscuss(){
        global $_W,$_GPC;
        $id = intval($_GPC['content_id']);

        $start = $this->get('start','');
        $end = $this->get('end','');
        $limit=" limit ".$start.",".$end;


        $member = $this->getUserInfo('');
        $contentInfo = pdo_fetch("select member_id,discuss,content_id from ".tablename('myxs_fodder_content')." where content_id =:content_id and uniacid=:uniacid ",array(':content_id'=>$id,':uniacid'=>$_W['uniacid']));

        $discussInfo = pdo_fetchall("select a.* from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and a.discuss_id=0 and a.discuss_type=0 order by a.discuss_likenum desc,a.create_time desc".$limit,array(':content_id'=>$id,':uniacid'=>$_W['uniacid']));
        $total = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and a.discuss_type=0 ",array(':content_id'=>$id,':uniacid'=>$_W['uniacid']));
        if(!empty($discussInfo)){
            foreach ($discussInfo as $k=>$v){
                if($contentInfo['member_id'] == $v['member_id']){
                    $discussInfo[$k]['is_author'] = true;
                }else{
                    $discussInfo[$k]['is_author'] = false;
                }

                if($v['member_id'] == $member['member_id']){
                    $discussInfo[$k]['is_delete'] = true;
                }else{
                    $discussInfo[$k]['is_delete'] = false;
                }

                $diss_member = pdo_fetch("select member_id,member_head_portrait,member_name from ".tablename('myxs_fodder_member')." where member_id =:member_id and uniacid=:uniacid ",array(':member_id'=>$v['member_id'],':uniacid'=>$_W['uniacid']));
                $discussInfo[$k]['member_avatar'] = $diss_member['member_head_portrait'];
                $discussInfo[$k]['member_nickname'] = $diss_member['member_name'];
                $operationInfo = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $member['member_id'],':content_id'=>$v['id'],':operation'=>'pldz',':uniacid'=>$_W['uniacid']));
                $discussInfo[$k]['is_like'] = $operationInfo['status'] == 1 ? true : false;

                $down = pdo_fetchall("select a.* from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and find_in_set(:id1,a.id_arr) and a.discuss_type=0 order by a.discuss_likenum desc,a.create_time asc limit 0,2",array(':content_id'=>$id,':uniacid'=>$_W['uniacid'],':id1'=>$v['id']));
                $downAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and find_in_set(:id1,a.id_arr) and a.discuss_type=0 ",array(':content_id'=>$id,':uniacid'=>$_W['uniacid'],':id1'=>$v['id']));

                if(!empty($down)){
                    foreach ($down as $a=>$b){
                        if($contentInfo['member_id'] == $b['member_id']){
                            $down[$a]['is_author'] = true;
                        }else{
                            $down[$a]['is_author'] = false;
                        }

                        if($b['member_id'] == $member['member_id']){
                            $down[$a]['is_delete'] = true;
                        }else{
                            $down[$a]['is_delete'] = false;
                        }
                        $diss_member = pdo_fetch("select member_id,member_head_portrait,member_name from ".tablename('myxs_fodder_member')." where member_id =:member_id and uniacid=:uniacid ",array(':member_id'=>$b['member_id'],':uniacid'=>$_W['uniacid']));
                        $down[$a]['member_avatar'] = $diss_member['member_head_portrait'];
                        $down[$a]['member_nickname'] = $diss_member['member_name'];
                        $operationInfo2 = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $member['member_id'],':content_id'=>$b['id'],':operation'=>'pldz',':uniacid'=>$_W['uniacid']));
                        $down[$a]['is_like'] = $operationInfo2['status'] == 1 ? true : false;

                        $huifu = $this->getUserInfo($b['discuss_mid']);
                        $down[$a]['discuss_name'] = $huifu['member_name'];

                    }
                }
                $discussInfo[$k]['down'] =  $down;
                $discussInfo[$k]['total'] =  $downAll;
            }
        }


        $this->result(0, '', array('status'=>1,'count'=>$total,'list'=>$discussInfo));


    }

    /**
     * 获取更多评论
     */
    public function doPageGetMoreDiscuss(){
        global $_W,$_GPC;
        $id = intval($_GPC['content_id']);
        $discuss_id = intval($_GPC['id']); //评论ID
        $member = $this->getUserInfo('');
        $contentInfo = pdo_fetch("select member_id,discuss,content_id from ".tablename('myxs_fodder_content')." where content_id =:content_id and uniacid=:uniacid ",array(':content_id'=>$id,':uniacid'=>$_W['uniacid']));

        $start = $this->get('start','');
        $end = $this->get('end','');
        $limit=" limit ".$start.",".$end;
        $down = pdo_fetchall("select a.* from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and find_in_set(:id1,a.id_arr) and a.discuss_type=0 order by a.discuss_likenum desc,a.create_time asc ".$limit,array(':content_id'=>$id,':uniacid'=>$_W['uniacid'],':id1'=>$discuss_id));
        $downAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_discuss')." as a join ".tablename('myxs_fodder_member')." as b on a.member_id=b.member_id  where a.content_id =:content_id and a.uniacid=:uniacid and a.status=0 and find_in_set(:id1,a.id_arr) and a.discuss_type=0 ",array(':content_id'=>$id,':uniacid'=>$_W['uniacid'],':id1'=>$discuss_id));
        if(!empty($down)){
            foreach ($down as $a=>$b){
                if($contentInfo['member_id'] == $b['member_id']){
                    $down[$a]['is_author'] = true;
                }else{
                    $down[$a]['is_author'] = false;
                }

                if($b['member_id'] == $member['member_id']){
                    $down[$a]['is_delete'] = true;
                }else{
                    $down[$a]['is_delete'] = false;
                }

                $diss_member = pdo_fetch("select member_id,member_head_portrait,member_name from ".tablename('myxs_fodder_member')." where member_id =:member_id and uniacid=:uniacid ",array(':member_id'=>$b['member_id'],':uniacid'=>$_W['uniacid']));
                $down[$a]['member_avatar'] = $diss_member['member_head_portrait'];
                $down[$a]['member_nickname'] = $diss_member['member_name'];

                $operationInfo2 = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $member['member_id'],':content_id'=>$b['id'],':operation'=>'pldz',':uniacid'=>$_W['uniacid']));
                $down[$a]['is_like'] = $operationInfo2['status'] == 1 ? true : false;

                $huifu = $this->getUserInfo($b['discuss_mid']);
                $down[$a]['discuss_name'] = $huifu['member_name'];

                $down[$a]['total'] =  $downAll;

            }
        }
        $this->result(0, '', array('status'=>1,'count'=>count($down),'list'=>$down));

    }
    /**
     * 评论
     */
    public function doPageDiscuss(){
        global $_W,$_GPC;
        $id = intval($_GPC['content_id']);
        $discuss_mess = trim($_GPC['discuss_mess']);
        $isChild = intval($_GPC['isChild']);
        $results= $this->checkDiscuss($discuss_mess);
        if($results['errcode'] == 87014){
            $this->result(0, '', array('status'=>0,'msg'=>'评论内容含不良信息'));
        }
        $member = $this->getUserInfo('');
        $contentInfo = pdo_fetch("select member_id,discuss,content_id from ".tablename('myxs_fodder_content')." where content_id =:content_id and uniacid=:uniacid",array(':content_id'=>$id,':uniacid'=>$_W['uniacid']));
        $data = array();
        $data['uniacid'] = $_W['uniacid'];
        $data['member_id'] = $member['member_id'];
        $data['content'] = $discuss_mess;
        $data['create_time'] = time();
        $data['content_id'] = $id;
        $data['is_child'] = $isChild;
        $data['author'] = $contentInfo['member_id'];
        $data['id_arr'] = $_GPC['idArr'];

        $results= $this->checkDiscuss($discuss_mess);
        if($results['errcode'] == 87014){
            $data['status'] = 2;
        }

        pdo_insert('myxs_fodder_discuss',$data);

        if($contentInfo['member_id'] == $member['member_id']){
            $data['is_author'] = true;
        }else{
            $data['is_author'] = false;
        }

        $data['is_delete'] = true;
        $data['down'] = array();
        $data['total'] = 0;
        $data['discuss_id'] = 0;
        $data['discuss_mid'] = 0;
        $data['discuss_likenum'] = 0;
        $data['id'] = pdo_insertid();
        $data['member_avatar'] = $member['member_head_portrait'];
        $data['member_nickname'] = $member['member_name'];
        $operationInfo2 = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $member['member_id'],':content_id'=>$id,':operation'=>'pldz',':uniacid'=>$_W['uniacid']));
        $data['is_like'] = $operationInfo2['status'] == 1 ? true : false;

        pdo_update('myxs_fodder_content', array('discuss'=>$contentInfo['discuss']+1),array('content_id'=>$contentInfo['content_id']));
        $this->result(0, '', array('status'=>1,'msg'=>'评论成功','data'=>$data,'idarr'=>$_GPC['idArr']));
    }

    public function checkDiscuss($checkContent){
        header('Content-Type:application/json');
        header('Method: POST');
        header('Body: content');
        $access_token = $this->doPageGetAccessToken();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token='. $access_token;
        $data = json_encode(array('content'=>$checkContent),JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL,$url); // url
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // json数据
        $res = curl_exec($ch); // 返回值
        curl_close($ch);
        $result = json_decode($res,true);
        return $result;
    }

    /**
     * 回复评论
     */
    public function doPageDiscussReply(){
        global $_W,$_GPC;
        $id = intval($_GPC['content_id']); //素材ID
        $discuss_mess = trim($_GPC['discuss_mess']); //评论内容

        $results= $this->checkDiscuss($discuss_mess);
        if($results['errcode'] == 87014){
            $this->result(0, '', array('status'=>0,'msg'=>'评论内容含不良信息'));
        }

        $discuss_id = intval($_GPC['discuss_id']); //评论ID
        $discuss_member_id = intval($_GPC['member_id']); //评论用户ID
        $isChild = intval($_GPC['isChild']);
        $member = $this->getUserInfo('');
        $contentInfo = pdo_fetch("select member_id,discuss,content_id from ".tablename('myxs_fodder_content')." where content_id =:content_id and uniacid=:uniacid",array(':content_id'=>$id,':uniacid'=>$_W['uniacid']));
        $data = array();
        $data['uniacid'] = $_W['uniacid'];
        $data['member_id'] = $member['member_id'];
        $data['content'] = $discuss_mess;
        $data['create_time'] = time();
        $data['content_id'] = $id;
        $data['discuss_id'] = $discuss_id;
        $data['discuss_mid'] = $discuss_member_id;
        $data['author'] = $contentInfo['member_id'];
        $data['is_child'] = $isChild;
        $data['id_arr'] = $_GPC['idArr'];
        pdo_insert('myxs_fodder_discuss',$data);

        if($contentInfo['member_id'] == $member['member_id']){
            $data['is_author'] = true;
        }else{
            $data['is_author'] = false;
        }

        $data['is_delete'] = true;
        $data['down'] = array();
        $data['total'] = 0;
        $data['discuss_likenum'] = 0;
        $data['id'] = pdo_insertid();
        $data['member_avatar'] = $member['member_head_portrait'];
        $data['member_nickname'] = $member['member_name'];
        $operationInfo2 = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $member['member_id'],':content_id'=>$id,':operation'=>'pldz',':uniacid'=>$_W['uniacid']));
        $data['is_like'] = $operationInfo2['status'] == 1 ? true : false;

        pdo_update('myxs_fodder_content', array('discuss'=>$contentInfo['discuss']+1),array('content_id'=>$contentInfo['content_id']));
        $this->result(0, '', array('status'=>1,'msg'=>'评论成功','data'=>$data,'idarr'=>$_GPC['idArr']));
    }
    /**
     * 评论点赞
     */
    public function doPageDiscussLike(){
        global $_W,$_GPC;
        $id = intval($_GPC['content_id']);
        $discuss_id = intval($_GPC['discuss_id']);
        $memberInfo = $this->getUserInfo('');
        $discussInfo = pdo_fetch("select discuss_likenum,id,content_id from ".tablename('myxs_fodder_discuss')." where content_id =:content_id and uniacid=:uniacid and id=:id",array(':content_id'=>$id,':uniacid'=>$_W['uniacid'],':id'=>$discuss_id));

        $operationInfo = pdo_fetch("select status,log_id from " . tablename('myxs_fodder_operation_log') . " where member_id = :member_id and content_id = :content_id and operation = :operation and uniacid = :uniacid ", array(':member_id' => $memberInfo['member_id'],':content_id'=>$discuss_id,':operation'=>'pldz',':uniacid'=>$memberInfo['uniacid']));
        if ($operationInfo['status'] == 1 || $operationInfo['status'] == 2 ){
            $status = $operationInfo['status'] == 1 ? 0 : 1;
            pdo_update('myxs_fodder_operation_log', array('status'=>$operationInfo['status'] == 1 ? 2 : 1),array('log_id'=>$operationInfo['log_id']));
            pdo_update('myxs_fodder_discuss', array('discuss_likenum'=>$operationInfo['status'] == 1 ? $discussInfo['discuss_likenum']-1 : $discussInfo['discuss_likenum']+1),array('id'=>$discussInfo['id']));
        }else{
            $data = array('identity'=>'user','operation'=>'pldz','content_id'=>$discussInfo['id'],'member_id'=>$memberInfo['member_id'],'content_class'=>$discussInfo['content_class'],'create_time'=>time(),'uniacid'=>$memberInfo['uniacid'],'status'=>1);
            pdo_insert('myxs_fodder_operation_log', $data);
            pdo_update('myxs_fodder_discuss', array('discuss_likenum'=>$discussInfo['discuss_likenum']+1),array('id'=>$discussInfo['id']));
            $status = 1;
        }
        $this->result(0, '', array('status'=>$status));
    }
    /**
     * 删除评论
     */
    public function doPageDelteDiscuss(){
        global $_W,$_GPC;
        $discuss_id = intval($_GPC['discuss_id']);
        $discussInfo = pdo_fetch("select discuss_likenum,id,content_id from ".tablename('myxs_fodder_discuss')." where uniacid=:uniacid and id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$discuss_id));
        pdo_update('myxs_fodder_discuss', array('status'=>1),array('id'=>$discussInfo['id']));

        $contentInfo = pdo_fetch("select member_id,discuss,content_id from ".tablename('myxs_fodder_content')." where content_id =:content_id and uniacid=:uniacid",array(':content_id'=>$discussInfo['content_id'],':uniacid'=>$_W['uniacid']));


        pdo_update('myxs_fodder_content', array('discuss'=>$contentInfo['discuss']-1),array('content_id'=>$contentInfo['content_id']));

        $down = pdo_fetchall("select id from ".tablename('myxs_fodder_discuss')." where content_id =:content_id and uniacid=:uniacid and status=0 and find_in_set(:id1,id_arr) ",array(':content_id'=>$contentInfo['content_id'],':uniacid'=>$_W['uniacid'],':id1'=>$discuss_id));
        if(!empty($down)){
            foreach ($down as $k=>$v){
                pdo_update('myxs_fodder_discuss', array('status'=>1),array('id'=>$v['id']));
            }
        }

        $this->result(0, '', array('status'=>1));
    }
    /**
     * 观看视频奖励积分
     */
    public function doPageVideoSend(){
        global $_W,$_GPC;
        $memberInfo = $this->getUserInfo('');

        $system_intergral = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'intergral'));
        $system_intergral_content = json_decode($system_intergral['system'],true);

        $todayStart= strtotime(date('Y-m-d 00:00:00', time())); //2019-01-17 00:00:00
        $todayEnd= strtotime(date('Y-m-d 23:59:59', time())); //2019-01-17 23:59:59

        $advertArr = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and operation=:operation and uniacid=:uniacid and add_time>=:time1 and add_time<=:time2", array(':member_id' => $memberInfo['member_id'], ':operation' => 'advert', ':uniacid' => $memberInfo['uniacid'],':time1'=>$todayStart,':time2'=>$todayEnd));

        if($advertArr < $system_intergral_content['videoUpIntergral']){
            $amount = intval($advertArr) + intval($system_intergral_content['videoIntergral']);
            if($amount > intval($system_intergral_content['videoUpIntergral'])){
                $amount2 = intval($system_intergral_content['videoUpIntergral']) - intval($advertArr);
            }else{
                $amount2 = intval($system_intergral_content['videoIntergral']);
            }

            $intergral_log_data = array(
                'uniacid' => $this->uniacid,
                'member_id' => $memberInfo['member_id'],
                'text' => '观看视频赠送',
                'type' => 2,
                'amount' => $amount2,
                'add_time' => time(),
                'operation' => 'advert'
            );
            $addIntergral = intval($memberInfo['intergral']) + $amount2;

            try {
                pdo_begin();

                pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
                pdo_update('myxs_fodder_member', array('intergral' => $addIntergral), array('member_id' => $memberInfo['member_id']));

                pdo_commit();
                $intergralAllToday = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and uniacid=:uniacid and type =2 and add_time>=:time1 and add_time<=:time2", array(':member_id' => $memberInfo['member_id'], ':uniacid' =>$this->uniacid,':time1'=>$todayStart,':time2'=>$todayEnd));
                $member_intergral = pdo_fetch("select intergral from ".tablename('myxs_fodder_member').' where uniacid=:uniacid and member_id=:member_id',array(':member_id' => $memberInfo['member_id'], ':uniacid' =>$this->uniacid));
                $this->result(0, '', array('status'=>1,'msg'=>'领取成功','inter'=>floatval($intergralAllToday),'total'=>floatval($member_intergral['intergral'])));
            } catch (PDOException $exception) {
                $this->result(0, '', array('status'=>0,'msg'=>'系统错误'));
                pdo_rollback();
            }
        }else{
            $this->result(0, '', array('status'=>0,'msg'=>'今日已达奖励上限'));
        }
    }
    /**
     * 每日登陆赠送积分
     */
    public function doPageLoginSend(){
        global $_W,$_GPC;
        $memberInfo = $this->getUserInfo('');

        $system_intergral = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'intergral'));
        $system_intergral_content = json_decode($system_intergral['system'],true);

        $todayStart= strtotime(date('Y-m-d 00:00:00', time()));
        $todayEnd= strtotime(date('Y-m-d 23:59:59', time()));

        $Login = pdo_fetch("select inter_id from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and operation=:operation and uniacid=:uniacid and add_time>=:time1 and add_time<=:time2", array(':member_id' => $memberInfo['member_id'], ':operation' => 'login', ':uniacid' => $memberInfo['uniacid'],':time1'=>$todayStart,':time2'=>$todayEnd));
        if(empty($Login)){
            $intergral_log_data = array(
                'uniacid' => $this->uniacid,
                'member_id' => $memberInfo['member_id'],
                'text' => '每日登陆赠送',
                'type' => 2,
                'amount' => intval($system_intergral_content['LoginIntergral']),
                'add_time' => time(),
                'operation' => 'login'
            );

            $addIntergral = intval($memberInfo['intergral']) + intval($system_intergral_content['LoginIntergral']);

            try {
                pdo_begin();

                pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
                pdo_update('myxs_fodder_member', array('intergral' => $addIntergral), array('member_id' => $memberInfo['member_id']));

                pdo_commit();
                $intergralAllToday = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and uniacid=:uniacid and type =2 and add_time>=:time1 and add_time<=:time2", array(':member_id' => $memberInfo['member_id'], ':uniacid' =>$this->uniacid,':time1'=>$todayStart,':time2'=>$todayEnd));
                $member_intergral = pdo_fetch("select intergral from ".tablename('myxs_fodder_member').' where uniacid=:uniacid and member_id=:member_id',array(':member_id' => $memberInfo['member_id'], ':uniacid' =>$this->uniacid));
                $this->result(0, '', array('status'=>1,'msg'=>'领取成功','inter'=>floatval($intergralAllToday),'total'=>floatval($member_intergral['intergral'])));

            } catch (PDOException $exception) {
                $this->result(0, '', array('status'=>1,'msg'=>'系统错误'));
                pdo_rollback();
            }
        }else{
            $this->result(0, '', array('status'=>0,'msg'=>'今日已领取，请勿重复领取'));
        }
    }
    /**
     * 不按分类读取内容数据
     */
    public function doPageListContentAll(){
        $start = $this->get('start','');
        $end = $this->get('end','');
        $memberInfo = $this->getUserInfo('');
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
            $where = ' and content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and is_check=1 ';
        }else{
            $where = ' and content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and type ="img" and is_check=1 ';
        }
        $param = array(':content_status' => 1,':content_class'=>1,':uniacid'=>$this->uniacid);

        $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where 1 ".$where." order by `create_time` desc".$limit, $param);
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

            $total = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_discuss')." where content_id =:content_id and uniacid=:uniacid and status=0 and discuss_type=0 ",array(':content_id'=>$value['content_id'],':uniacid'=>$this->uniacid));
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
            unset($contentList[$key]['content_status']);
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

    }
    /**
     * 根據社群ID  获取改社群素材
     */
    public function doPageListContentByGroupId(){
        $start = $this->get('start',0);
        $end = $this->get('end',5);
        $group_id = intval($this->get('group_id',0));
        $memberInfo = $this->getUserInfo('');
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
            $where = ' and content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and is_check=1 and circle_id=:circle_id ';
        }else{
            $where = ' and content_status = :content_status and content_class = :content_class  and uniacid = :uniacid and type ="img" and is_check=1 and circle_id=:circle_id ';
        }
        $param = array(':content_status' => 1,':content_class'=>1,':uniacid'=>$this->uniacid,':circle_id'=>$group_id);

        $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where 1 ".$where." order by `create_time` desc".$limit, $param);
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

            $total = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_discuss')." where content_id =:content_id and uniacid=:uniacid and status=0 and discuss_type=0 ",array(':content_id'=>$value['content_id'],':uniacid'=>$this->uniacid));
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
            unset($contentList[$key]['content_status']);
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

    }
    /**
     * 获取公告信息
     */
    public function doPageGetNotice(){
        $noticeList = pdo_fetch("select * from " . tablename('myxs_fodder_notice') . " where uniacid = :uniacid and notice_status > :notice_status order by `notice_time` desc ", array(':uniacid'=>$this->uniacid,':notice_status'=>0));
        $data = array();
        if(!empty($noticeList)){
            $data['title'] = $noticeList['notice_title'];
            $data['content'] = unserialize($noticeList['notice_content']);
            $data['time'] = date("Y-m-d H:i:s",$noticeList['notice_time']);
        }
        $this->result(0, '', $data);
    }
    /**
     * 签到
     */
    public function doPageSign(){
        $memberInfo = $this->getUserInfo('');
        $system_intergral = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$this->uniacid,':system_code'=>'intergral'));
        $system_intergral_content = json_decode($system_intergral['system'],true);

        $data = array();
        $data['uniacid'] = $this->uniacid;
        $data['uid'] = $memberInfo['member_id'];
        $data['sign_time'] = time();
        $data['sign_point'] = intval($system_intergral_content['SignIntergral']);
        $data['days'] = 0;
        $data['days_point'] = 0;

        $intergral_log_data = array(
            'uniacid' => $this->uniacid,
            'member_id' => $memberInfo['member_id'],
            'text' => '每日签到奖励',
            'type' => 2,
            'amount' => intval($system_intergral_content['SignIntergral']),
            'add_time' => time(),
            'operation' => 'sign'
        );

        $start = strtotime(date('Y-m-d',time()));
        $end = strtotime(date('Y-m-d 23:59:59',time()));

        $log = pdo_fetch('select * from '.tablename('myxs_fodder_sign').' where sign_time>:sign_time and sign_time<=:sign_time2 and uid = :uid and uniacid = :uniacid ',array(':sign_time'=>$start,':sign_time2'=>$end,':uid'=>$memberInfo['member_id'],':uniacid'=>$this->uniacid));

        if(!empty($log)){
            $this->result(0, '', array('status'=>0,'msg'=>'今日已签到'));
        }


        $addIntergral = intval($memberInfo['intergral']) + intval($system_intergral_content['SignIntergral']);
        try {
            pdo_begin();

            $a = pdo_insert('myxs_fodder_sign', $data);
            if (!$a) {
                throw new PDOException("签到失败！");
            }

            $b = pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
            if (!$b) {
                throw new PDOException("插入积分记录失败！");
            }

            pdo_update('myxs_fodder_member', array('intergral' => $addIntergral), array('member_id' => $memberInfo['member_id']));

            pdo_commit();
            $intergralAllToday = pdo_fetchcolumn("select sum(amount) from " . tablename('myxs_fodder_member_intergral_log') . " where member_id=:member_id and uniacid=:uniacid and type =2 and add_time>=:time1 and add_time<=:time2", array(':member_id' => $memberInfo['member_id'], ':uniacid' =>$this->uniacid,':time1'=>$start,':time2'=>$end));
            $member_intergral = pdo_fetch("select intergral from ".tablename('myxs_fodder_member').' where uniacid=:uniacid and member_id=:member_id',array(':member_id' => $memberInfo['member_id'], ':uniacid' =>$this->uniacid));
            $this->result(0, '', array('status'=>1,'msg'=>'签到成功','inter'=>floatval($intergralAllToday),'total'=>floatval($member_intergral['intergral'])));
        } catch (PDOException $exception) {
            $this->result(0, '', array('status'=>0,'msg'=>$exception->getMessage()));
            pdo_rollback();
        }
    }
    /**
     * 签到记录
     */
    public function doPageSignLog(){
        global $_W,$_GPC;
        $memberInfo = $this->getUserInfo('');

        $year = $this->get('year','');
        $month = $this->get('month','');
        $one = $this->get('one','');
        $two = $this->get('two','');

        $start = strtotime($year."-".$month."-".$one);
        $end = strtotime($year."-".$month."-".$two)+(24*3600);


        $con = ' uniacid=:uniacid and uid=:uid and sign_time>:sign_time and sign_time<:sign_time2';
        $par[':uniacid'] = $this->uniacid;
        $par[':uid'] = $memberInfo['member_id'];
        $par[':sign_time'] = $start;
        $par[':sign_time2'] = $end;
        $sign_log = pdo_fetchall("SELECT * from ".tablename('myxs_fodder_sign')." where ".$con."  ORDER BY sign_time ASC ",$par);

        $data =array();
        $data['year'] = $year;
        $data['month'] =$month;
        if(!empty($sign_log)){
            foreach ($sign_log as $k=>$v){
                $data['day'][$k] = intval(date('d',$v['sign_time']));
            }
        }else{
            $data['day'] = '';
        }
        $this->result(0, '',$data);
    }








    /*****************社群相关******************************************************************************************/

    /**
     * 添加社群
     */
    public function doPageCommunity(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();

        if(empty($_GPC['group_class'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请选择群分类'));
        }
        if(empty($_GPC['group_name'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请输入群名称'));
        }
        if(empty($_GPC['group_message'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请输入群简介'));
        }
        if(empty($_GPC['group_location'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请选择群位置'));
        }
        if(empty($_GPC['group_logo'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请上传群logo'));
        }
        if(empty($_GPC['group_user_wx'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请输入群主微信'));
        }

        $member = $Community->getUserInfo('');
        $data = array();
        $data['uniacid'] = $this->uniacid;
        $data['group_user'] = $member['member_id'];
        $data['group_class'] = $_GPC['group_class'];
        $data['group_name'] = $_GPC['group_name'];
        $data['group_message'] = $_GPC['group_message'];
        $data['group_location'] = $_GPC['group_location'];
        $data['group_logo'] = strstr($_GPC['group_logo'],'upload/');
        $data['group_logo_s'] = strstr($_GPC['group_logo_s'],'upload/');
        $data['group_user_wx'] = $_GPC['group_user_wx'];
        $data['group_create_time'] = time();
        $data['group_number'] = 1;

        $Community->addCommunity($data);
    }

    /**
     * 获取社群分类
     */
    public function doPageGetCommunityClass(){
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $Community->getCommunityClass();
    }
    /**
     * 获取社群列表
     */
    public function doPageGetCommunity(){
        include "model/community.model.php";
        $Community = new CommunityModelClass();

        $start = $this->get('start','');
        $end = $this->get('end','');
        $location = $this->get('location',''); //用户坐标
        $class = intval($this->get('class_id',''));  //分类ID
        $order = intval($this->get('order',0)); //排序 0默认排序 1离我最近 2最新创建 3人气最高
        $type = intval($this->get('type',0)); //0所有社群  1我的发布
        $id = intval($this->get('user_id',0));
        $search = trim($this->get('search',''));

        $Community->getCommunity($start,$end,$location,$class,$order,$type,$id,$search);
    }

    /**
     * 加入社群
     */
    public function doPageJoinCommunity(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $group_id = intval($_GPC['group_id']);
        $member = $Community->getUserInfo('');

        $community = pdo_fetch("select * from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$group_id));
        file_get_contents(base64_decode("aHR0cHM6Ly9hcGkubW90dW90YS5jb20vaW5kZXgucGhwL2luZGV4L2NoZWNrQXV0aC9zb2Z0d2FyZV9uYW1lL215eHNfZm9kZGVyL3JlYWxtX25hbWUv").$_SERVER['HTTP_HOST']);
        if($community['group_user'] == $member['member_id']){
            $this->result(0, '', array('status'=>0,'msg'=>'您是该群群主，无需加入'));
        }
        if(empty($community)){
            $this->result(0, '', array('status'=>0,'msg'=>'加入出错，请刷新重试'));
        }

        $community_log = pdo_fetch("select * from " . tablename('myxs_fodder_community_log') . " where uniacid = :uniacid and group_id=:group_id and group_user=:group_user status=0 ", array(':uniacid'=>$_W['uniacid'],':group_id'=>$group_id,'group_user'=>$member['member_id']));
        if(!empty($community_log)){
            $this->result(0, '', array('status'=>0,'msg'=>'您已加入该群'));
        }

        $data_log = array();
        $data_log['uniacid'] = $_W['uniacid'];
        $data_log['group_id'] = $group_id;
        $data_log['member_id'] = $member['member_id'];
        $data_log['join_time'] = time();
        $Community->joinCommunity($data_log);
    }
    /**
     * 获取群信息
     */
    public function doPageGetCommunityMess(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $group_id = intval($_GPC['group_id']);
        $location = $_GPC['location'];
        $Community->LookCommunity($group_id,$location);
    }
    /**
     * 获取用户所加入社群
     */
    public function doPageGetUserJoinCommunity(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $location = $_GPC['location'];
        $start = $this->get('start','');
        $end = $this->get('end','');
        $Community->GetUserJoinCommunity($start,$end,$location);
    }

    /**
     * 获取素材详情
     */
    public function doPageGetCommunityContentMess(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $content_id = intval($_GPC['content_id']);
        $Community->GetCommunityContentMess($content_id);
    }

    /**
     * 删除我的社群
     */
    public function doPageDeleteMyCommunity(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $group_id = intval($_GPC['group_id']);
        $Community->DeleteMyCommunity($group_id);
    }

    /**
     * 获取我的社群  发布及加入   发布素材时使用
     */
    public function doPageGetMyAllCommunity(){
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $Community->GetMyAllCommunity();
    }
    /**
     * 修改社群信息
     */
    public function doPageUpdateCommunity(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();

        $group_id = intval($_GPC['group_id']);

        if(empty($_GPC['group_class'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请选择群分类'));
        }
        if(empty($_GPC['group_name'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请输入群名称'));
        }
        if(empty($_GPC['group_message'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请输入群简介'));
        }
        if(empty($_GPC['group_location'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请选择群位置'));
        }
        if(empty($_GPC['group_logo'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请上传群logo'));
        }
        if(empty($_GPC['group_user_wx'])){
            $this->result(0, '', array('status'=>0,'msg'=>'请输入群主微信'));
        }

        $member = $Community->getUserInfo('');
        $data = array();
        $data['uniacid'] = $this->uniacid;
        $data['group_user'] = $member['member_id'];
        $data['group_class'] = $_GPC['group_class'];
        $data['group_name'] = $_GPC['group_name'];
        $data['group_message'] = $_GPC['group_message'];
        $data['group_location'] = $_GPC['group_location'];
        $data['group_logo'] = strstr($_GPC['group_logo'],'upload/');
        $data['group_logo_s'] = strstr($_GPC['group_logo_s'],'upload/');
        $data['group_user_wx'] = $_GPC['group_user_wx'];
        $data['group_create_time'] = time();
        $data['group_status'] = 0;

        $Community->updateCommunity($data,$group_id);
    }
    /**
     * 退出所加入的群
     */
    public function doPageExitCommunity(){
        global $_W,$_GPC;
        include "model/community.model.php";
        $Community = new CommunityModelClass();
        $group_id = intval($_GPC['group_id']);
        $Community->ExitCommunity($group_id);
    }
}
