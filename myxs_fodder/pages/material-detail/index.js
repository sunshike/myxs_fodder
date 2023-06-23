import http from '../../utils/request.js'; 
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    content_text: '',
    barHeight: app.globalData.CustomBar,
    time: '',
    circle_title: '',
    hot_num: 0,
    material_num: 0,
    circle_img: '',
    content: [],
    content2: [],
    content_id: '0',
    type: '',
    commentShow: false,
    hasJoin: false,
    commentNum: 0,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onShareAppMessage: function (e) {
    return {}
  },
  onShareTimeline: function(e) {
    return {}
  },
  onLoad: function (options) {
    const that = this
    this.setData({
      commentShow: true,
      content_id: options.id
    })
    
    http.get('GetCommunityContentMess', {
      content_id: options.id
    })
    .then(res => {
      console.log('material-detail', res)
      that.group_id = res.circle_id
      that.setData({
        content_text: res.text,
        time: app.getDateDiff((+res.create_time) * 1000),
        circle_title: res.circle.group_name,
        hot_num: res.circle.group_number,
        material_num: res.circle.group_num,
        circle_img: res.circle.group_logo,
        content: res.content,
        content2: res.content2,
        type: res.type,
        hasJoin: res.circle.is_join,
      })
    })
  },
  
  onJoin(e) {
    const that = this
    const hasJoin = this.data.hasJoin
    if (!hasJoin) {
      wx.showModal({
        content: '是否加入该社群?',
        success(res) {
          if (res.confirm) {
            http.get('JoinCommunity', {
                group_id: that.group_id
              })
              .then(res => {
                if (res.status === 1) {
                  that.setData({
                    hasJoin: true
                  })
                } else {
                  wx.showModal({
                    content: res.msg,
                    showCancel: false,
                    confirmText: '知道了'
                  })
                }
              })
              .catch(err => {
                console.log('加入时发生错误', err)
              })
          }
        }
      })
    }
  },
  setCommentNum(e) {
    this.setData({
      commentNum: e.detail.num
    })
  },
  back(){
    wx.navigateBack({
      delta: 1
    })
  },
})