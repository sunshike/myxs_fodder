const app = getApp()
import http from '../../utils/request.js';
let siteroot = app.siteInfo.siteroot;
siteroot = siteroot.replace('app/index.php', 'web/index.php');
let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&c=utility&a=file&do=upload&thumb=0';
Page({

  /**
   * 页面的初始数据
   */
  data: {
    date: "",
    assignDate: "",
    years: 0,
    months: 0,
    intergral: 0,
    intergralList: [],
    dataNull: 2,
    lessenIntergral: 0,
    addIntergral: 0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var timestamp = Date.parse(new Date());
    var date = new Date(timestamp);
    //获取年份  
    var Y = date.getFullYear();
    //获取月份  
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    //获取当日日期 
    var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    this.setData({
      date: Y + '-' + M,
      years: Y,
      months: M
    })
    this.data.assignDate = Y + '-' + M;
    var monthday = this.getCurrentMonthDayNum(); //当前月份天数
    //当前月月初0:00
    var monthFirst = Date.parse(this.data.date) / 1000 - 8 * 60 * 60;
    //下月月初0:00
    var monthEnd = Date.parse(this.data.date) / 1000 + monthday * 24 * 60 * 60 - 8 * 60 * 60;
    http.get('intergralLog', {
      monthFirst: monthFirst,
      monthEnd: monthEnd
    }).then(data => {
      if (data.content.length == 0) {
        this.setData({
          dataNull: 0
        })
        return false
      } else {
        this.setData({
          dataNull: 1
        })
      }
      var datas = [data];
      this.setData({
        intergralList: datas,
        lessenIntergral: data.lessenIntergral,
        addIntergral: data.addIntergral,
        memberimg: app.globalData.member.memberImg,
        logo_bg: app.globalData.logo_bg
      })
    })
  },
  //获取当月天数
  getCurrentMonthDayNum: function () {
    let today = new Date();
    let dayAllThisMonth = 31;
    if (today.getMonth() + 1 != 12) {
      let currentMonthStartDate = new Date(today.getFullYear() + "/" + (today.getMonth() + 1) + "/01"); // 本月1号的日期
      let nextMonthStartDate = new Date(today.getFullYear() + "/" + (today.getMonth() + 2) + "/01"); // 下个月1号的日期
      dayAllThisMonth = (nextMonthStartDate - currentMonthStartDate) / (24 * 3600 * 1000);
    }

    return dayAllThisMonth;
  },
  //获取指定月天数
  getCurrentAssignMonthDayNum: function (e) {
    let today = new Date(e);
    let dayAllThisMonth = 31;
    if (today.getMonth() + 1 != 12) {
      let currentMonthStartDate = new Date(today.getFullYear() + "/" + (today.getMonth() + 1) + "/01"); // 本月1号的日期
      let nextMonthStartDate = new Date(today.getFullYear() + "/" + (today.getMonth() + 2) + "/01"); // 下个月1号的日期
      dayAllThisMonth = (nextMonthStartDate - currentMonthStartDate) / (24 * 3600 * 1000);
    }

    return dayAllThisMonth;
  },
  getDateTime: function (e) {
    var that = this;
    var date = e.detail.value
    var dataInterval = new Date(date.replace(/-/g, "/")).getTime();
    var dataChose = dataInterval / 1000;
    var arr = date.split("-");
    that.setData({
      years: arr[0],
      months: arr[1],
      intergralList: [],
      lessenIntergral: 0,
      addIntergral: 0
    })
    that.data.assignDate = date;
    var days = that.getCurrentAssignMonthDayNum(that.data.assignDate);
    //当前月月初0:00
    var monthFirst = Date.parse(that.data.assignDate) / 1000 - 8 * 60 * 60;
    //下月月初0:00
    var monthEnd = Date.parse(that.data.assignDate) / 1000 + days * 24 * 60 * 60 - 8 * 60 * 60;
    http.get('intergralLog', {
      monthFirst: monthFirst,
      monthEnd: monthEnd
    }).then(data => {
      if (data.content.length == 0) {
        that.setData({
          dataNull: 0
        })
        return false
      } else {
        that.setData({
          dataNull: 1
        })
      }
      var datas = [data];
      that.setData({
        intergralList: datas,
        lessenIntergral: data.lessenIntergral,
        addIntergral: data.addIntergral
      })
    })
  },
  fanhui: function () {
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