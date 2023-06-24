<?php
// --------------------------------------------------------------------------------------------------------------------------新增表---------------------------------------------------------------------------------------------------------------------------
$tab = pdo_tableexists('myxs_fodder_shuffling');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_shuffling`  (
  `shuffling_id` int(11) NOT NULL AUTO_INCREMENT,
  `shuffling_content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '轮播图片',
  `shuffling_add_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `shuffling_position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '轮播位置',
  `uniacid` int(11) NULL DEFAULT NULL,
  `shuffling_status` int(1) NULL DEFAULT 2 COMMENT '1显示  2隐藏  3删除',
  `shuffling_update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`shuffling_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_member_intergral_log');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_member_intergral_log`  (
  `inter_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL COMMENT '应用id',
  `member_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '详情（充值或者是下载）',
  `type` int(11) NULL DEFAULT NULL COMMENT '类型（0为减少 1为增加）',
  `amount` int(11) NULL DEFAULT 0 COMMENT '积分数量',
  `add_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
  `content_id` int(11) NULL DEFAULT NULL COMMENT '素材id',
  `operation` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类型（下载  转发 收藏）',
  `get_member_id` int(11) NULL DEFAULT 0 COMMENT '点击分享进入用户',
  PRIMARY KEY (`inter_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_grouping_bg');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_grouping_bg`  (
  `bg_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `grouping_id` int(11) NULL DEFAULT NULL COMMENT '分组id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '管理员 id',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '添加时间',
  `stat_bg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开屏广告链接',
  `uniacid` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`bg_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_admin_intergral_log');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_admin_intergral_log`  (
  `lg_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NULL DEFAULT 0 COMMENT '用户id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '管理员id',
  `inter_type` int(1) NULL DEFAULT NULL COMMENT '2加积分  1减积分',
  `inter_count` int(11) NULL DEFAULT NULL COMMENT '积分数',
  `log_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
  `uniacid` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`lg_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_advert');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_advert`  (
  `advert_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告ID',
  `advert_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '广告名称',
  `advert_text` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '广告内容',
  `advert_class` tinyint(5) NULL DEFAULT 0 COMMENT '1流量广告，2自定义广告，3会员广告',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `start_time` int(11) NULL DEFAULT 0 COMMENT '展示开始时间',
  `end_time` int(11) NULL DEFAULT 0 COMMENT '展示结束时间',
  `advert_status` tinyint(5) NULL DEFAULT 1 COMMENT '0关闭，1开启，2删除',
  `uniacid` int(11) NULL DEFAULT 0,
  `advert_position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '广告位置',
  PRIMARY KEY (`advert_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_water_bg');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_water_bg`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NULL DEFAULT NULL COMMENT '添加时间',
  `stat_bg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '水印链接',
  `uniacid` int(11) NULL DEFAULT NULL,
  `uid` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_sign');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_sign`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `uid` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `sign_time` int(10) NULL DEFAULT NULL COMMENT '签到时间',
  `sign_point` int(11) NULL DEFAULT NULL COMMENT '积分奖励',
  `days` int(11) NULL DEFAULT 0 COMMENT '连续签到天数',
  `days_point` int(11) NULL DEFAULT 0 COMMENT '连续签到奖励',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '签到表' ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_notice');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_notice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `notice_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '公告标题',
  `notice_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '公告内容',
  `notice_time` int(10) NULL DEFAULT NULL COMMENT '添加时间',
  `notice_status` tinyint(1) NULL DEFAULT 1 COMMENT '状态 0删除  1正常',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('myxs_fodder_discuss');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_discuss`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `member_id` int(11) NOT NULL COMMENT '评论用户ID',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '评论内容',
  `create_time` int(10) NULL DEFAULT NULL COMMENT '评论时间',
  `content_id` int(11) NULL DEFAULT 0 COMMENT '评论素材ID',
  `discuss_id` int(11) NULL DEFAULT 0 COMMENT '评论id   用于回复评论',
  `discuss_mid` int(11) NULL DEFAULT 0 COMMENT '回复的评论发布用户ID',
  `discuss_type` tinyint(1) NULL DEFAULT 0 COMMENT '评论类型  0素材评论',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态  0正常  1已删除 2内容违规删除',
  `discuss_likenum` int(11) NULL DEFAULT 0 COMMENT '评论点赞数',
  `author` int(11) NULL DEFAULT 0 COMMENT '素材发布人ID',
  `is_child` tinyint(1) NULL DEFAULT 0 COMMENT '是否子评论回复',
  `id_arr` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '关系链',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('ims_myxs_fodder_community');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_community`  (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `group_class` int(11) NULL DEFAULT NULL COMMENT '群分类',
  `group_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群名称',
  `group_message` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '群简介',
  `group_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群位置',
  `group_logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群logo',
  `group_user` int(11) NULL DEFAULT NULL COMMENT '群主ID',
  `group_user_wx` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群主微信',
  `group_create_time` int(10) NULL DEFAULT NULL COMMENT '群创建时间',
  `group_status` tinyint(1) NULL DEFAULT 0 COMMENT '社群审核状态 0未审核  1已审核',
  `group_number` int(11) NULL DEFAULT 0 COMMENT '群人数',
  `group_logo_s` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群logo  450*450',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社群表' ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('ims_myxs_fodder_community_class');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_community_class`  (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `class_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '社群分类名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社群分类表' ROW_FORMAT = Dynamic;");
}
$tab = pdo_tableexists('ims_myxs_fodder_community_log');
if (empty($tab)){
    pdo_query("CREATE TABLE `ims_myxs_fodder_community_log`  (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `group_id` int(11) NULL DEFAULT NULL COMMENT '群ID',
  `member_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `join_time` int(10) NULL DEFAULT NULL COMMENT '加入时间',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '0 正常 1已退群',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社群加入记录' ROW_FORMAT = Dynamic;");
}

//---------------------------------------------------------------------------------------------------------------------------新增字段--------------------------------------------------------------------------------------------------------------------------
// advert
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_image');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." ADD  `advert_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '选择平台及用户时  选择图片 的 地址' AFTER `advert_video`;");
}
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_video');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." ADD  `advert_video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '选择平台及用户时  选择视频 的 地址' AFTER `advert_times`;");
}
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_times');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." ADD  `advert_times` int(11) NULL DEFAULT 0 COMMENT '曝光次数' AFTER `advert_class_type`;");
}
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_class_type');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." ADD  `advert_class_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '广告类型' AFTER `advert_position`;");
}

