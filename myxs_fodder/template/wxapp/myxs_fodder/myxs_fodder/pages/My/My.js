// pages/My/My.js
const app = getApp();
import http from '../../utils/request.js';
const {
  $Toast
} = require('../../iview_ui/base/index');
Page({
  /**
   * 页面的初始数据
   */
  data: {
    isAdmin: 0,
    banquan: app.globalData.banquan,
    barHeight: app.globalData.CustomBar,
    is_class_admin: 0,
    shuffling_content: [],
    shuffling_state: 2,
    toGroupShow: true,
    groupId: '',
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    const that = this
    this.setData({
        groupId: app.globalData.groupId,
        banquan: app.globalData.banquan,
        advCode: app.globalData.member_Advert.member.advert_text ? app.globalData.member_Advert.member.advert_text : "",
        advshow: app.globalData.member_Advert.member.show,
        watermark_status: app.globalData.watermark_status,
        adv: app.globalData.member_Advert.member.advert_text ? app.globalData.member_Advert.member.advert_text : ""
      }),
      wx.showShareMenu({
        withShareTicket: true,
        menus: ['shareAppMessage', 'shareTimeline']
      })
  },
  onHide() {
    this.getTabBar().setData({
      boxShow: false
    })
    this.getTabBar().reset()
    this.setData({
      toGroupShow: true
    })
  },
  previewNowImage: function (e) {
    var currentUrl = e.currentTarget.dataset.currenturl
    var previewUrls = e.currentTarget.dataset.previewurl
    wx.previewImage({
      current: currentUrl,
      urls: previewUrls,
    })
  },
  Closeadvert: function () {
    this.setData({
      advshow: 0
    })
  },
  myinfo: function () {
    const that = this;
    http.get('index').then(data => {
      that.setData({
        intergral: data.member.intergral
      })
    })
    if (app.globalData.member.memberImg == '' || app.globalData.member.memberName == '') {
      that.setData({
        memberImg: app.globalData.logo_bg,
        name: '点击登录',
        memberid: '登录后可获得更好体验',
        isAdmin: 0,
        is_class_admin: 0,
        is_login: 0
      })
    } else {
      that.setData({
        memberImg: app.globalData.member.memberImg,
        name: app.globalData.member.memberName,
        memberid: app.globalData.member.memberId,
        isAdmin: app.globalData.member.memberAdmin,
        is_class_admin: app.globalData.member.is_class_admin,
        is_login: 1
      })
    }
  },
  gologin: function () {
    if (this.data.name != "点击登录") return;
    wx.navigateTo({
      url: '../login/login',
    })
  },
  MyRelease: function () {
    wx.navigateTo({
      url: '../MyRelease/MyRelease',
    })
  },
  toLove() {
    wx.navigateTo({
      url: '../hello/hello',

    })
  },
  MyCollection: function () {
    wx.navigateTo({
      url: '../MyCollection/MyCollection',
    })
  },
  MyDownload: function () {
    wx.navigateTo({
      url: '../MyDownload/MyDownload',
    })
  },
  MyIntergral: function () {
    wx.navigateTo({
      url: '../integral/integral',
    })
  },
  MySet: function () {
    wx.navigateTo({
      url: '../MySet/MySet',
    })
  },
  watermark: function () {
    wx.navigateTo({
      url: '../watermark/watermark',
    })
  },
  GroupManagement: function () {
    wx.navigateTo({
      url: '../GroupManagement/GroupManagement',
    })
  },
  OpeningAdv: function () {
    wx.navigateTo({
      url: '../OpeningAdv/OpeningAdv',
    })
  },
  OpenMark: function () {
    wx.navigateTo({
      url: '../AddMark/addMark',
    })
  },
  onShow: function () {
    this.getTabBar().setData({
      selected: 3,
    })
    this.setData({
      releaseCoverShow: false
    })
    this.myinfo();
  },
  copyright: function () {
    wx.navigateTo({
      url: '../copyright/copyright',
    })
  },
  toMyGroup() {
    wx.navigateTo({
      url: "../MySocialGroup/index",
    })
  },
  fanhui: function () {
    wx.navigateBack({
      delta: 1
    })
  },
  Activation: function () {
    wx.navigateTo({
      url: '../Activation/Activation',
    })
  },
  //分享自定义
  onShareAppMessage: function (e) {
    return {
      title: app.globalData.toptext,
      imageUrl: app.globalData.topimg,
      path: 'myxs_fodder/pages/start/start?id=' + app.globalData.member.memberId, // 路径，传递参数到指定页面。
    }
  },
  //分享自定义
  onShareTimeline: function () {
    return {
      title: app.globalData.toptext,
      imageUrl: app.globalData.topimg
    }
  },
  // 加入群聊显示
  toGroup() {
    if (!app.globalData.groupId) {
      $Toast({
        content: '暂未配置群聊功能，请联系管理员',
        type: 'error'
      });
      return
    }
    this.setData({
      toGroupShow: !this.data.toGroupShow
    })
  },
  // 打开企业微信客服
  wechatService() {
    wx.openCustomerServiceChat({
      extInfo: {url: 'https://work.weixin.qq.com/kfid/kfcdfb699638926c2d4'},
      corpId: 'wwd0d619484628b4af',
      success(res) {}
    })
  },
})