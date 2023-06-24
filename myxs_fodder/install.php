<?php

$sql = <<<EOT

DROP TABLE IF EXISTS `ims_myxs_fodder_admin_intergral_log`;
CREATE TABLE `ims_myxs_fodder_admin_intergral_log`  (
  `lg_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NULL DEFAULT 0 COMMENT '用户id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '管理员id',
  `inter_type` int(1) NULL DEFAULT NULL COMMENT '2加积分  1减积分',
  `inter_count` int(11) NULL DEFAULT NULL COMMENT '积分数',
  `log_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
  `uniacid` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`lg_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_advert`;
CREATE TABLE `ims_myxs_fodder_advert`  (
  `advert_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '广告ID',
  `advert_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '广告名称',
  `advert_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '广告内容',
  `advert_class` tinyint(5) NULL DEFAULT 2 COMMENT '广告身份  1流量广告，2平台，3会员广告',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `start_time` int(11) NULL DEFAULT 0 COMMENT '展示开始时间',
  `end_time` int(11) NULL DEFAULT 0 COMMENT '展示结束时间',
  `advert_status` tinyint(5) NULL DEFAULT 1 COMMENT '0关闭，1开启，2删除',
  `uniacid` int(11) NULL DEFAULT 0,
  `advert_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '广告位置',
  `advert_class_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '广告类型',
  `advert_times` int(11) NULL DEFAULT 0 COMMENT '曝光次数',
  `advert_video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '选择平台及用户时  选择视频 的 地址',
  `advert_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '选择平台及用户时  选择图片 的 地址',
  PRIMARY KEY (`advert_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_class`;
CREATE TABLE `ims_myxs_fodder_class`  (
  `class_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `class_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '分类名称',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `class_status` int(5) NULL DEFAULT 1 COMMENT '分类状态：1启用，2删除',
  `circle_id` int(11) NULL DEFAULT 0 COMMENT '0：广场分类，非0为圈子ID且为圈子分类',
  `uniacid` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`class_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '内容分类表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_community`;
CREATE TABLE `ims_myxs_fodder_community`  (
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
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社群表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_community_class`;
CREATE TABLE `ims_myxs_fodder_community_class`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `class_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '社群分类名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社群分类表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_community_log`;
CREATE TABLE `ims_myxs_fodder_community_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `group_id` int(11) NULL DEFAULT NULL COMMENT '群ID',
  `member_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `join_time` int(10) NULL DEFAULT NULL COMMENT '加入时间',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '0 正常 1已退群',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 103 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '社群加入记录' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_content`;
CREATE TABLE `ims_myxs_fodder_content`  (
  `content_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '内容ID',
  `circle_id` int(11) NULL DEFAULT 0 COMMENT '圈子ID',
  `class_id` int(11) NULL DEFAULT 0 COMMENT '分类ID',
  `member_id` int(11) NULL DEFAULT 0 COMMENT '会员ID',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '图片，视频地址',
  `content_status` int(5) NULL DEFAULT 1 COMMENT '0：删除，1：显示，2隐藏',
  `content_class` int(5) NULL DEFAULT NULL COMMENT '1：广场内容，2圈子内容',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `clnb` int(11) NULL DEFAULT 0 COMMENT '收藏数量',
  `donnb` int(11) NULL DEFAULT 0 COMMENT '下载数量',
  `sharenb` int(11) NULL DEFAULT 0 COMMENT '分享数量',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '文本内容',
  `uniacid` int(11) NULL DEFAULT 0,
  `fictitious_clnb` int(11) NULL DEFAULT 0 COMMENT '虚拟收藏量',
  `fictitious_donnb` int(11) NULL DEFAULT 0 COMMENT '虚拟下载数量',
  `fictitious_sharenb` int(11) NULL DEFAULT 0 COMMENT '虚拟分享数量',
  `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT 'img:图片，video:视频，voice：音频',
  `grouping_id` int(11) NULL DEFAULT 0 COMMENT '用户分组ID，看查看内容根据此ID区分',
  `is_check` tinyint(1) NULL DEFAULT 0 COMMENT '是否检测  0未检测  1已检测',
  `no_pass_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '未通过ID',
  `likenum` int(11) NULL DEFAULT 0 COMMENT '点赞数量',
  `discuss` int(11) NULL DEFAULT 0 COMMENT '评论数量',
  `fictitious_likenum` int(11) NULL DEFAULT 0 COMMENT '虚拟点赞数',
  `video_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '视频封面地址',
  `content2` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '图片压缩地址',
  PRIMARY KEY (`content_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 248 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_day_sign`;
CREATE TABLE `ims_myxs_fodder_day_sign`  (
  `sign_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日签ID',
  `sign_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '日签标题',
  `sign_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '日签内容',
  `sign_img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '日签图片',
  `display_time` int(11) NULL DEFAULT NULL COMMENT '指定显示时间',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `sign_status` tinyint(5) NULL DEFAULT 1 COMMENT '0：删除，1：显示',
  `uniacid` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`sign_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_discuss`;
CREATE TABLE `ims_myxs_fodder_discuss`  (
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
) ENGINE = InnoDB AUTO_INCREMENT = 648 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_grouping`;
CREATE TABLE `ims_myxs_fodder_grouping`  (
  `grouping_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `grouping_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '分组名称',
  `grouping_passwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '分组邀请码',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '分组创建时间',
  `uniacid` int(11) NULL DEFAULT 0,
  `admin_id` int(11) NULL DEFAULT 0 COMMENT '分组管理员id',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '修改时间',
  `update_member_id` int(11) NULL DEFAULT 0 COMMENT '修改人id',
  PRIMARY KEY (`grouping_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_grouping_bg`;
CREATE TABLE `ims_myxs_fodder_grouping_bg`  (
  `bg_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `grouping_id` int(11) NULL DEFAULT NULL COMMENT '分组id',
  `admin_id` int(11) NULL DEFAULT NULL COMMENT '管理员 id',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '添加时间',
  `stat_bg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '开屏广告链接',
  `uniacid` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`bg_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_member`;
CREATE TABLE `ims_myxs_fodder_member`  (
  `member_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员ID',
  `open_id` varchar(225) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '小程序获取的微信openid',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '注册时间',
  `member_status` tinyint(5) NULL DEFAULT 1 COMMENT '0：删除：1：正常，2：黑名单',
  `member_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '会员名称',
  `member_head_portrait` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '会员头像',
  `member_mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '会员手机',
  `member_is_bind` tinyint(5) NULL DEFAULT 0 COMMENT '0：未绑定，1：已绑定',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `uniacid` int(11) NULL DEFAULT NULL,
  `is_system` int(5) NULL DEFAULT 0 COMMENT '0,不是系统管理员，1，是系统管理员，前台可上传广场内容',
  `grouping_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '用户分组ID，查看内容根据此ID区分',
  `intergral` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '用户积分',
  `is_class_admin` int(1) NULL DEFAULT 0 COMMENT '是否是分组管理员',
  `parent` int(11) NULL DEFAULT 0 COMMENT '上级ID',
  `balance` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '余额',
  `parent_time` int(10) NULL DEFAULT 0 COMMENT '绑定时间',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像压缩图片',
  `watermark` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '用户水印设置',
  PRIMARY KEY (`member_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 256 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '会员表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_member_intergral_log`;
CREATE TABLE `ims_myxs_fodder_member_intergral_log`  (
  `inter_id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL COMMENT '应用id',
  `member_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '详情（充值或者是下载）',
  `type` int(11) NULL DEFAULT NULL COMMENT '类型（1为减少 2为增加）',
  `amount` int(11) NULL DEFAULT 0 COMMENT '积分数量',
  `add_time` int(11) NULL DEFAULT NULL COMMENT '操作时间',
  `content_id` int(11) NULL DEFAULT NULL COMMENT '素材id',
  `operation` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类型（下载  转发 收藏）',
  `get_member_id` int(11) NULL DEFAULT 0 COMMENT '点击分享进入用户',
  PRIMARY KEY (`inter_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 948 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_notice`;
CREATE TABLE `ims_myxs_fodder_notice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `notice_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '公告标题',
  `notice_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '公告内容',
  `notice_time` int(10) NULL DEFAULT NULL COMMENT '添加时间',
  `notice_status` tinyint(1) NULL DEFAULT 1 COMMENT '状态 0删除  1正常',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_operation_log`;
CREATE TABLE `ims_myxs_fodder_operation_log`  (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `identity` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT 'user:用户',
  `operation` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT 'xz:下载,sz：收藏',
  `content_id` int(11) NULL DEFAULT 0 COMMENT '内容ID',
  `member_id` int(11) NULL DEFAULT 0 COMMENT '会员id',
  `content_class` int(5) NULL DEFAULT 0 COMMENT '1：广场内容，2：圈子内容',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `uniacid` int(11) NULL DEFAULT NULL,
  `status` int(5) NULL DEFAULT NULL COMMENT '1收藏，2取消，3下载',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 839 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户收藏，下载' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_shuffling`;
CREATE TABLE `ims_myxs_fodder_shuffling`  (
  `shuffling_id` int(11) NOT NULL AUTO_INCREMENT,
  `shuffling_content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '轮播图片',
  `shuffling_add_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  `shuffling_position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '轮播位置',
  `uniacid` int(11) NULL DEFAULT NULL,
  `shuffling_status` int(1) NULL DEFAULT 2 COMMENT '1显示  2隐藏  3删除',
  `shuffling_update_time` int(11) NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`shuffling_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_sign`;
CREATE TABLE `ims_myxs_fodder_sign`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NULL DEFAULT NULL,
  `uid` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `sign_time` int(10) NULL DEFAULT NULL COMMENT '签到时间',
  `sign_point` int(11) NULL DEFAULT NULL COMMENT '积分奖励',
  `days` int(11) NULL DEFAULT 0 COMMENT '连续签到天数',
  `days_point` int(11) NULL DEFAULT 0 COMMENT '连续签到奖励',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 45 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '签到表' ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_system`;
CREATE TABLE `ims_myxs_fodder_system`  (
  `system_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '系统设置ID',
  `system_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '设置识别码，sms:短信',
  `system_content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '设置内容',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `system` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '基础默认设置',
  `uniacid` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`system_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `ims_myxs_fodder_water_bg`;
CREATE TABLE `ims_myxs_fodder_water_bg`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NULL DEFAULT NULL COMMENT '添加时间',
  `stat_bg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '水印链接',
  `uniacid` int(11) NULL DEFAULT NULL,
  `uid` int(11) NULL DEFAULT NULL,
  `stat_bg_s` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '水印缩略图',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

EOT;

pdo_query($sql);
