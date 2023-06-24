<?php
/**
 * myxs_fodder模块微站定义
 *
 * @author myxinshang
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Myxs_fodderModuleSite extends WeModuleSite {


    public function doWebBasics() {
        global $_GPC,$_W;
        $titleList = array(
            'system' => '系统设置',
            'intergral'=>'积分设置',
        );
        $class = empty($_GPC['class']) ? 'system' : $_GPC['class'];
        $system = pdo_fetch("select * from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>$class));

        $check_url = 'https://'.$_SERVER['HTTP_HOST'].'/addons/myxs_fodder/task/checkfile.php?i='.$_W['uniacid'];

        if ($class == 'system'){
            if($_GPC['submit']=='提交') {

                if ($_GPC['title']==""){
                    message('请输入系统名称！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['member_name']==""){
                    message('请输入编辑名称！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['map_key']==""){
                    message('请设置并输入腾讯地图KEY！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['share_txt']==""){
                    message('请输入分享简介！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['share_bg']==""){
                    message('请上传分享封面图！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['logo_bg']==""){
                    message('请上传LOGO！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['stat_bg']==""){
                    message('请上传程序启动页！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }
                if ($_GPC['communityTip']==""){
                    message('请输入社群发布须知！', $this->createWebUrl('Basics', array('class' => 'system')), 'error');
                }

                $systemData = array();
                $systemData['system_code'] = $class;
                $systemData['system_content'] = json_encode(array('stat_bg' => $_GPC['stat_bg'],'logo_bg' => $_GPC['logo_bg'],'qr_bg' => $_GPC['qr_bg'],'content_bg'=>$_GPC['content_bg'],'share_bg'=>$_GPC['share_bg'],'wg_img'=>$_GPC['wg_img']));
                $systemData['create_time'] = time();
                $systemData['system'] = json_encode(array('title' => $_GPC['title'],'map_key'=>$_GPC['map_key'],'communityTip'=>$_GPC['communityTip'],'group_chat'=>$_GPC['group_chat'],'member_name'=>$_GPC['member_name'],'copyright'=>$_GPC['copyright'],'grouping'=>$_GPC['grouping'],'share_txt'=>$_GPC['share_txt'],'day_sign_status'=>$_GPC['day_sign_status'],'video_is_show'=>intval($_GPC['video_is_show']),'watermark_status'=>0));
                $systemData['uniacid'] = $_W['uniacid'];
                if (empty($system)) {
                    pdo_insert('myxs_fodder_system', $systemData);
                    message('添加系统设置成功', $this->createWebUrl('Basics', array('class' => 'system')), 'success');
                } else {
                    pdo_update('myxs_fodder_system', $systemData, array('system_id' => $system['system_id']));
                    message('编辑系统设置成功', $this->createWebUrl('Basics', array('class' => 'system')), 'success');
                }
            }
            $system_content  = json_decode($system['system_content'],true);
            $system_seting = json_decode($system['system'],true);
            include $this->template('default/Basics/index');
        }
        if($class=='intergral'){
            if($_GPC['submit']=='提交') {
                $systemData = array();
                $systemData['system_code'] = $class;
                $systemData['create_time'] = time();
                if(empty($_GPC['forwardIntergral'])){
                    $_GPC['forwardIntergral']=0;
                }
                if (empty($_GPC['releaseSendIntergral'])){
                    $_GPC['releaseSendIntergral'] = 0;
                }
                if (empty($_GPC['videoIntergral'])){
                    $_GPC['videoIntergral'] = 0;
                }
                if (empty($_GPC['videoUpIntergral'])){
                    $_GPC['videoUpIntergral'] = 0;
                }
                if (empty($_GPC['LoginIntergral'])){
                    $_GPC['LoginIntergral'] = 0;
                }
                if (empty($_GPC['inviteIntergral'])){
                    $_GPC['inviteIntergral'] = 0;
                }
                if (empty($_GPC['SignIntergral'])){
                    $_GPC['SignIntergral'] = 0;
                }
                if ($_GPC['adminWxNum']==""){
                    message('请输入微信号，以便于用户充值！', $this->createWebUrl('Basics', array('class' => 'intergral')), 'error');
                }
                if ($_GPC['intergralTip']==""){
                    message('请输入积分充值说明！', $this->createWebUrl('Basics', array('class' => 'intergral')), 'error');
                }
                $systemData['system'] = json_encode(array('releaseSendIntergral'=>$_GPC['releaseSendIntergral'],'SignIntergral'=>$_GPC['SignIntergral'],'inviteIntergral'=>$_GPC['inviteIntergral'],'LoginIntergral'=>$_GPC['LoginIntergral'],'videoIntergral'=>$_GPC['videoIntergral'],'videoUpIntergral'=>$_GPC['videoUpIntergral'],'takeOutIntergral' => $_GPC['takeOutIntergral'],'rewardIntergral'=>$_GPC['rewardIntergral'],'givingIntergral'=>$_GPC['givingIntergral'],'forwardIntergral'=>$_GPC['forwardIntergral'],'adminWxNum'=>$_GPC['adminWxNum'],'intergralTip'=>$_GPC['intergralTip']));
                $systemData['uniacid'] = $_W['uniacid'];
                if (empty($system)) {
                    pdo_insert('myxs_fodder_system', $systemData);
                    message('添加积分设置成功', $this->createWebUrl('Basics', array('class' => 'intergral')), 'success');
                } else {
                    pdo_update('myxs_fodder_system', $systemData, array('system_id' => $system['system_id']));
                    message('编辑积分设置成功', $this->createWebUrl('Basics', array('class' => 'intergral')), 'success');
                }
            }
            $system_seting = json_decode($system['system'],true);
            include $this->template('default/Basics/intergral');

        }
    }

    /**
     *  获取广场首页内容
     */
    public function doWebContent() {
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'main';

        $where="";
        if($operation=='search'){
            $search=trim($_GPC['searchmess']);
            if(!empty($search)){
                $where.=" and text like '%$search%'";
            }
        }

        $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where  content_status > :content_status and content_class = :content_class and uniacid = :uniacid  ".$where."  order by `content_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':content_status'=>0,':content_class'=>1,':uniacid'=>$_W['uniacid']));
        $class_status = array('删除','显示','隐藏');



        foreach ($contentList as $key =>$value){
            $Class = pdo_fetch("select * from " . tablename('myxs_fodder_class') . " where class_id = :class_id", array(':class_id' => $value['class_id']));
            $contentList[$key]['class_id'] = $Class['class_name'];
            $contentList[$key]['content_status'] = $class_status[$value['content_status']];
            $contentList[$key]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
            $grouping = pdo_fetch("select * from " . tablename('myxs_fodder_grouping') . " where grouping_id = :grouping_id ", array(':grouping_id'=>$value['grouping_id']));
            $contentList[$key]['grouping_id'] = $value['grouping_id'] == 0 ? '默认分组': $grouping['grouping_name'];
        }
        $contentListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_content') . " where content_class = :content_class and uniacid = :uniacid  ".$where."   order by `content_id` desc", array(':content_class'=>1,':uniacid'=>$_W['uniacid']));
        $pager = pagination($contentListTotal, $pindex, $psize);
        include $this->template('default/content/index');
    }

    /**
     *  快速修改内容显示状态
     */
    public function doWebUpdateContent(){
        global $_W  ,$_GPC;
        $content =pdo_fetch("select * from " . tablename('myxs_fodder_content') . " where content_id = :content_id", array(':content_id' => $_GPC['id']));
        if ($content['content_status'] == 1){
            pdo_update('myxs_fodder_content',array('content_status' => 2 ),array('content_id'=>$_GPC['id']));
            echo '隐藏';
            exit;
        }else{
            pdo_update('myxs_fodder_content',array('content_status' => 1 ),array('content_id'=>$_GPC['id']));
            echo '显示';
            exit;
        }
    }

    /**
     * 广场内容  增、删、改、查
     */
    public function doWebContentMess() {
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_content') . " WHERE content_id = :content_id", array(':content_id' => $id));
            if (empty($row)) {
                message('抱歉，内容不存在或是已经被删除！');
            }
            pdo_update('myxs_fodder_content',array('content_status'=>0),array('content_id'=>$id));
            message('删除成功！', referer(), 'success');
        } elseif($operation=='add'){
            $class =pdo_fetchall("select * from " . tablename('myxs_fodder_class') . " where class_status=:class_status and uniacid = :uniacid ", array('class_status'=>1,':uniacid'=>$_W['uniacid']));
            $groupingList = pdo_fetchall("select * from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid ", array(':uniacid'=>$_W['uniacid']));

            $sub=true;
            if($_GPC['submit']=='提交'){

                if(empty($_GPC['text'])){
                    message('请输入文本内容！');
                }
                $data=array();
                $data['type']=trim($_GPC['type']);
                $data['class_id']=intval($_GPC['class_id']);
                $data['grouping_id']=intval($_GPC['grouping_id']);
                $data['member_id']=1;
                $data['text']=$_GPC['text'];
                $data['fictitious_clnb']=intval($_GPC['fictitious_clnb']);
                $data['fictitious_donnb']=intval($_GPC['fictitious_donnb']);
                $data['fictitious_sharenb']=intval($_GPC['fictitious_sharenb']);

                if ($data['type'] == 'video'){
                    $data['content']=json_encode(array($_GPC['video_path'],300,255));
                }else{
                    $data['content']=json_encode($_GPC['thumbs']);
                }
                $data['content_class']=1;
                $data['content_status']=intval($_GPC['content_status']);
                $data['create_time']=time();
                $data['update_time']=time();
                $data['uniacid']=$_W['uniacid'];
                if (empty( $data['text'])){
                    message('文本内容不能为空！', $this->createWebUrl('ContentMess', array('op' => 'add', 'id' => $id)), 'error');
                }
                if (empty($data['content'])){
                    message('图片附件不能为空！', $this->createWebUrl('ContentMess', array('op' => 'add', 'id' => $id)), 'error');
                }
                $insertid=pdo_insert('myxs_fodder_content',$data);
                message('内容添加成功！', $this->createWebUrl('content'), 'success');
            }
        }else{
            $sub=false;
            $content =pdo_fetch("select * from " . tablename('myxs_fodder_content') . " where content_id = :content_id", array(':content_id' => $id));
            $member_admin=pdo_fetch("select username from " . tablename('users') . " where uid = :uid", array(':uid' => $content['member_id']));

            $member_user=pdo_fetch("select member_name from " . tablename('myxs_fodder_member') . " where member_id = :member_id", array(':member_id' => $content['member_id']));
            if(empty($member_admin)){
                $member_name=$member_user['member_name'];
            }else{
                $member_name=$member_admin['username'];
            }


            $contentimg=json_decode($content['content'],true);
            $class =pdo_fetchall("select * from " . tablename('myxs_fodder_class') . " where class_status=:class_status and uniacid = :uniacid ", array('class_status'=>1,':uniacid'=>$_W['uniacid']));
            $groupingList = pdo_fetchall("select * from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid ", array(':uniacid'=>$_W['uniacid']));

            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['type']=trim($_GPC['type']);
                $data['class_id']=intval($_GPC['class_id']);
                $data['grouping_id']=intval($_GPC['grouping_id']);
                $data['text']=$_GPC['text'];
                $data['fictitious_clnb']=intval($_GPC['fictitious_clnb']);
                $data['fictitious_donnb']=intval($_GPC['fictitious_donnb']);
                $data['fictitious_sharenb']=intval($_GPC['fictitious_sharenb']);
                $data['content_status']=intval($_GPC['content_status']);
                if($_GPC['up_type'] == 1){
                    $data['type'] = 'video';
                    $data['content']=json_encode(array($_GPC['video_path'],300,255));
                }else{
                    $data['type'] = 'img';
                    $data['content']=json_encode($_GPC['thumbs']);
                }
                if (empty( $data['text'])){
                    message('文本内容不能为空！', $this->createWebUrl('ContentMess', array('op' => 'update', 'id' => $id)), 'error');
                }
                if (empty($data['content'])){
                    message('图片附件不能为空！', $this->createWebUrl('ContentMess', array('op' => 'update', 'id' => $id)), 'error');
                }
                pdo_update('myxs_fodder_content',$data,array('content_id'=>$_GPC['id']));
                message('内容更新成功！', $this->createWebUrl('contentmess', array('op' => 'update', 'id' => $id)), 'success');
            }

        }
        include $this->template('default/content/post');
    }

    public function doWebClass() {
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $classList = pdo_fetchall("select * from " . tablename('myxs_fodder_class') . " where circle_id = :circle_id and uniacid = :uniacid order by `class_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':circle_id'=>'0',':uniacid'=>$_W['uniacid']));
        $class_status = array('1'=>'启用','2'=>'删除');
        foreach ($classList as $key =>$value){
            $Class = pdo_fetch("select * from " . tablename('myxs_fodder_class') . " where class_id = :class_id", array(':class_id' => $value['class_id']));
            $classList[$key]['class_status'] = $class_status[$value['class_status']];
            $classList[$key]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
        }
        $classListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_class') . " where circle_id = :circle_id and uniacid = :uniacid order by `class_id` desc", array(':circle_id'=>'0',':uniacid'=>$_W['uniacid']));
        $pager = pagination($classListTotal, $pindex, $psize);
        include $this->template('default/class/index');
    }

    /**
     * 分类  增、删、改、查
     */
    public function doWebClassMess() {
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_class') . " WHERE class_id = :class_id", array(':class_id' => $id));
            if (empty($row)) {
                message('抱歉，内容不存在或是已经被删除！');
            }
            pdo_update('myxs_fodder_class',array('class_status'=>2),array('class_id'=>$id));
            message('删除成功！', referer(), 'success');
        }elseif($operation=='add'){
            if($_GPC['op']=='add'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){

                if(empty($_GPC['class_name'])){
                    message('请输入分类名称！');
                }
                $data=array();
                $data['class_name']=$_GPC['class_name'];
                $data['class_status']=intval($_GPC['class_status']);
                $data['create_time']=time();
                $data['uniacid']=$_W['uniacid'];
                $insertid=pdo_insert('myxs_fodder_class',$data);
                message('分类添加成功！', $this->createWebUrl('class'), 'success');
            }
        }else{
            $sub=false;
            $content =pdo_fetch("select * from " . tablename('myxs_fodder_class') . " where class_id = :class_id", array(':class_id' => $id));
            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['class_name']=$_GPC['class_name'];
                $data['class_status']=intval($_GPC['class_status']);
                $data['create_time']=time();
                pdo_update('myxs_fodder_class',$data,array('class_id'=>$_GPC['id']));
                message('分类更新成功！', $this->createWebUrl('classmess', array('op' => 'update', 'id' => $id)), 'success');
            }
        }
        include $this->template('default/class/post');
    }

    /**
     *  日签首页
     */
    public function doWebDaySign() {
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $daySignList = pdo_fetchall("select * from " . tablename('myxs_fodder_day_sign') . " where uniacid = :uniacid and sign_status > :sign_status order by `sign_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':uniacid'=>$_W['uniacid'],':sign_status'=>0));
        $daySignListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_day_sign') . " where uniacid = :uniacid and sign_status > :sign_status order by `sign_id` desc", array(':uniacid'=>$_W['uniacid'],':sign_status'=>0));
        foreach ($daySignList as $key => $value){
            $daySignList[$key]['display_time'] = date("Y-m-d",$value['display_time']);
            $daySignList[$key]['sign_img'] = toimage($value['sign_img']);
        }
        $pager = pagination($daySignListTotal, $pindex, $psize);
        include $this->template('default/daySign/index');
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    /**
     * 日签  增、删、改、查
     */
    public function doWebDaySignMess() {
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);

            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_day_sign') . " WHERE sign_id = :sign_id", array(':sign_id' => $id));
            if (empty($row)) {
                message('抱歉，日签不存在或是已经被删除！');
            }
            pdo_update('myxs_fodder_day_sign',array('sign_status'=>0),array('sign_id'=>$id));
            message('删除成功！', referer(), 'success');
        }elseif($operation=='add'){
            $sub=true;
            if($_GPC['submit']=='提交'){
                $data=array();
                if (empty($_GPC['sign_title'])){
                    message('请输入日签标题！');
                }
                if (empty($_GPC['sign_content'])){
                    message('请输入日签内容！');
                }
                $data['sign_img']=$_GPC['sign_img'];
                $data['sign_title']=$_GPC['sign_title'];
                $data['sign_content']=$_GPC['sign_content'];
                $data['sign_status']=intval($_GPC['sign_status']);
                $data['display_time']=strtotime($_GPC['time']);
                $data['create_time']=time();
                $data['uniacid']=$_W['uniacid'];
                if (empty($data['sign_title'])){
                    message('标题不能为空！', $this->createWebUrl('DaySignMess', array('op' => 'add', 'id' => $id)), 'error');
                }
                if(empty($data['sign_content'])){
                    message('内容不能为空！', $this->createWebUrl('DaySignMess', array('op' => 'add', 'id' => $id)), 'error');

                }
                if(empty($data['sign_img'])){
                    message('图片不能为空！', $this->createWebUrl('DaySignMess', array('op' => 'add', 'id' => $id)), 'error');

                }
                pdo_insert('myxs_fodder_day_sign',$data);
                message('内容添加成功！', $this->createWebUrl('daySign'), 'success');
            }

        }else{
            $sub=false;
            $daysign =pdo_fetch("select * from " . tablename('myxs_fodder_day_sign') . " where sign_id = :sign_id", array(':sign_id' => $id));
            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['sign_img']=$_GPC['sign_img'];
                $data['sign_title']=$_GPC['sign_title'];
                $data['sign_content']=$_GPC['sign_content'];
                $data['display_time']=strtotime($_GPC['time']);
                $data['sign_status']=intval($_GPC['sign_status']);
                if (empty($data['sign_title'])){
                    message('标题不能为空！', $this->createWebUrl('DaySignMess', array('op' => 'add', 'id' => $id)), 'error');
                }
                if(empty($data['sign_content'])){
                    message('内容不能为空！', $this->createWebUrl('DaySignMess', array('op' => 'add', 'id' => $id)), 'error');

                }
                if(empty($data['sign_img'])){
                    message('图片不能为空！', $this->createWebUrl('DaySignMess', array('op' => 'add', 'id' => $id)), 'error');

                }
                pdo_update('myxs_fodder_day_sign',$data,array('sign_id'=>$_GPC['id']));
                message('日签更新成功！', $this->createWebUrl('DaySignMess', array('op' => 'update', 'id' => $id)), 'success');
            }

        }
        include $this->template('default/daySign/post');
    }

    /**
     *  会员首页
     */
    public function doWebMember(){
        global $_GPC,$_W;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'main';

        $where="";
        if($operation=='search'){
            $search=trim($_GPC['searchmess']);
            if(!empty($search)){
                $where.=" and member_name like '%$search%'";
            }
        }
        $memberList = pdo_fetchall("select * from " . tablename('myxs_fodder_member') . " where uniacid = :uniacid  ".$where." order by `member_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':uniacid'=>$_W['uniacid']));
        $memberListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_member') . " where uniacid = :uniacid  ".$where."  order by `member_id` desc", array(':uniacid'=>$_W['uniacid']));
        foreach ($memberList as $key => $value){
            $memberList[$key]['create_time'] = date("Y-m-d",$value['create_time']);
            $memberList[$key]['member_head_portrait'] = toimage($value['member_head_portrait']);
            $grouping = pdo_fetch("select * from " . tablename('myxs_fodder_grouping') . " where grouping_id = :grouping_id ", array(':grouping_id'=>$value['grouping_id']));
            $memberList[$key]['grouping_id'] = $value['grouping_id'] == 0 ? '默认分组': $grouping['grouping_name'];

            $memberList[$key]['intergral'] = floatval($value['intergral']);
            $memberList[$key]['balance'] = floatval($value['balance']);


            $downAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_member')." where parent=:parent and uniacid=:uniacid ",array(':parent'=>$value['member_id'],':uniacid'=>$_W['uniacid']));
            $memberList[$key]['downOne'] = intval($downAll);
            $down = pdo_fetchall("select member_id from ".tablename('myxs_fodder_member')." where parent=:parent and uniacid=:uniacid",array(':parent'=>$value['member_id'],':uniacid'=>$_W['uniacid']));
            $ids = '';
            if(!empty($down)){
                foreach ($down as $k=>$v){
                    $ids .=$v['member_id'].',';
                }

                $ids = substr($ids,0,strlen($ids)-1);
                $downTwoAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_member')." where parent in (:parent) and uniacid=:uniacid",array(':parent'=>$ids,':uniacid'=>$_W['uniacid']));
                $memberList[$key]['downTwo'] = intval($downTwoAll);
            }else{
                $memberList[$key]['downTwo'] = 0;
            }
            $memberList[$key]['downall'] = $memberList[$key]['downOne'] + $memberList[$key]['downTwo'];
        }
        $pager = pagination($memberListTotal, $pindex, $psize);
        include $this->template('default/member/index');
    }

    /**
     *  会员编辑页面
     */
    public function doWebMemberPost(){
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_member') . " WHERE member_id = :member_id", array(':member_id' => $id));
            if (empty($row)) {
                message('抱歉，会员不存在或是已经被删除！');
            }
            pdo_delete('myxs_fodder_member',array('member_id'=>$id));
//            pdo_update('myxs_fodder_member',array('member_status'=>0),array('member_id'=>$id));
            message('删除成功！', referer(), 'success');
        }elseif($operation=="addIntergral"){

        }else{
            $sub=false;
            $member =pdo_fetch("select * from " . tablename('myxs_fodder_member') . " where member_id = :member_id", array(':member_id' => $id));
            $groupingList = pdo_fetchall("select * from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid ", array(':uniacid'=>$_W['uniacid']));

            $member_groupingList = pdo_fetchall("select * from " . tablename('myxs_fodder_grouping') . " where grouping_id in(".$member['grouping_id'].") and uniacid = :uniacid ", array(':uniacid'=>$_W['uniacid']));


            $aa = array_column($groupingList, 'grouping_id');
            foreach($member_groupingList as $k=>$v) {
                $key = array_search($v["grouping_id"],$aa);
                unset($groupingList[$key]);
            }

            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['member_name']=$_GPC['member_name'];
                $data['member_head_portrait']=$_GPC['member_head_portrait'];
                $data['member_mobile']=$_GPC['member_mobile'];
//                $data['grouping_id']=intval($_GPC['grouping_id']);
//                $data['member_is_bind']=intval($_GPC['member_is_bind']);
                $data['create_time']=strtotime($_GPC['create_time']);
                $data['is_system']=intval($_GPC['is_system']);
                if (empty($data['member_name'])){
                    message('会员昵称不能为空！', $this->createWebUrl('MemberPost', array('op' => 'update', 'id' => $id)), 'error');
                }
                if(empty($data['member_head_portrait'])){
                    message('会员头像不能为空！', $this->createWebUrl('MemberPost', array('op' => 'update', 'id' => $id)), 'error');

                }

                pdo_update('myxs_fodder_member',$data,array('member_id'=>$_GPC['id']));
                message('会员更新成功！', $this->createWebUrl('MemberPost', array('op' => 'update', 'id' => $id)), 'success');
            }
        }
        include $this->template('default/member/post');

    }

    /**
     * 获取用户下级
     */
    public function doWebGetUserDown(){
        global $_W  ,$_GPC;
        $id=intval($_GPC['member_id']);
        $type=intval($_GPC['type']);
        $start = intval($_GPC['start']);
        $end = intval($_GPC['end']);
        $limit=" limit ".$start.",".$end;
        if($type == 1){
            $down = pdo_fetchall("select member_name,member_head_portrait,parent_time,create_time from ".tablename('myxs_fodder_member')." where parent=:parent and uniacid=:uniacid ".$limit,array(':parent'=>$id,':uniacid'=>$_W['uniacid']));
            $downAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_member')." where parent=:parent and uniacid=:uniacid ",array(':parent'=>$id,':uniacid'=>$_W['uniacid']));
            if(!empty($down)){
                foreach ($down as $k=>$v){
                    $down[$k]['member_head_portrait'] = toimage($v['member_head_portrait']);
                    $down[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
                    if($v['parent_time'] > 0 ){
                        $down[$k]['parent_time'] = date('Y-m-d H:i',$v['parent_time']);
                    }else{
                        $down[$k]['parent_time'] = '无';
                    }
                }
            }
            return json_encode(array('list'=>$down,'count'=>count($down),'total'=>intval($downAll)));
        }
        if($type == 2){
            $ids = '';
            $down = pdo_fetchall("select member_id from ".tablename('myxs_fodder_member')." where parent=:parent and uniacid=:uniacid",array(':parent'=>$id,':uniacid'=>$_W['uniacid']));
            if(!empty($down)){
                foreach ($down as $k=>$v){
                    $ids .=$v['member_id'].',';
                }

                $ids = substr($ids,0,strlen($ids)-1);
                $downTwo = pdo_fetchall("select member_name,member_head_portrait,parent_time,create_time from ".tablename('myxs_fodder_member')." where parent in (:parent) and uniacid=:uniacid ".$limit,array(':parent'=>$ids,':uniacid'=>$_W['uniacid']));
                $downTwoAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_member')." where parent in (:parent) and uniacid=:uniacid",array(':parent'=>$ids,':uniacid'=>$_W['uniacid']));
                if(!empty($downTwo)){
                    foreach ($downTwo as $ks=>$vs){
                        $downTwo[$ks]['member_head_portrait'] = toimage($vs['member_head_portrait']);
                        $downTwo[$ks]['create_time'] = date('Y-m-d H:i',$vs['create_time']);
                        if($v['parent_time'] > 0 ){
                            $downTwo[$ks]['parent_time'] = date('Y-m-d H:i',$vs['parent_time']);
                        }else{
                            $downTwo[$ks]['parent_time'] = '无';
                        }
                    }
                }
                return json_encode(array('list'=>$downTwo,'count'=>count($downTwo),'total'=>intval($downTwoAll)));
            }
        }
        if($type == 3){
            $logs = pdo_fetchall("select member_id,text,type,add_time,amount from ".tablename('myxs_fodder_member_intergral_log')." where member_id=:member_id and uniacid=:uniacid order by add_time  desc ".$limit,array(':member_id'=>$id,':uniacid'=>$_W['uniacid']));
            $logsAll = pdo_fetchcolumn("select count(*) from ".tablename('myxs_fodder_member_intergral_log')." where member_id=:member_id and uniacid=:uniacid ",array(':member_id'=>$id,':uniacid'=>$_W['uniacid']));
            if(!empty($logs)) {
                foreach ($logs as $k => $v) {
                    $member =pdo_fetch("select member_head_portrait,member_name from " . tablename('myxs_fodder_member') . " where uniacid = :uniacid and member_id = :member_id", array(':uniacid' => $_W['uniacid'],':member_id'=>$id));
                    $logs[$k]['member_head_portrait'] = toimage($member['member_head_portrait']);
                    $logs[$k]['member_name'] = $member['member_name'];
                    $logs[$k]['create_time'] = date('Y-m-d H:i', $v['add_time']);
                    if($v['type'] == 1){
                        $logs[$k]['amount'] = '-'.$v['amount'];
                    }else{
                        $logs[$k]['amount'] = '+'.$v['amount'];
                    }

                }
                return json_encode(array('list' => $logs, 'count' => count($logs), 'total' => intval($logsAll)));
            }
        }
        if($type == 4){
            return json_encode(array('list' => '', 'count' => 0, 'total' => 0));
        }

    }
    public function doWebUpdateUserIntergral(){
        global $_W  ,$_GPC;
        $id=intval($_GPC['member_id']);
        $total=$_GPC['total'];
        $remark=$_GPC['remark'];

        $member =pdo_fetch("select * from " . tablename('myxs_fodder_member') . " where member_id = :member_id", array(':member_id' => $id));

        $data=array();
        $data['intergral']=$member['intergral']+$total;

        $state=pdo_update('myxs_fodder_member',$data,array('member_id'=>$id));
        if($state){
            if($total>0){
                $type=2;
            }else{
                $type=1;
            }
            $intergral_log_data = array(
                'uniacid' => $member['uniacid'],
                'member_id' => $id,
                'text' => '后台变更',
                'type' => $type,
                'amount' => abs($total),
                'add_time' => time(),
                'operation' => 'cz');
            pdo_insert('myxs_fodder_member_intergral_log', $intergral_log_data);
            $admin_intergral_log=array(
                'uniacid' => $member['uniacid'],
                'member_id' => $id,
                'inter_type' => $type,
                'inter_count' => abs($total),
                'log_time' => time(),
                'admin_id'=>intval($_W['user']['uid'])
            );
            pdo_insert('myxs_fodder_admin_intergral_log', $admin_intergral_log);
        }
        echo 1;
    }

    /**
     *  会员分组编辑
     */
    public function doWebMemberEdit(){
        global $_W  ,$_GPC;
        $id = $_GPC['id'];
        $member_id = $_GPC['member_id'];
        $member =pdo_fetch("select * from " . tablename('myxs_fodder_member') . " where member_id = :member_id", array(':member_id' => $member_id));

        $member_grouping = explode(',',$member['grouping_id']);
        $key = array_search($id,$member_grouping);

        if($key){
            unset($member_grouping[$key]);
        }else{
            $member_grouping[] = $id;
        }

        $update_member = array();
        $update_member['grouping_id'] = implode(',',$member_grouping);

        $i = pdo_update('myxs_fodder_member',$update_member,array('member_id'=>$member['member_id']));
        if ($i){
            echo 1;
        }
    }

    /**
     *
     * 根据昵称查询会员
     *
     */
    public function doWebGetUserToNickName(){
        global $_W  ,$_GPC;
        $nickname=$_GPC['nickName'];
        $member =pdo_fetchall("select * from " . tablename('myxs_fodder_member') . " where uniacid = :uniacid and member_name like :member_name", array(':uniacid' => $_W['uniacid'],':member_name'=>"%".$nickname."%"));
        return json_encode($member);
    }
    /**
     *  回收站
     */
    public function doWebRecycle(){
        global $_W  ,$_GPC;
        $operation = $_GPC['op'];
        $type = $_GPC['type'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
       if ($operation == 'content'){
           if ($type == 'recovery'){
               pdo_update('myxs_fodder_content',array('content_status' => 1 ),array('content_id'=>$_GPC['id']));
               message('从回收站恢复内容成功！', $this->createWebUrl('Recycle', array('op' => 'content')), 'success');
           }elseif ($type == 'delete'){
               pdo_delete('myxs_fodder_content', array('content_id'=>$_GPC['id']));
               message('从回收站删除内容成功！', $this->createWebUrl('Recycle', array('op' => 'content')), 'success');
           }else{
               $contentList = pdo_fetchall("select * from " . tablename('myxs_fodder_content') . " where  content_status = :content_status and content_class = :content_class and uniacid = :uniacid  order by `content_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':content_status'=>0,':content_class'=>1,':uniacid'=>$_W['uniacid']));
               $class_status = array('删除','显示','隐藏');
               foreach ($contentList as $key =>$value){
                   $Class = pdo_fetch("select * from " . tablename('myxs_fodder_class') . " where class_id = :class_id", array(':class_id' => $value['class_id']));
                   $contentList[$key]['class_id'] = $Class['class_name'];
                   $contentList[$key]['content_status'] = $class_status[$value['content_status']];
                   $contentList[$key]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);

               }
               $contentListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_content') . " where content_class = :content_class and uniacid = :uniacid order by `content_id` desc", array(':content_class'=>1,':uniacid'=>$_W['uniacid']));
               $pager = pagination($contentListTotal, $pindex, $psize);
           }
           include $this->template('default/content/recycle');
       }
    }

    /**
     *  分组设置首页
     */
    public function doWebGrouping(){
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $groupingList = pdo_fetchall("select * from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid order by `grouping_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':uniacid'=>$_W['uniacid']));
//        $class_status = array('1'=>'启用','2'=>'删除');
        foreach ($groupingList as $key =>$value){
            $admin_name=pdo_fetch("select member_name from ".tablename('myxs_fodder_member')." where member_id=:member_id",array(':member_id'=>$value['admin_id']));
//            $Class = pdo_fetch("select * from " . tablename('myxs_fodder_class') . " where class_id = :class_id", array(':class_id' => $value['class_id']));
//            $classList[$key]['class_status'] = $class_status[$value['class_status']];
            $groupingList[$key]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
            if(empty($admin_name)){
                $groupingList[$key]['grouping_admin']="无";
            }else{
                $groupingList[$key]['grouping_admin']=$admin_name['member_name'];
            }
        }
        $groupingListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_grouping') . " where uniacid = :uniacid order by `grouping_id` desc", array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($groupingListTotal, $pindex, $psize);

        include $this->template('default/grouping/index');
    }

    /**
     *  分组设置 增 卅 该 查
     */
    public function doWebGroupingPost(){
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $member_id=intval($_W['user']['uid']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        $grouping_passwd =pdo_fetch("select * from " . tablename('myxs_fodder_grouping') . " where grouping_passwd = :grouping_passwd and uniacid = :uniacid", array(':grouping_passwd' => trim($_GPC['grouping_passwd']),':uniacid'=>$_W['uniacid']));

        if($operation=='delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_grouping') . " WHERE grouping_id = :grouping_id", array(':grouping_id' => $id));
            if (empty($row)) {
                message('抱歉，用户分组不存在或是已经被删除！');
            }
            pdo_delete('myxs_fodder_grouping', array('grouping_id'=>$id));
            message('删除用户分组成功！', referer(), 'success');
        }elseif($operation=='add'){
            if($_GPC['op']=='add'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                if(empty($_GPC['grouping_name'])){
                    message('请输入分组名称！');
                }
                if(empty($_GPC['grouping_passwd'])){
                    message('请输入分组邀请码！');
                }
                if (!empty($grouping_passwd)){
                    message('输入分组邀请码重复！');
                }
                $data=array();
                $data['grouping_name']=$_GPC['grouping_name'];
                $data['grouping_passwd']=trim($_GPC['grouping_passwd']);
                $data['admin_id']=intval($_GPC['admin_id']);
                $data['create_time']=time();
                $data['uniacid']=$_W['uniacid'];
                $data['update_time']=time();
                $data['update_member_id']=$member_id;
                pdo_insert('myxs_fodder_grouping',$data);
                $insertId=pdo_insertid();

                $userMess=pdo_fetch("select grouping_id from ".tablename('myxs_fodder_member')." where member_id=:member_id and uniacid=:uniacid",array(':member_id'=>intval($_GPC['admin_id']),':uniacid'=>$_W['uniacid']));
                $grouping = explode(',',$userMess['grouping_id']);
                $grouping[]= $insertId;
                $update_grouping_id = implode(',',$grouping);

                if(intval($_GPC['admin_id'])!=0){
                    pdo_update('myxs_fodder_member',array('is_class_admin'=>1,'grouping_id'=>$update_grouping_id),array('member_id'=>intval($_GPC['admin_id'])));
                }
                message('分组添加成功！', $this->createWebUrl('grouping'), 'success');
            }
        }else{
            $sub=false;
            $content =pdo_fetch("select * from " . tablename('myxs_fodder_grouping') . " where grouping_id = :grouping_id", array(':grouping_id' => $id));

            $admin_name=pdo_fetch("select member_name from ".tablename('myxs_fodder_member')." where member_id=:member_id",array(':member_id'=>$content['admin_id']));

            $content['grouping_admin']=$admin_name['member_name'];
            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $oldAdmin =pdo_fetch("select admin_id from " . tablename('myxs_fodder_grouping') . " where grouping_id = :grouping_id", array(':grouping_id' => $id));

                $data=array();
                $data['grouping_name']=$_GPC['grouping_name'];
                $data['grouping_passwd']=trim($_GPC['grouping_passwd']);
                $data['admin_id']=intval($_GPC['admin_id']);
                $data['create_time']=time();
                $data['update_time']=time();
                $data['update_member_id']=$member_id;
                pdo_update('myxs_fodder_grouping',$data,array('grouping_id'=>$_GPC['id']));

                $oldAdminGroup =pdo_fetchall("select * from " . tablename('myxs_fodder_grouping') . " where admin_id = :admin_id", array(':admin_id' => $oldAdmin['admin_id']));
                if(empty($oldAdminGroup)){
                    pdo_update('myxs_fodder_member', array('is_class_admin' => 0), array('member_id' => $oldAdmin['admin_id']));
                }

                $userMess=pdo_fetch("select grouping_id from ".tablename('myxs_fodder_member')." where member_id=:member_id and uniacid=:uniacid",array(':member_id'=>intval($_GPC['admin_id']),':uniacid'=>$_W['uniacid']));
                $grouping = explode(',',$userMess['grouping_id']);
                if(intval($_GPC['admin_id'])!=0) {
                    if (!in_array($_GPC['id'],$grouping)){
                        $grouping[]= $_GPC['id'];
                        $update_grouping_id = implode(',',$grouping);
                        pdo_update('myxs_fodder_member', array('is_class_admin' => 1,'grouping_id'=>$update_grouping_id), array('member_id' => intval($_GPC['admin_id'])));

                    }else{
                        pdo_update('myxs_fodder_member', array('is_class_admin' => 1), array('member_id' => intval($_GPC['admin_id'])));
                    }
                }
                message('分组更新成功！', $this->createWebUrl('GroupingPost', array('op' => 'update', 'id' => $id)), 'success');
            }
        }
        include $this->template('default/grouping/post');
    }

    /**
     *  广告模块
     */
    public function doWebAdvert(){
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $advertClass = array(1=>'流量主广告',2=>'平台广告',3=>'用户广告');
        $advertPosition = array('member'=>'会员中心','content'=>'广场','member_set'=>'会员设置','intergral'=>'我的积分');

        $advertClassTu = array('video'=>'视频广告','image'=>'图片广告');
        $advertClassFlow = array('banner'=>'banner广告','incentive_video'=>'激励视频广告','plaque'=>'插屏广告','flow_video'=>'视频广告','patch_video'=>'视频贴片广告','grid_video'=>'格子广告');

        $advertList = pdo_fetchall("select * from " . tablename('myxs_fodder_advert') . " where uniacid = :uniacid order by `advert_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':uniacid'=>$_W['uniacid']));

        foreach ($advertList as $key =>$value){
            $advertList[$key]['advert_class'] = $advertClass[$value['advert_class']];
            if($value['advert_class'] == 1){
                $advertList[$key]['advert_class_type'] = $advertClassFlow[$value['advert_class_type']];
            }else{
                $advertList[$key]['advert_class_type'] = $advertClassTu[$value['advert_class_type']];
            }
            $advertList[$key]['advert_position'] = $advertPosition[$value['advert_position']];
            $advertList[$key]['create_time'] = date("Y-m-d H:i:s",$value['create_time']);
        }
        $advertListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_advert') . " where uniacid = :uniacid order by `advert_id` desc", array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($advertListTotal, $pindex, $psize);

        include $this->template('default/advert/index');
    }

    public function doWebAdvertPost(){
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $advertPosition = array('member'=>'会员中心','content'=>'广场','member_set'=>'会员设置','intergral'=>'我的积分');

        $advertClass = array(2=>'平台',1=>'流量主',3=>'用户');
        $advertClassTu = array('video'=>'视频','image'=>'图片广告');
        $advertClassFlow = array('banner'=>'banner广告','incentive_video'=>'激励视频广告','plaque'=>'插屏广告','flow_video'=>'视频广告','patch_video'=>'视频贴片广告','grid_video'=>'格子广告');

        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_advert') . " WHERE advert_id = :advert_id", array(':advert_id' => $id));
            if (empty($row)) {
                message('抱歉，广告不存在或是已经被删除！');
            }
            pdo_delete('myxs_fodder_advert', array('advert_id'=>$id));
            message('删除广告成功！', referer(), 'success');
        }elseif($operation=='add'){
            if($_GPC['op']=='add'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                if(empty($_GPC['advert_class_type'])){
                    message('请选择广告类型！');
                }
                if($_GPC['advert_class'] != 1){
                    if(empty($_GPC['advert_video']) && $_GPC['advert_class_type'] == 'video'){
                        message('请选择上传视频！');
                    }
                    if(empty($_GPC['advert_image']) && $_GPC['advert_class_type'] == 'image'){
                        message('请选择上传图片！');
                    }
                }else{
                    if(empty($_GPC['advert_text'])){
                        message('请输入广告位ID！');
                    }
                }
                if(empty($_GPC['advert_name'])){
                    message('请输入广告名称！');
                }

                $data=array();
                $data['advert_name']=$_GPC['advert_name'];
                $data['advert_class']=intval($_GPC['advert_class']);
                $data['advert_class_type']=trim($_GPC['advert_class_type']);
                $data['advert_times']=intval($_GPC['advert_times']);
                if($_GPC['advert_class'] != 1){
                    if($_GPC['advert_class_type'] == 'video'){
                        $data['advert_video']=trim($_GPC['advert_video']);
                    }
                    if($_GPC['advert_class_type'] == 'image'){
                        $data['advert_image']=serialize($_GPC['advert_image']);
                    }
                }else{
                    $data['advert_text']=trim($_GPC['advert_text']);
                }
                $data['advert_position']=trim($_GPC['advert_position']);
                $data['create_time']=time();
                $data['uniacid']=$_W['uniacid'];
                $insertid=pdo_insert('myxs_fodder_advert',$data);
                message('添加广告成功！', $this->createWebUrl('Advert'), 'success');
            }
        }else{
            $sub=false;
            $content =pdo_fetch("select * from " . tablename('myxs_fodder_advert') . " where advert_id = :advert_id", array(':advert_id' => $id));
            if(!empty($content)){
                $content['advert_image'] = unserialize($content['advert_image']);
            }
            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                if($_GPC['advert_class'] == 1){
                    $type= $advertClassFlow[$_GPC['advert_class_type']];
                    if(empty($type)){
                        message('请选择广告类型！');
                    }
                    if(empty($_GPC['advert_text'])){
                        message('请输入广告位ID！');
                    }
                }else{
                    $type= $advertClassTu[$_GPC['advert_class_type']];
                    if(empty($type)){
                        message('请选择广告类型！');
                    }
                    if(empty($_GPC['advert_video']) && $_GPC['advert_class_type'] == 'video'){
                        message('请选择上传视频！');
                    }
                    if(empty($_GPC['advert_image']) && $_GPC['advert_class_type'] == 'image'){
                        message('请选择上传图片！');
                    }
                }

                $data=array();
                $data['advert_name']=$_GPC['advert_name'];
                $data['advert_position']=trim($_GPC['advert_position']);
                $data['advert_class']=intval($_GPC['advert_class']);
                $data['advert_class_type']=trim($_GPC['advert_class_type']);
                $data['advert_times']=intval($_GPC['advert_times']);
                if($_GPC['advert_class'] != 1){
                    if($_GPC['advert_class_type'] == 'video'){
                        $data['advert_video']=trim($_GPC['advert_video']);
                        $data['advert_image'] = '';
                    }
                    if($_GPC['advert_class_type'] == 'image'){
                        $data['advert_image']=serialize($_GPC['advert_image']);
                        $data['advert_video'] = '';
                    }
                }else{
                    $data['advert_text']=trim($_GPC['advert_text']);
                }
                $data['create_time']=time();
                pdo_update('myxs_fodder_advert',$data,array('advert_id'=>$_GPC['id']));
                message('更新广告成功！', $this->createWebUrl('AdvertPost', array('op' => 'update', 'id' => $id)), 'success');
            }
        }
        include $this->template('default/advert/post');
    }

    /**
     * 轮播首页
     */
    public function doWebShuffling(){
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $shufflingPosition = array('member'=>'会员中心','content'=>'广场','member_set'=>'会员设置','found'=>'发现页');
        $shufflingStatus=array('1'=>'显示','2'=>'隐藏','3'=>'删除');

        $shufflingList = pdo_fetchall("select * from " . tablename('myxs_fodder_shuffling') . " where uniacid = :uniacid order by `shuffling_id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':uniacid'=>$_W['uniacid']));

        foreach ($shufflingList as $key =>$value){
            $shufflingList[$key]['shuffling_position'] = $shufflingPosition[$value['shuffling_position']];
            $shufflingList[$key]['shuffling_status'] = $shufflingStatus[$value['shuffling_status']];
            $shufflingList[$key]['shuffling_add_time'] = date("Y-m-d H:i:s",$value['shuffling_add_time']);
            $shufflingList[$key]['shuffling_update_time'] = date("Y-m-d H:i:s",$value['shuffling_update_time']);
        }
        $shufflingListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_shuffling') . " where uniacid = :uniacid order by `shuffling_id` desc", array(':uniacid'=>$_W['uniacid']));
        $pager = pagination($shufflingListTotal, $pindex, $psize);


        include $this->template('default/shuffling/index');
    }

    /**
     * 轮播添加页
     */
    public function doWebShufflingPost(){
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $shufflingPosition = array('member'=>'会员中心','content'=>'广场','member_set'=>'会员设置','found'=>'发现页');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_shuffling') . " WHERE shuffling_id = :shuffling_id", array(':shuffling_id' => $id));
            if (empty($row)) {
                message('抱歉，轮播不存在或是已经被删除！');
            }
            pdo_delete('myxs_fodder_shuffling', array('shuffling_id'=>$id));
            message('删除轮播成功！', referer(), 'success');
        }elseif($operation=='add'){
            if($_GPC['op']=='add'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['shuffling_content']=json_encode($_GPC['thumbs']);
                $data['shuffling_position']=trim($_GPC['shuffling_position']);
                $data['shuffling_add_time']=time();
                $data['shuffling_update_time']=time();
                $data['uniacid']=$_W['uniacid'];
                $data['shuffling_status']=intval($_GPC['shuffling_status']);
                if ($data['shuffling_content']=="null"){
                    message('图片附件不能为空！', $this->createWebUrl('ShufflingPost', array('op' => 'add', 'id' => $id)), 'error');
                }
                if(count($_GPC['thumbs'])>3){
                    message('最多上传三张图片！', $this->createWebUrl('ShufflingPost', array('op' => 'add', 'id' => $id)), 'error');
                }
                $shuffling_content = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_shuffling') . " WHERE shuffling_position = :shuffling_position", array(':shuffling_position' => $data['shuffling_position']));
                if(empty($shuffling_content)){
                    $insertid=pdo_insert('myxs_fodder_shuffling',$data);
                    message('添加轮播成功！', $this->createWebUrl('Shuffling'), 'success');
                }else{
                    $is_no=array();
                    $is_no['shuffling_content']=json_encode($_GPC['thumbs']);
                    $is_no['shuffling_position']=trim($_GPC['shuffling_position']);
                    $is_no['shuffling_update_time']=time();
                    $is_no['shuffling_status']=intval($_GPC['shuffling_status']);
                    pdo_update('myxs_fodder_shuffling',$is_no,array('shuffling_id'=>$shuffling_content['shuffling_id']));
                    message('替换当前轮播成功！', $this->createWebUrl('Shuffling'), 'success');
                }

            }
        }else{
            $sub=false;
            $content =pdo_fetch("select * from " . tablename('myxs_fodder_shuffling') . " where shuffling_id = :shuffling_id", array(':shuffling_id' => $id));
            $contentimg=json_decode($content['shuffling_content'],true);
            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['shuffling_content']=json_encode($_GPC['thumbs']);
                $data['shuffling_position']=trim($_GPC['shuffling_position']);
                $data['shuffling_update_time']=time();
                $data['shuffling_status']=intval($_GPC['shuffling_status']);
                pdo_update('myxs_fodder_shuffling',$data,array('shuffling_id'=>$_GPC['id']));
                message('更新轮播成功！', $this->createWebUrl('ShufflingPost', array('op' => 'update', 'id' => $id)), 'success');
            }
        }
        include $this->template('default/shuffling/post');
    }

    /**
     * 发现管理  lx add  弃用  页面及方法暂时保留
     */
    public function doWebFound() {
        global $_GPC,$_W;
        $titleList = array(
            'shuffling' => '轮播设置',
        );
        $class = empty($_GPC['class']) ? 'shuffling' : $_GPC['class'];
        if ($class == 'shuffling'){
            $shuffling = pdo_fetch("select * from " . tablename('myxs_fodder_found_shuffling') . " where uniacid = :uniacid", array(':uniacid'=>$_W['uniacid']));
            $shuffling['shuffling'] = unserialize($shuffling['shuffling']);
            if(!empty($shuffling['shuffling'])){
                foreach ($shuffling['shuffling'] as &$row){
                    $row = toimage($row);
                }
            }
            if($_GPC['submit']=='提交') {
                $data= array();
                $data['uniacid'] = $_W['uniacid'];
                $data['shuffling'] = serialize($_GPC['found_img']);
                if (empty($shuffling)) {
                    pdo_insert('myxs_fodder_found_shuffling', $data);
                    message('添加轮播成功', $this->createWebUrl('found', array('class' => 'shuffling')), 'success');
                } else {
                    pdo_update('myxs_fodder_found_shuffling', $data, array('id' => $shuffling['id']));
                    message('编辑轮播成功', $this->createWebUrl('found', array('class' => 'shuffling')), 'success');
                }
            }
            include $this->template('default/found/index');
        }
    }

    /**
     *  公告管理
     */
    public function doWebNotice() {
        global $_W  ,$_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $noticeList = pdo_fetchall("select * from " . tablename('myxs_fodder_notice') . " where uniacid = :uniacid and notice_status > :notice_status order by `id` desc LIMIT ".($pindex - 1) * $psize . "," . $psize, array(':uniacid'=>$_W['uniacid'],':notice_status'=>0));
        $noticeListTotal = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_notice') . " where uniacid = :uniacid and notice_status > :notice_status order by `id` desc", array(':uniacid'=>$_W['uniacid'],':notice_status'=>0));
        foreach ($noticeList as $key => $value){
            $noticeList[$key]['notice_time'] = date("Y-m-d H:i:s",$value['notice_time']);
            $noticeList[$key]['notice_content'] = unserialize($value['notice_content']);
        }
        $pager = pagination($noticeListTotal, $pindex, $psize);
        include $this->template('default/notice/index');
        //这个操作被定义用来呈现 管理中心导航菜单
    }
    /**
     * 公告  增、删、改、查
     */
    public function doWebNoticeMess() {
        global $_W  ,$_GPC;
        $id=intval($_GPC['id']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'look';
        if($operation=='delete'){
            $id = intval($_GPC['id']);

            $row = pdo_fetch("SELECT * FROM " . tablename('myxs_fodder_notice') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，公告不存在或是已经被删除！');
            }
            pdo_update('myxs_fodder_notice',array('notice_status'=>0),array('id'=>$id));
            message('删除成功！', referer(), 'success');
        }elseif($operation=='add'){
            $sub=true;
            if($_GPC['submit']=='提交'){
                $data=array();
                if (empty($_GPC['notice_title'])){
                    message('请输入公告标题！');
                }
                if (empty($_GPC['notice_content'])){
                    message('请输入公告内容！');
                }
                $data['notice_title']=$_GPC['notice_title'];
                $data['notice_content']=serialize($_GPC['notice_content']);
                $data['notice_time']=time();
                $data['uniacid']=$_W['uniacid'];
                if (empty($data['notice_title'])){
                    message('标题不能为空！', $this->createWebUrl('NoticeMess', array('op' => 'add', 'id' => $id)), 'error');
                }
                if(empty($data['notice_content'])){
                    message('内容不能为空！', $this->createWebUrl('NoticeMess', array('op' => 'add', 'id' => $id)), 'error');

                }
                pdo_insert('myxs_fodder_notice',$data);
                message('内容添加成功！', $this->createWebUrl('notice'), 'success');
            }

        }else{
            $sub=false;
            $notice =pdo_fetch("select * from " . tablename('myxs_fodder_notice') . " where id = :id", array(':sign_id' => $id));
            if($_GPC['op']=='update'){
                $sub=true;
            }
            if($_GPC['submit']=='提交'){
                $data=array();
                $data['notice_title']=$_GPC['notice_title'];
                $data['notice_content']=serialize($_GPC['notice_content']);
                $data['notice_time']=time();
                if (empty($data['notice_title'])){
                    message('标题不能为空！', $this->createWebUrl('NoticeMess', array('op' => 'add', 'id' => $id)), 'error');
                }
                if(empty($data['notice_content'])){
                    message('内容不能为空！', $this->createWebUrl('NoticeMess', array('op' => 'add', 'id' => $id)), 'error');

                }
                pdo_update('myxs_fodder_notice',$data,array('id'=>$_GPC['id']));
                message('日签更新成功！', $this->createWebUrl('NoticeMess', array('op' => 'update', 'id' => $id)), 'success');
            }

        }
        include $this->template('default/notice/post');
    }

    public function doWebCommunity(){
        global $_GPC,$_W;
        $titleList = array(
            'class_list' => '分类列表',
            'add_class'=>'添加分类',
            'community_list'=>'社群列表',
        );
        $class = empty($_GPC['class']) ? 'class_list' : $_GPC['class'];

        if ($class == 'class_list'){
            $communityClass = pdo_fetchall("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid", array(':uniacid'=>$_W['uniacid']));
            include $this->template('default/community/index');
        }
        if($class=='add_class'){
            $id = intval($_GPC['id']);
            $content = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$id));
            if($_GPC['submit']=='提交') {
                $systemData = array();
                if ($_GPC['class_name']==""){
                    message('请输入分类名称！', $this->createWebUrl('Community', array('class' => 'add_class')), 'error');
                }
                $systemData['class_name'] = $_GPC['class_name'];
                $systemData['uniacid'] = $_W['uniacid'];
                if ($id) {
                    pdo_update('myxs_fodder_community_class', $systemData, array('id' => $id));
                    message('编辑社群分类成功', $this->createWebUrl('Community', array('class' => 'class_list')), 'success');
                } else {
                    pdo_insert('myxs_fodder_community_class', $systemData);
                    message('添加社群分类成功', $this->createWebUrl('Community', array('class' => 'class_list')), 'success');
                }
            }
            include $this->template('default/community/post');

        }
        if($class == 'delete'){
            $id = intval($_GPC['id']);
            $content = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$id));
            if (empty($content)) {
                message('抱歉，社群分类不存在或是已经被删除！');
            }
            pdo_delete('myxs_fodder_community_class', array('id'=>$id));
            message('删除社群分类成功！', referer(), 'success');
        }
        if($class == 'community_list'){
            $system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'system'));
            $system_content = json_decode($system['system_content'],true);
            $system_system = json_decode($system['system'],true);
            foreach ($system_content as $key =>$value){
                if(strpos(toimage($value),'http://') !== false){
                    $system_content[$key] = str_ireplace("http://","https://",toimage($value));
                }else{
                    $system_content[$key] = toimage($value);
                }
            }

            $id = intval($_GPC['id']);

            if($id){
                $communityClass = pdo_fetchall("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid", array(':uniacid'=>$_W['uniacid']));

                $community = pdo_fetch("select * from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$id));
                if(!empty($community)){
                    $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$community['group_class']));
                    $community['class'] = $class['class_name'];
//                    $community['group_logo'] = toimage($community['group_logo']);
                    $member =pdo_fetch("select member_name,member_id,member_head_portrait from " . tablename('myxs_fodder_member') . " where member_id = :member_id", array(':member_id' => $community['group_user']));

                    if (empty($member)){
                        $community['member_name'] = $system_system['member_name'];
                        $community['member_head_portrait'] = $system_content['logo_bg'];
                    }else{
                        $community['member_name'] = $member['member_name'];
                        if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
                            $community['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
                        }else{
                            $community['member_head_portrait'] = toimage($member['member_head_portrait']);
                        }
                    }

                    $community['group_create_time'] = date('Y-m-d H:i:s',$community['group_create_time']);

                    if($community['group_status'] == 0){
                        $community['group_status_mess'] = '未审核';
                    }else{
                        $community['group_status_mess'] = '已审核';
                    }

                    $community['group_num'] = $this->getCommunityContentNum($community['id']);
                }

                if($_GPC['submit']=='提交') {
                    $group_class = intval($_GPC['group_class']);
                    $group_name = trim($_GPC['group_name']);
                    $group_logo = $_GPC['group_logo'];
                    $group_message = $_GPC['group_message'];
                    $group_user_wx = $_GPC['group_user_wx'];

                    if(strpos($group_logo,'upload/')!==false){
                        $group_logo = strstr($group_logo,'upload/');
                    }

                    $data = array();
                    $data['group_class'] = $group_class;
                    $data['group_name'] = $group_name;
                    $data['group_logo'] = $group_logo;
                    $data['group_message'] = $group_message;
                    $data['group_user_wx'] = $group_user_wx;
                    pdo_update('myxs_fodder_community',$data,array('id'=>$community['id'],'uniacid'=>$_W['uniacid']));
                    message('编辑社群成功', $this->createWebUrl('Community', array('class' => 'community_list','id'=>$community['id'])), 'success');
                }
                include $this->template('default/community/update_list');
            }else{
                $community = pdo_fetchall("select * from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid order by group_class desc,group_status asc", array(':uniacid'=>$_W['uniacid']));
                if(!empty($community)){
                    foreach ($community as $k=>$v){
                        $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$v['group_class']));
                        $community[$k]['class'] = $class['class_name'];
                        $community[$k]['group_logo'] = toimage($v['group_logo']);
                        $member =pdo_fetch("select member_name,member_id,member_head_portrait from " . tablename('myxs_fodder_member') . " where member_id = :member_id", array(':member_id' => $v['group_user']));

                        if (empty($member)){
                            $community[$k]['member_name'] = $system_system['member_name'];
                            $community[$k]['member_head_portrait'] = $system_content['logo_bg'];
                        }else{
                            $community[$k]['member_name'] = $member['member_name'];
                            if(strpos(toimage($member['member_head_portrait']),'http://') !== false){
                                $community[$k]['member_head_portrait'] = str_ireplace("http://","https://",toimage($member['member_head_portrait']));
                            }else{
                                $community[$k]['member_head_portrait'] = toimage($member['member_head_portrait']);
                            }
                        }

                        $community[$k]['group_create_time'] = date('Y-m-d H:i:s',$v['group_create_time']);

                        if($v['group_status'] == 0){
                            $community[$k]['group_status_mess'] = '未审核';
                        }else{
                            $community[$k]['group_status_mess'] = '已审核';
                        }

                        $community[$k]['group_num'] = $this->getCommunityContentNum($v['id']);


                    }
                }
                include $this->template('default/community/list');
            }




        }
        if($class == 'audit'){
            $id = intval($_GPC['id']);
            $content = pdo_fetch("select id,group_status from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$id));
            if (empty($content)) {
                message('抱歉，社群分类不存在或是已经被删除！');
            }
            if($content['group_status'] == 1){
                message('该社群已审核通过！');
            }
            pdo_update('myxs_fodder_community',array('group_status'=>1),array('id'=>$id,'uniacid'=>$_W['uniacid']));

            message('审核成功！', referer(), 'success');
        }
    }
    public function getCommunityContentNum($id){
        global $_W, $_GPC;
        $num = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_content') . " where  uniacid = :uniacid  and circle_id = :circle_id", array(':uniacid'=>$_W['uniacid'],':circle_id'=>$id));
        return intval($num);

    }
}