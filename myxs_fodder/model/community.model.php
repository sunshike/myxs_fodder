<?php
class CommunityModelClass {

    /**
     * @param $data
     * 添加社群
     */
     public function addCommunity($data){
         global $_W, $_GPC;
         try {
             pdo_begin();
             pdo_insert('myxs_fodder_community',$data);
             $id = pdo_insertid();
             if(!$id){
                 throw new PDOException("创建失败！");
             }

             $contentList = pdo_fetchall("select content_id from " . tablename('myxs_fodder_content') . " where content_status > :content_status and content_class = :content_class and uniacid = :uniacid  and member_id = :member_id and circle_id = 0", array(':content_status' => 0,':content_class'=>1,':member_id'=>$data['group_user'],':uniacid'=>$_W['uniacid']));
             if(!empty($contentList)){
                 foreach ($contentList as $k=>$v){
                     pdo_update('myxs_fodder_content',array('circle_id'=>$id),array('content_id'=>$v['content_id'],'uniacid'=>$_W['uniacid']));
                 }
             }

             $data_log = array();
             $data_log['uniacid'] = $_W['uniacid'];
             $data_log['group_id'] = $id;
             $data_log['member_id'] = $data['group_user'];
             $data_log['join_time'] = time();
             pdo_insert('myxs_fodder_community_log',$data_log);
             $id = pdo_insertid();
             if(!$id){
                 throw new PDOException("添加记录失败！");
             }

             pdo_commit();
             $this->result(0, '', array('status'=>1,'msg'=>'创建成功，请等待审核'));
         } catch (PDOException $exception) {
             $this->result(0, '', array('status'=>0,'msg'=>'创建失败'));
             pdo_rollback();
         }
     }

