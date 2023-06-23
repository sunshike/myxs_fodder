// myxs_fodder/pages/hello/hello.js
import http from '../../utils/request.js';  
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    barHeight: app.globalData.CustomBar,
    content: [],

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    const that = this;
    app.util.request({
      'url': 'entry/wxapp/GetWife', 
      'enableCache': true,
      header: {
        'Content-Type': 'application/json'
      },
      success: function (res) {
        let obj = JSON.parse(res.data.data);
        let arr = [];
        for(let item in obj) {
          if(obj[item].indexOf("电话") != -1) arr.push(obj[item]+"\n☎");
          else arr.push(obj[item]+"\n"); 
        }
        console.log(arr.join(""))
        that.setData({
          content: arr
        })
      },
      fail: function (res) {
        wx.showToast({
          title: '获取数据失败',
        })
      },
    }); 
  },
  onCopy(e) {
    wx.setClipboardData({
      data: this.data.content.join(""),
    })
  },
  back(){
    wx.navigateBack({
      delta: 1
    })
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})