// content
$res = pdo_fieldexists('myxs_fodder_content', 'is_check');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD  `is_check` tinyint(1) NULL DEFAULT 0 COMMENT '是否检测  0未检测  1已检测' AFTER `grouping_id`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'no_pass_id');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD  `no_pass_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '未通过ID' AFTER `is_check`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'likenum');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD  `likenum` int(11) NULL DEFAULT 0 COMMENT '点赞数量' AFTER `no_pass_id`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'discuss');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD  `discuss` int(11) NULL DEFAULT 0 COMMENT '评论数量' AFTER `likenum`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'fictitious_likenum');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD  `fictitious_likenum` int(11) NULL DEFAULT 0 COMMENT '虚拟点赞数' AFTER `discuss`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'grouping_id');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD grouping_id int(11) NULL DEFAULT 0 COMMENT '用户分组ID，看查看内容根据此ID区分';");
}
$res = pdo_fieldexists('myxs_fodder_content', 'type');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD type varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT 'img:图片，video:视频，voice：音频';");
}
$res = pdo_fieldexists('myxs_fodder_content', 'fictitious_sharenb');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD fictitious_sharenb int(11) NULL DEFAULT 0 COMMENT '虚拟分享数量';");
}
$res = pdo_fieldexists('myxs_fodder_content', 'fictitious_donnb');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD fictitious_donnb int(11) NULL DEFAULT 0 COMMENT '虚拟下载数量';");
}
$res = pdo_fieldexists('myxs_fodder_content', 'fictitious_clnb');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD fictitious_clnb int(11) NULL DEFAULT 0 COMMENT '虚拟收藏量';");
}
$res = pdo_fieldexists('myxs_fodder_content', 'video_img');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD COLUMN `video_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '视频封面地址' AFTER `fictitious_likenum`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'content2');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD COLUMN `content2` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '图片压缩地址' AFTER `video_img`;");
}

// member
$res = pdo_fieldexists('myxs_fodder_member', 'is_class_admin');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD is_class_admin int(1) NOT NULL DEFAULT 0 COMMENT '是否是分组管理员';");
}
$res = pdo_fieldexists('myxs_fodder_member', 'intergral');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD intergral int(11) NOT NULL DEFAULT 0 COMMENT '用户积分';");
}
$res = pdo_fieldexists('myxs_fodder_member', 'watermark');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD watermark text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '用户水印设置';");
}
$res = pdo_fieldexists('myxs_fodder_member', 'parent');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD  `parent` int(11) NULL DEFAULT 0 COMMENT '上级ID' AFTER `is_class_admin`;");
}
$res = pdo_fieldexists('myxs_fodder_member', 'balance');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD  `balance` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '余额' AFTER `parent`;");
}
$res = pdo_fieldexists('myxs_fodder_member', 'parent_time');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD  `parent_time` int(10) NULL DEFAULT 0 COMMENT '绑定时间' AFTER `balance`;");
}
$res = pdo_fieldexists('myxs_fodder_member', 'avatar');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD COLUMN `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像压缩图片' AFTER `parent_time`;");
}
$res = pdo_fieldexists('myxs_fodder_member', 'watermark');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD COLUMN `watermark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '用户水印设置' AFTER `avatar`;");
}