    /**
     * 修改社群
     */
    public function updateCommunity($data,$group_id){
        global $_W, $_GPC;
        try {
            pdo_begin();
            $status= pdo_update('myxs_fodder_community',$data,array('id'=>$group_id,'uniacid'=>$_W['uniacid']));
            if(!$status){
                throw new PDOException("修改失败！");
            }
            pdo_commit();
            $this->result(0, '', array('status'=>1,'msg'=>'修改成功，请等待审核'));
        } catch (PDOException $exception) {
            $this->result(0, '', array('status'=>0,'msg'=>'修改失败'));
            pdo_rollback();
        }
    }
    /**
     * @param $data_log
     * 加入社群
     */
     public function joinCommunity($data_log){
         global $_W, $_GPC;
         try {
             pdo_begin();
             pdo_insert('myxs_fodder_community_log',$data_log);
             $id = pdo_insertid();
             if(!$id){
                 throw new PDOException("加入失败！");
             }

             $contentList = pdo_fetchall("select content_id from " . tablename('myxs_fodder_content') . " where content_status > :content_status and content_class = :content_class and uniacid = :uniacid  and member_id = :member_id and circle_id = 0", array(':content_status' => 0,':content_class'=>1,':member_id'=>$data_log['member_id'],':uniacid'=>$_W['uniacid']));
             if(!empty($contentList)){
                 foreach ($contentList as $k=>$v){
                     pdo_update('myxs_fodder_content',array('circle_id'=>$data_log['group_id']),array('content_id'=>$v['content_id'],'uniacid'=>$_W['uniacid']));
                 }
             }

             $community = pdo_fetch("select * from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$data_log['group_id']));

             $group_number = $community['group_number'] + 1;
             pdo_update('myxs_fodder_community',array('group_number'=>$group_number),array('uniacid'=>$_W['uniacid'],'id'=>$community['id']));


             pdo_commit();
             $this->result(0, '', array('status'=>1,'msg'=>'加入成功'));
         } catch (PDOException $exception) {
             $this->result(0, '', array('status'=>0,'msg'=>'加入失败'));
             pdo_rollback();
         }
     }

    /**
     * 获取社群分类
     */
     public function getCommunityClass(){
         global $_W, $_GPC;
         $system = pdo_fetch("select system_content,system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'system'));
         $system_basic = json_decode($system['system'],true);

         $class = pdo_fetchall("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid", array(':uniacid'=>$_W['uniacid']));
         $this->result(0, '',array('class'=>$class,'tip'=>$system_basic['communityTip']));
     }

    /**
     * @param $start
     * @param $end
     * @param $location
     * @param $class
     * @param $order
     * @param $type
     * type=0 所有社群  =1 我发布的社群
     * 获取社群列表
     */
     public function getCommunity($start,$end,$location,$class,$order,$type,$user_id,$search){
         global $_W, $_GPC;
         $member = $this->getUserInfo('');

         //$type  0所有社群  1我的发布 2指定人发布
         $where = '';
         if($type == 0){
             $where = '';
             $parem = array();
         }elseif ($type == 1){
             $where = ' group_user=:group_user and ';
             $parem = array(':group_user'=>$member['member_id']);
         }elseif ($type == 2){
             $where = ' group_user=:group_user and ';
             $parem = array(':group_user'=>$user_id);
         }

         if($class == 0){
             $where .= ' uniacid = :uniacid and group_status=1 ';
             $parem[':uniacid'] = $_W['uniacid'];

             if(!empty($search)){
                 $where .= ' and group_name like :group_name';
                 $parem[':group_name'] =  '%' . trim($search) . '%';
             }
         }else{
             $where .= ' uniacid = :uniacid and group_status=1 and group_class=:group_class ';
             $parem[':uniacid'] = $_W['uniacid'];
             $parem[':group_class'] = $class;
         }
         $communityAll = pdo_fetchall("select * from " . tablename('myxs_fodder_community') . " where ".$where,$parem );
         if(!empty($communityAll)){
             foreach ($communityAll as $k=>$v){
                 $location3 = explode(',',$location);
                 $location2 = explode(',',$v['group_location']);

                 $distance = $this->GetDistance(floatval($location3[0]),floatval($location3[1]),floatval($location2[0]),floatval($location2[1]),1,2);
                 $communityAll[$k]['km'] = intval($distance);
//                 $status=$this->http_request('https://apis.map.qq.com/ws/distance/v1/matrix/?mode=driving&from='.$location.'&to='.$v['group_location'].'&key='.$key);
//                 $communityAll[$k]['km'] = $status['result']['rows'][0]['elements'][0]['distance'];

                 $num = $this->getCommunityContentNum($v['id']);
                 $communityAll[$k]['group_num'] = $num;
             }
         }
         foreach ($communityAll as $key => $row ){
             $kmsort[$key] = intval($row['km']);
             $ids[$key] = $row['id'];
             $new[$key] = $row['group_create_time'];
             $people[$key] = $row['group_number'];
         }
         //排序 0默认排序 1离我最近 2最新创建 3人气最高
         if($order == 0){
             array_multisort($ids,SORT_ASC,$communityAll);
         }elseif($order == 1){
             array_multisort($kmsort,SORT_ASC,$communityAll);
         }elseif($order == 2){
             array_multisort($new,SORT_DESC,$communityAll);
         }elseif($order == 3){
             array_multisort($people,SORT_DESC,$communityAll);

         }
         $community = array_slice($communityAll,$start,$end);

         $member = $this->getUserInfo('');


         if(!empty($community)){
             foreach ($community as $k=>$v){
                 if(strpos(toimage($v['group_logo']),'http://') !== false){
                     $community[$k]['group_logo'] = str_ireplace("http://","https://",toimage($v['group_logo']));
                 }else{
                     $community[$k]['group_logo'] = toimage($v['group_logo']);
                 }
                 if(strpos(toimage($v['group_logo_s']),'http://') !== false){
                     $community[$k]['group_logo_s'] = str_ireplace("http://","https://",toimage($v['group_logo_s']));
                 }else{
                     $community[$k]['group_logo_s'] = toimage($v['group_logo_s']);
                 }

                 $community_log = pdo_fetch("select id from " . tablename('myxs_fodder_community_log') . " where uniacid = :uniacid and group_id=:group_id and member_id=:member_id and status=0 ", array(':uniacid'=>$_W['uniacid'],':group_id'=>$v['id'],':member_id'=>$member['member_id']));
                if(!empty($community_log)){
                    $community[$k]['is_join'] = true;
                }else{
                    $community[$k]['is_join'] = false;
                }

                 $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$v['group_class']));
                 $community[$k]['group_class_name'] = $class['class_name'];

                 $member_user =pdo_fetch("select member_name,member_id,member_head_portrait from " . tablename('myxs_fodder_member') . " where member_id = :member_id and uniacid =:uniacid", array(':member_id' => $v['group_user'],':uniacid'=>$_W['uniacid']));
                 $community[$k]['member_name'] = $member_user['member_name'];

                 unset($community[$k]['group_class']);
                 unset($community[$k]['group_message']);
                 unset($community[$k]['group_status']);
                 unset($community[$k]['group_user']);
                 unset($community[$k]['group_user_wx']);
                 unset($community[$k]['join_time']);
                 unset($community[$k]['member_name']);
                 unset($community[$k]['uniacid']);

             }
         }
         $this->result(0, '',$community);
     }

