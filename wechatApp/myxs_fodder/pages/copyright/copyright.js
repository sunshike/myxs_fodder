// myxs_fodder/pages/copyright/copyright.js

Page({
  data: {
    opacity: 0,
    Edition: [{
        version: '当前版本 V1.0.19',
        update: [
          '1.修复分享空白。',
        ],
        show: 0,
        state: 0,
        Date: '2019年02月25日'
      },
      {
        version: '当前版本 V1.0.18',
        update: [
          '1.修复分享空白。',
          '2.新增日签开关。',
        ],
        show: 0,
        state: 0,
        Date: '2019年01月07日'
      },
      {
        version: '当前版本 V1.0.17',
        update: [
          '1.修复前端iOS上传视频无反应BUG。',
        ],
        show: 0,
        state: 0,
        Date: '2019年01月05日'
      },
      {
        version: '当前版本 V1.0.16',
        update: [
          '1.新增各页面顶部返回键。',
        ],
        show: 0,
        state: 0,
        Date: '2018年11月29日'
      },
      {
        version: '当前版本 V1.0.14',
        update: [
          '1.修复前台复制文本失效。',
          '2.修复新会员获取信息失效。',
        ],
        show: 0,
        state: 0,
        Date: '2018年11月29日'
      },
      {
        version: '当前版本 V1.0.12',
        update: [
          '1、优化页面请求',
          '2、新增广告位',
        ],
        show: 0,
        state: 0,
        Date: '2018年11月28日'
      },
      {
        version: '当前版本 V1.0.9',
        update: [
          '1、启动页3秒跳转。',
          '2、未设置启动页直接跳转首页。',
        ],
        show: 0,
        state: 0,
        Date: '2018年10月23日'
      },

      {
        version: '当前版本 V1.0.4',
        update: [
          '1、支持视频素材发布，浏览。',
          '2、增加用户分组根据分组权限浏览不同权限内容。',
        ],
        show: 0,
        state: 0,
        Date: '2018年10月23日'
      },
      {
        version: '当前版本 V1.0.3',
        update: [
          '1、修复授权登录无法获取到用户信息的bug，并优化授权体验，用户首次进入用户中心时需要授权，授权后以后进入小程序将不再弹出授权提示',
          '2、修复当“我的下载”列表中有被删除的内容时“我的下载”空白的bug',
        ],
        show: 1,
        state: 1,
        Date: '2018年10月16日'
      },
      {
        version: '版本 V1.0.2',
        update: [
          '1、后台可以指定用户权限前台发布素材；',
          '2、支持修改启动页图片以及启动页版权信息；',
          '3、单条内容最多上传9张图片'
        ],
        show: 0,
        state: 0,
        Date: '2018年10月15日'
      },
    ]
  },
  wxHao: function () {
    wx.showModal({
      content: '开发者微信:demigod_team',
      showCancel: false,
      confirmText: '复制微信',
      confirmColor: '#ff4081',
      success: function (res) {
        wx.setClipboardData({
          data: 'demigod_team',
        })
      }
    })
  },
  home: function () {
    wx.reLaunch({
      url: '../index/index',
    })
  },
  Show: function (e) {
    var that = this;
    var i = e.currentTarget.dataset.index
    if (that.data.Edition[i].show) {
      that.data.Edition[i].show = 0
    } else {
      that.data.Edition[i].show = 1
    }
    this.setData({
      Edition: that.data.Edition
    })
  },
  //页面滚动
  onPageScroll: function (e) {
    var d = (e.scrollTop / 25) / 10;
    this.setData({
      opacity: d
    })
  },
})