// grouping
$res = pdo_fieldexists('myxs_fodder_grouping', 'update_member_id');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_grouping')." ADD update_member_id int(11) NULL DEFAULT 0 COMMENT '修改人id';");
}
$res = pdo_fieldexists('myxs_fodder_grouping', 'update_time');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_grouping')." ADD update_time int(11) NULL DEFAULT 0 COMMENT '修改时间';");
}
$res = pdo_fieldexists('myxs_fodder_grouping', 'admin_id');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_grouping')." ADD admin_id int(11) NULL DEFAULT 0 COMMENT '该分组管理员id';");
}

//water_bg
$res = pdo_fieldexists('ims_myxs_fodder_water_bg', 'stat_bg_s');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_water_bg')." ADD COLUMN `stat_bg_s` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '水印缩略图' AFTER `uid`;");
}


// ---------------------------------------------------------------------------------------------------------------------------修改字段------------------------------------------------------------------------------------------------------------------------
// advert
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_position');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." MODIFY  `advert_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '广告位置' AFTER `uniacid`;");
}
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_class');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." MODIFY  `advert_class` tinyint(5) NULL DEFAULT 2 COMMENT '广告身份  1流量广告，2平台，3会员广告' AFTER `advert_text`;");
}
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_text');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." MODIFY  `advert_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '广告内容' AFTER `advert_name`;");
}
$res = pdo_fieldexists('myxs_fodder_advert', 'advert_name');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_advert')." MODIFY  `advert_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '广告名称' AFTER `advert_id`;");
}

// content
$res = pdo_fieldexists('myxs_fodder_content', 'grouping_id');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." MODIFY  `grouping_id` int(11) NULL DEFAULT 0 COMMENT '用户分组ID，看查看内容根据此ID区分' AFTER `type`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'type');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." MODIFY  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT 'img:图片，video:视频，voice：音频' AFTER `fictitious_sharenb`;");
}
$res = pdo_fieldexists('myxs_fodder_content', 'text');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." MODIFY  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '文本内容' AFTER `sharenb`;");
}

// grouping
$res = pdo_fieldexists('myxs_fodder_grouping', 'admin_id');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_grouping')." MODIFY  `admin_id` int(11) NULL DEFAULT 0 COMMENT '分组管理员id' AFTER `uniacid`;");
}

// member
$res = pdo_fieldexists('myxs_fodder_member', 'grouping_id');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." MODIFY  `grouping_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '用户分组ID，查看内容根据此ID区分';");
}
$res = pdo_fieldexists('myxs_fodder_member', 'intergral');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." MODIFY  `intergral` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '用户积分' AFTER `grouping_id`;");
}
$res = pdo_fieldexists('myxs_fodder_member', 'is_class_admin');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." MODIFY  `is_class_admin` int(1) NULL DEFAULT 0 COMMENT '是否是分组管理员' AFTER `intergral`;");
}

// intergral_log
$res = pdo_fieldexists('myxs_fodder_member_intergral_log', 'type');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member_intergral_log')." MODIFY  `type` int(11) NULL DEFAULT NULL COMMENT '类型（1为减少 2为增加）' AFTER `text`;");
}

//water_bg  lx add at 2020-08-20
$res = pdo_fieldexists('myxs_fodder_water_bg', 'stat_bg_s');
if (!empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_water_bg')." MODIFY  `stat_bg_s` varchar(255) DEFAULT NULL COMMENT '水印缩略图' AFTER `uid`;");
}

//content   lx add at 2020-08-20
$res = pdo_fieldexists('myxs_fodder_content', 'video_img');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD video_img varchar(255) DEFAULT NULL COMMENT '视频封面地址';");
}
$res = pdo_fieldexists('myxs_fodder_content', 'content2');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_content')." ADD content2 text COMMENT '图片压缩地址';");
}

//member   lx add at 2020-08-20
$res = pdo_fieldexists('myxs_fodder_member', 'avatar');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." ADD  `avatar` varchar(255) DEFAULT NULL COMMENT '头像压缩图片' AFTER `parent_time`;");
}


$res = pdo_fieldexists('myxs_fodder_member', 'grouping_id');
if (empty($res)) {
    pdo_query("ALTER TABLE ".tablename('myxs_fodder_member')." MODIFY COLUMN `grouping_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '用户分组ID，查看内容根据此ID区分' AFTER `is_system`;");
}