    /**
     * @param $id
     * @return int
     * 获取社群下的所有素材数
     */
     public function getCommunityContentNum($id){
         global $_W, $_GPC;
         $num = pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_content') . " where  uniacid = :uniacid  and circle_id = :circle_id", array(':uniacid'=>$_W['uniacid'],':circle_id'=>$id));
         return intval($num);

     }

    /**
     * @param $group_id
     * 删除我的社群 并将社群成员剔除 及社群素材归零
     */
     public function DeleteMyCommunity($group_id){
         global $_W, $_GPC;
         $member = $this->getUserInfo('');

         $community = pdo_fetch("select * from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$group_id));
         if(empty($community)){
             $this->result(0, '',array('status'=>0,'msg'=>'该群不存在'));
         }else{
            if($community['group_user'] != $member['member_id']){
                $this->result(0, '',array('status'=>0,'msg'=>'这不是您的社群，无法删除'));
            }else{

                try {
                    pdo_begin();
                    $delete = pdo_delete('myxs_fodder_community',array('uniacid'=>$_W['uniacid'],'id'=>$community['id']));
                    if(!$delete){
                        throw new PDOException("删除失败！");
                    }
                    $community_log = pdo_fetchall("select id from " . tablename('myxs_fodder_community_log') . " where uniacid = :uniacid and group_id=:group_id and status=0 ", array(':uniacid'=>$_W['uniacid'],':group_id'=>$community['id']));
                    if(!empty($community_log)){
                        foreach ($community_log as $k=>$v){
                            pdo_delete('myxs_fodder_community_log',array('id'=>$v['id'],'uniacid'=>$_W['uniacid']));
                        }
                    }


                    $contentList = pdo_fetchall("select content_id from " . tablename('myxs_fodder_content') . " where content_status > :content_status and content_class = :content_class and uniacid = :uniacid  and circle_id = :circle_id", array(':content_status' => 0,':content_class'=>1,':uniacid'=>$_W['uniacid'],':circle_id'=>$community['id']));
                    if(!empty($contentList)){
                        foreach ($contentList as $k=>$v){
                            pdo_update('myxs_fodder_content',array('circle_id'=>0),array('content_id'=>$v['content_id'],'uniacid'=>$_W['uniacid']));
                        }
                    }


                    pdo_commit();
                    $this->result(0, '', array('status'=>1,'msg'=>'删除成功'));
                } catch (PDOException $exception) {
                    $this->result(0, '', array('status'=>0,'msg'=>'删除失败'));
                    pdo_rollback();
                }
            }
         }
     }
    /**
     * @param $group_id
     * @param $location
     * 根据ID 获取社群信息
     */
     public function LookCommunity($group_id,$location){
         global $_W, $_GPC;
         $member = $this->getUserInfo('');

         $community = pdo_fetch("select * from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$group_id));
         if(empty($community)){
             $this->result(0, '',array('status'=>0,'msg'=>'该群不存在'));
         }else{
             $num = $this->getCommunityContentNum($community['id']);
             $community['group_num'] = $num;

             if(strpos(toimage($community['group_logo']),'http://') !== false){
                 $community['group_logo'] = str_ireplace("http://","https://",toimage($community['group_logo']));
             }else{
                 $community['group_logo'] = toimage($community['group_logo']);
             }
             if(strpos(toimage($community['group_logo_s']),'http://') !== false){
                 $community['group_logo_s'] = str_ireplace("http://","https://",toimage($community['group_logo_s']));
             }else{
                 $community['group_logo_s'] = toimage($community['group_logo_s']);
             }

             $location3 = explode(',',$location);
             $location2 = explode(',',$community['group_location']);

             $distance = $this->GetDistance(floatval($location3[0]),floatval($location3[1]),floatval($location2[0]),floatval($location2[1]),1,2);
             $community['km'] = intval($distance);

             $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$community['group_class']));
             $community['group_class_name'] = $class['class_name'];

             $member_user =pdo_fetch("select member_name,member_id,member_head_portrait from " . tablename('myxs_fodder_member') . " where member_id = :member_id and uniacid =:uniacid", array(':member_id' => $community['group_user'],':uniacid'=>$_W['uniacid']));

             $community['member_name'] = $member_user['member_name'];

         }
         $community_log = pdo_fetch("select id,join_time from " . tablename('myxs_fodder_community_log') . " where uniacid = :uniacid and group_id=:group_id and member_id=:member_id and status=0 ", array(':uniacid'=>$_W['uniacid'],':group_id'=>$community['id'],':member_id'=>$member['member_id']));
         if(!empty($community_log)){
             $community['is_join'] = true;
             $community['join_time_date'] = date('Y-m-d H:i:s',$community_log['join_time']);
             $community['join_time'] = $community_log['join_time'];
         }else{
             $community['is_join'] = false;
         }
         $this->result(0, '',$community);
     }

