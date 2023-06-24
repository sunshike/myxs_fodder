import http from '../../utils/request.js';  
var app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    contentListHeight: '100%', 
    barHeight: app.globalData.CustomBar,
  }, 

  onLoad: function(options) {
    const query = this.createSelectorQuery()
    query.select('#header').boundingClientRect();
    query.exec(rect => {
      console.log(rect) 
      this.setData({
        contentListHeight: `calc(100vh - ${rect[0].height}px)`
      })
    })

    const content = this.selectComponent('#content')
    content.dataQuery(1, {}, true)
  },
  ReturnNew: function() {
    wx.navigateBack({
      delta: 1
    })
  },
})