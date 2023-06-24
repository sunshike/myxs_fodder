import util from 'we7/resource/js/util.js';

App({ 
  onLaunch: function (res) {
    wx.getSystemInfo({
      success: e => {
        this.globalData.StatusBar = e.statusBarHeight;
        let capsule = wx.getMenuButtonBoundingClientRect();
        if (capsule) {
          this.globalData.Custom = capsule;
          this.globalData.CustomBar = capsule.bottom + capsule.top - e.statusBarHeight;
        } else {
          this.globalData.CustomBar = e.statusBarHeight + 50;
        }
      }
    })
  },
  
  // 计算距离当前间隔时间
  getDateDiff(dateTimeStamp) {
    var result;
    var minute = 1000 * 60;
    var hour = minute * 60;
    var day = hour * 24;
    var halfamonth = day * 15;
    var month = day * 30;
    var now = new Date().getTime();
    var date = new Date(dateTimeStamp)
    var diffValue = now - dateTimeStamp;
    if (diffValue < 0) {
      return;
    }
    var monthC = diffValue / month;
    var weekC = diffValue / (7 * day);
    var dayC = diffValue / day;
    var hourC = diffValue / hour;
    var minC = diffValue / minute;
    if (dayC >= 1) {
      if (parseInt(dayC) < 2) {
        result = "昨天";
      } else {
        var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1)
        var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate()
        var h = date.getHours();
        var m = date.getMinutes();
        var s = date.getSeconds();
        result = M + '月' + D + '日';
      }
    } else if (hourC >= 1) {
      result = "" + parseInt(hourC) + "小时前";
    } else if (minC >= 1) {
      result = "" + parseInt(minC) + "分钟前";
    } else {
      result = "刚刚";
    }
    return result;
  },
  
  onError: function (msg) {
    console.log('出现错误', msg)
  },
  //加载微擎工具类
  util: util,

  globalData: {
    userInfo: null,
    openid: 0,
  },
  siteInfo: require('siteinfo.js')
});