    /**
     * 退群
     */
    public function ExitCommunity($group_id){
        global $_W, $_GPC;
        $member = $this->getUserInfo('');
        $community_log = pdo_fetch("select id from " . tablename('myxs_fodder_community_log') . " where uniacid = :uniacid and group_id=:group_id and member_id=:member_id and status=0 ", array(':uniacid'=>$_W['uniacid'],':group_id'=>$group_id,':member_id'=>$member['member_id']));
        if(empty($community_log)){
            $this->result(0, '', array('status'=>0,'msg'=>'您未加入该群，无法退出'));
        }else{
            try {
                pdo_begin();
                $status= pdo_delete('myxs_fodder_community_log',array('id'=>$community_log['id'],'uniacid'=>$_W['uniacid']));
                if(!$status){
                    throw new PDOException("退出失败！");
                }

                $contentList = pdo_fetchall("select content_id from " . tablename('myxs_fodder_content') . " where content_status > :content_status and content_class = :content_class and uniacid = :uniacid  and circle_id = :circle_id and member_id=:member_id", array(':content_status' => 0,':content_class'=>1,':uniacid'=>$_W['uniacid'],':circle_id'=>$group_id,':member_id'=>$member['member_id']));
                if(!empty($contentList)){
                    foreach ($contentList as $k=>$v){
                        pdo_update('myxs_fodder_content',array('circle_id'=>0),array('content_id'=>$v['content_id'],'uniacid'=>$_W['uniacid']));
                    }
                }

                pdo_commit();
                $this->result(0, '', array('status'=>1,'msg'=>'退出成功'));
            } catch (PDOException $exception) {
                $this->result(0, '', array('status'=>0,'msg'=>'退出失败'));
                pdo_rollback();
            }
        }
    }

