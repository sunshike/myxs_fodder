import http from '../../utils/request.js';
var app = getApp();
var t;
Page({
  data: { 
    HttpBl: 0,
    loadModal: false,
  },
  onLoad: function(options) { 
    var that = this;
    app.globalData.system_des = wx.getSystemInfoSync().system.includes('iOS') ? 'iphone' : 'android'
    http.get('index')
    .then(data => { 
      //小 1程序名字
      app.globalData.title = JSON.parse(data.system.system).title;
      // 群聊id
      app.globalData.groupId = JSON.parse(data.system.system).group_chat;
      // 分享图片
      app.globalData.topimg = data.system.system_content.share_bg;
      // 分享文字
      app.globalData.toptext = JSON.parse(data.system.system).share_txt;
      app.globalData.stat_bg = data.system.system_content.stat_bg;
      app.globalData.map_key = data.system.system_basic.map_key
      //LOGO
      app.globalData.logo_bg=data.system.system_content.logo_bg;
      //日签开关
      app.globalData.day_sign_status = JSON.parse(data.system.system).day_sign_status;
      //水印开关
      app.globalData.watermark_status = JSON.parse(data.system.system).watermark_status;
      //版权
      app.globalData.banquan = JSON.parse(data.system.system).copyright;
      //分类
      app.globalData.class = data.class;
      //广告
      app.globalData.member_Advert = data.member_advert;
      //下载消耗积分
      app.globalData.takeOutIntergral=data.intergral.takeOutIntergral;
      //分享奖励积分
      app.globalData.rewardIntergral=data.intergral.rewardIntergral;
      //管理员微信二维码
      app.globalData.adminWxNum = data.intergral.adminWxNum;
      // 水印图片
      app.globalData.member_water = data.member_water;
      // 视频激励广告id
      // app.globalData.videoAdId = data.intergral_advert.advert_text;
      app.globalData.videoAdId = "adunit-669c3b35e198abf1";
      var member = {
        memberImg: data.member.member_head_portrait,
        memberName: data.member.member_name,
        memberId: data.member.member_id,
        memberAdmin: data.member.is_system,
        watermark: JSON.parse(data.member.watermark),
        memberIntergral:data.member.intergral,
        is_class_admin:data.member.is_class_admin
      }
      //用户信息
      app.globalData.member = member
      if (data.system.system_content.stat_bg == '' || data.system.system_content.stat_bg == undefined) {
        wx.switchTab({
          url: '../square/square',
        })
        return false
      }
      var member_groups = data.member.grouping_id.split(",").sort();
      var member_max_group = member_groups[member_groups.length - 1];
      var bgData = {
        group_id: member_max_group,
        type: "look"
      }
      http.get('GetGroupBg', bgData)
      .then(datas => {
        if (datas) {
          that.setData({ 
            loadModal: true,
            jingruimg: datas.stat_bg,
          })
        } else {
          that.setData({
            loadModal: true,
            jingruimg: data.system.system_content.stat_bg,
          })
        }
      });
      that.setData({
        HttpBl: 1,
        Banquan: JSON.parse(data.system.system).copyright
      })
    })
    .catch(err => {
      console.log(err)
    })
  },
  // 直接进入首页
  index: function() {
    clearInterval(t)
    t = setTimeout(function() {
        wx.switchTab({
          url: '../Discover/discover',
        })
      }, 5000);
  },
})