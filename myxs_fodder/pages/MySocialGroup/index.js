// myxs_fodder/pages/MySocialGroup/index.js
import http from '../../utils/request.js';
const app = getApp()
const {latitude, longitude} = wx.getStorageSync('pos')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    TabCur: 0,
    tabList: [
      "我的社群",
      "我加入的社群"
    ],
    barHeight: app.globalData.CustomBar,
    requestInterface: 'GetCommunity',
    my_group: true,
    my_join: false,
    contentListHeight: '100%',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onShow() {
    console.log('show')
    const squarelist = this.selectComponent('#squarelist')
    squarelist.dataQuery('', 0, 2, {
      type: 1,
      user_id: app.globalData.member.memberId
    })
  },
  onLoad: function (options) {
    const query = this.createSelectorQuery()
    query.select('#header').boundingClientRect();
    query.select('#selectBar').boundingClientRect();
    query.exec(rect => {
      console.log(rect) 
      this.setData({
        contentListHeight: `calc(100vh - ${rect[0].height}px - ${rect[1].height}px)`
      })
    })
  },
  onQuit(e) {
    const that = this
    http.get('ExitCommunity', {
      group_id: e.detail.group_id
    })
    .then(res => {
      console.log(res)
      const squarelist = that.selectComponent('#squarelist')
      const {latitude, longitude} = wx.getStorageSync('pos')
      squarelist.dataQuery(`${latitude},${longitude}`, 0, 2)
    })
    .catch(err => {
      console.log('退出社群失败', err)
    })
  },
  tabSelect(e) {
    const that = this
    const squarelist = that.selectComponent('#squarelist')
    let reInterface = (e.currentTarget.dataset.id == 1) ? 'GetUserJoinCommunity' : 'GetCommunity'
    const my_group = (e.currentTarget.dataset.id == 1) ? false : true
    const my_join = (e.currentTarget.dataset.id == 1) ? true : false
    this.setData({
      my_group: my_group,
      my_join: my_join,
      TabCur: e.currentTarget.dataset.id,
      requestInterface: reInterface
    }, () => {
      if(e.currentTarget.dataset.id == 1) {
        squarelist.dataQuery(`${latitude},${longitude}`, 0, 2)
      } else {
        squarelist.dataQuery('', 0, 2, {
          type: 1,
          user_id: app.globalData.member.memberId
        })
      }
    })
  },
  back(){
    wx.navigateBack({
      delta: 1
    })
  },
})