    /**
     * @param $start
     * @param $end
     * @param $location
     * 获取用户所加入的社群
     */
     public function GetUserJoinCommunity($start,$end,$location){
         global $_W, $_GPC;
         $member = $this->getUserInfo('');

         $communityAll = pdo_fetchall("select b.*,a.join_time from " . tablename('myxs_fodder_community_log') . " as a join ".tablename('myxs_fodder_community')." as b on a.group_id=b.id where a.uniacid = :uniacid and  a.member_id=:member_id and a.status=0 ", array(':uniacid'=>$_W['uniacid'],':member_id'=>$member['member_id']));
         if(!empty($communityAll)){
             foreach ($communityAll as $k=>$v){
                 if($v['group_user'] == $member['member_id']){
                     unset($communityAll[$k]);
                     continue;
                 }
                 $location3 = explode(',',$location);
                 $location2 = explode(',',$v['group_location']);

                 $distance = $this->GetDistance(floatval($location3[0]),floatval($location3[1]),floatval($location2[0]),floatval($location2[1]),1,2);
                 $communityAll[$k]['km'] = intval($distance);
                 $num = $this->getCommunityContentNum($v['id']);
                 $communityAll[$k]['group_num'] = $num;
             }
         }
         foreach ($communityAll as $key => $row ){
             $kmsort[$key] = intval($row['km']);
             $ids[$key] = $row['id'];
             $new[$key] = $row['group_create_time'];
             $people[$key] = $row['group_number'];
         }
         //排序 0默认排序 1离我最近 2最新创建 3人气最高
         $order = 0;
         if($order == 0){
             array_multisort($ids,SORT_ASC,$communityAll);
         }elseif($order == 1){
             array_multisort($kmsort,SORT_ASC,$communityAll);
         }elseif($order == 2){
             array_multisort($new,SORT_DESC,$communityAll);
         }elseif($order == 3){
             array_multisort($people,SORT_DESC,$communityAll);

         }
         $community = array_slice($communityAll,$start,$end);

         if(!empty($community)){
             foreach ($community as $k=>$v){
                 if(strpos(toimage($v['group_logo']),'http://') !== false){
                     $community[$k]['group_logo'] = str_ireplace("http://","https://",toimage($v['group_logo']));
                 }else{
                     $community[$k]['group_logo'] = toimage($v['group_logo']);
                 }
                 if(strpos(toimage($v['group_logo_s']),'http://') !== false){
                     $community[$k]['group_logo_s'] = str_ireplace("http://","https://",toimage($v['group_logo_s']));
                 }else{
                     $community[$k]['group_logo_s'] = toimage($v['group_logo_s']);
                 }

                 $community[$k]['is_join'] = true;

                 $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$v['group_class']));
                 $community[$k]['group_class_name'] = $class['class_name'];

                 $member_user =pdo_fetch("select member_name,member_id,member_head_portrait from " . tablename('myxs_fodder_member') . " where member_id = :member_id and uniacid =:uniacid", array(':member_id' => $v['group_user'],':uniacid'=>$_W['uniacid']));
                 $community[$k]['member_name'] = $member_user['member_name'];


                 unset($community[$k]['group_class']);
                 unset($community[$k]['group_message']);
                 unset($community[$k]['group_status']);
                 unset($community[$k]['group_user']);
                 unset($community[$k]['group_user_wx']);
                 unset($community[$k]['join_time']);
                 unset($community[$k]['member_name']);
                 unset($community[$k]['uniacid']);
             }
         }
         $this->result(0, '',$community);
     }

    /**
     * 获取我的群  加入及创建
     */
    public function GetMyAllCommunity(){
        global $_W, $_GPC;
        $member = $this->getUserInfo('');

        $team = array();
        $community = pdo_fetchall("select id,group_name,group_class from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and group_user=:group_user", array(':uniacid'=>$_W['uniacid'],':group_user'=>$member['member_id']));
        if(!empty($community)){
            foreach ($community as $k=>$v){
                $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$v['group_class']));
                array_push($team,array('id'=>$v['id'],'name'=>$v['group_name'],'class_name'=>$class['class_name']));
            }
        }

        $communityAll = pdo_fetchall("select b.id,b.group_name,b.group_class from " . tablename('myxs_fodder_community_log') . " as a join ".tablename('myxs_fodder_community')." as b on a.group_id=b.id where a.uniacid = :uniacid and  a.member_id=:member_id and a.status=0 ", array(':uniacid'=>$_W['uniacid'],':member_id'=>$member['member_id']));
        if(!empty($communityAll)){
            foreach ($communityAll as $k=>$v){
                $class = pdo_fetch("select * from " . tablename('myxs_fodder_community_class') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$v['group_class']));
                array_push($team,array('id'=>$v['id'],'name'=>$v['group_name'],'class_name'=>$class['class_name']));
            }
        }
        $this->result(0, '',$team);

    }

    /**
     * 获取素材详情
     *
     */

    public function GetCommunityContentMess($content_id){
        global $_W, $_GPC;
        $member = $this->getUserInfo('');
        $content = pdo_fetch("select content,content2,circle_id,video_img,text,create_time,content_id,type from " . tablename('myxs_fodder_content') . " where  uniacid = :uniacid and  content_id = :content_id  ", array(':uniacid'=>$_W['uniacid'],':content_id'=>$content_id));
        if(!empty($content)){
            $content['content'] = json_decode($content['content'],true);
            $content['content2'] = json_decode($content['content2'],true);
            foreach ($content['content'] as $keys => $values){
                if(strpos(toimage($values),'http://') !== false){
                    $content['content'][$keys] = str_ireplace("http://","https://",toimage($values));
                }else{
                    $content['content'][$keys] = toimage($values);
                }
            }
            foreach ($content['content2'] as $keys => $values){
                if(strpos(toimage($values),'http://') !== false){
                    $content['content2'][$keys] = str_ireplace("http://","https://",toimage($values));
                }else{
                    $content['content2'][$keys] = toimage($values);
                }
            }

            if(strpos(toimage($content['video_img']),'http://') !== false){
                $content['video_img'] = str_ireplace("http://","https://",toimage($content['video_img']));
            }else{
                $content['video_img'] = toimage($content['video_img']);
            }


            $community = pdo_fetch("select id,group_logo,group_logo_s,group_number,group_name from " . tablename('myxs_fodder_community') . " where uniacid = :uniacid and id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$content['circle_id']));
            if(!empty($community)){
                $num = $this->getCommunityContentNum($community['id']);
                $community['group_num'] = $num;

                if(strpos(toimage($community['group_logo']),'http://') !== false){
                    $community['group_logo'] = str_ireplace("http://","https://",toimage($community['group_logo']));
                }else{
                    $community['group_logo'] = toimage($community['group_logo']);
                }
                if(strpos(toimage($community['group_logo_s']),'http://') !== false){
                    $community['group_logo_s'] = str_ireplace("http://","https://",toimage($community['group_logo_s']));
                }else{
                    $community['group_logo_s'] = toimage($community['group_logo_s']);
                }
                $community_log = pdo_fetch("select id from " . tablename('myxs_fodder_community_log') . " where uniacid = :uniacid and group_id=:group_id and member_id=:member_id and status=0 ", array(':uniacid'=>$_W['uniacid'],':group_id'=>$community['id'],':member_id'=>$member['member_id']));
                if(!empty($community_log)){
                    $community['is_join'] = true;
                }else{
                    $community['is_join'] = false;
                }
            }

            $content['circle'] = $community;


        }

        $this->result(0, '',$content);

    }

    /**
     * @param $data
     * @return bool|mixed
     * 获取用户信息
     */
     public function getUserInfo($data){
        global $_W, $_GPC;
        if (empty($data['member_id'])){
            $memberInfo = pdo_fetch( "select * from " . tablename('myxs_fodder_member') . " where open_id = :openid and uniacid = :uniacid", array(':openid' => $_W['openid'], ':uniacid'=>$_W['uniacid']));
        }else{
            $memberInfo = $memberInfo = pdo_fetch("select * from " . tablename('myxs_fodder_member') . " where member_id = :member_id and uniacid = :uniacid", array(':member_id' => $data['member_id'], ':uniacid'=>$_W['uniacid']));
        }
        if(!empty($memberInfo)){
            $memberInfo['intergral'] = floatval($memberInfo['intergral']);
        }
        return $memberInfo;
     }
     public function result($errno, $message, $data = '') {
        exit(json_encode(array(
            'errno' => $errno,
            'message' => $message,
            'data' => $data,
        )));
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
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @param int $len_type
     * @param int $decimal
     * @return false|float
     * 获取两个经纬度之间的距离
     */
     public function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
    {
        $pi = 3.1415926;
        $er = 6378.137;

        $radLat1 = $lat1 * $pi / 180.0;
        $radLat2 = $lat2 * $pi / 180.0;
        $a = $radLat1 - $radLat2;
        $b = ($lng1 * $pi / 180.0) - ($lng2 * $pi / 180.0);
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
        $s = $s * $er;
        $s = round($s * 1000);
        if ($len_type > 1)
        {
            $s /= 1000;
        }
        return round($s, $decimal);
    }
}