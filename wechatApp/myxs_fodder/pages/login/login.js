// myxs_fodder/pages/login/login.js

import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
const app = getApp()
Page({ 
  onLoad: function (options) {
    var that = this;
      that.setData({
        topimg: app.globalData.topimg
      })
  },
  fanhui: function () {
    wx.navigateBack({
      delta: 1
    })
  },
  uploadUserinfo: function (result) {
    var app = getApp();

    if (result.detail.errMsg != 'getUserInfo:ok') {
      wx.navigateBack({
        delta: 1
      })
    } else {
      app.globalData.member.memberImg = result.detail.userInfo.avatarUrl;
      var data = {
        avatarUrl: result.detail.userInfo.avatarUrl,
        nickName: result.detail.userInfo.nickName
      }
      http.get('UpdateMember', data).then(data => {
        if (data) {
          app.globalData.member.memberName = result.detail.userInfo.nickName;
          $Toast({
            content: '授权成功，请返回首页',
            type: 'success'
          });
          wx.redirectTo({
            url: '../My/My',
          })
        } else {
          $Toast({
            content: '未知错误',
            type: 'error'
          });
        }
      });
    }
  },
})