// myxs_fodder/pages/SquareDetail/index.js
import http from '../../utils/request.js';
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    groupHide: true,
    TabCur: 0,
    title: '',
    imgSrc: '',
    backgroundSrc: '',
    hotNum: 999,
    materialNum: 999,
    group_classify: '', // 社群分类
    des: '', // 简介
    wx: '', // 群主微信
    barHeight: app.globalData.CustomBar,
    tabList: [
      '素材',
      '群主其他社群'
    ],
    groupId: '',
    btnHidden: false, // 悬浮按钮隐藏
  },
  onShareAppMessage: function (e) {
    return {}
  },
  onShareTimeline: function(e) {
    return {}
  },
  /**
   * 生命周期函数--监听页面加载 
   */
  onLoad: function (options) {
    const query = this.createSelectorQuery()
    query.select('#header').boundingClientRect();
    query.select('#selectBar').boundingClientRect();
    query.exec(rect => {
      console.log(rect) 
      this.setData({
        contentListHeight: `calc(100vh - ${rect[0].height}px - ${rect[1].height}px + 20px)`
      })
    })
    
    const that = this
    this.setData({
      groupId: app.globalData.groupId
    })
    console.log(options)
    this.pos = options.pos
    http.get('GetCommunityMess', {
      group_id: options.id,
      location: options.pos
    })
    .then(res => {
      console.log(res)
      that.wx_id = res.group_user_wx
      const squarelist = that.selectComponent('#squarelist')
      squarelist.dataQuery(that.pos, 0, 2, {
        type: 2,
        class: 0,
        user_id: res.group_user
      })
      const content = that.selectComponent('#content')
      content.dataQuery(1, {group_id: res.id}, true)
      that.setData({
        imgSrc: res.group_logo,
        hotNum: res.group_number,
        materialNum: res.group_num,
        wx: res.member_name,
        des: res.group_message,
        group_classify: res.group_class_name,
        title: res.group_name,
        backgroundSrc: res.group_logo_s
      })
    })
    .catch(err => {
      console.log('squareDetail page 获取数据出错', err)
    })
    
    
    
  },
  togglecommentCover(e) {
    this.setData({
      btnHidden: !this.data.btnHidden
    })
  },
  back(){
    wx.navigateBack({
      delta: 1
    })
  },
  tabSelect(e) {
    this.setData({
      TabCur: e.currentTarget.dataset.id
    }, () => {
      if(e.currentTarget.dataset.id == 1) {
        
      }
    })
    
  },
  copy_wx() {
    wx.setClipboardData({
      data: this.wx_id,
    })
  },
  toGroup() {
    this.setData({
      groupHide: !this.data.groupHide
    })
  },
  toMaterialDetail(e) {
    wx.navigateTo({
      url: `../material-detail/index?id=${e.detail.id}`,
    })
  },
})