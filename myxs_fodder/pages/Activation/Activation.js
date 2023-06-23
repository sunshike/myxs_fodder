// myxs_fodder/pages/Activation/Activation.js
import http from '../../utils/request.js';

const { $Toast } = require('../../iview_ui/base/index');
var app = getApp()
Page({
 
  /**
   * 页面的初始数据
   */
  data: {
    barHeight: app.globalData.CustomBar,
  },
  vfCode: function (e) {
    this.setData({
      Code: e.detail.value
    })
  },
  ReturnNew: function () {
    wx.navigateBack({
      delta: 1
    })
  },
  submt: function () {
    var that = this;
    var data = {
      code: that.data.Code
    }
    http.post('CheckGrouping', data).then(data => {
      if (data.status){
        wx.showModal({
          content: data.message,
          showCancel: false,
          confirmText: '确定',
          confirmColor: '#ff4081',
          success: function (res) {
            wx.reLaunch({
              url: '../index/index'
            })
          }
        })
       

      }else{
        wx.showModal({
          content: data.message,
          showCancel: false,
          confirmText: '确定',
          confirmColor: '#ff4081',
         
        })
       
      }




    })


  }


})