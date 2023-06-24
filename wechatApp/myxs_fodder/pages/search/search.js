//index.js
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
const {
  latitude,
  longitude
} = wx.getStorageSync('pos')
const app = getApp()
Page({
  data: {
    class_id: -1,
    isSearch: false,
    inputValue: '',
    commentShow: false,
    TabCur: 0,
    hotTags: [
      "每日一笑",
      "火影忍者疾风传",
      "八卦设",
      "有说有笑",
      "设计狮",
      "工程狮",
      "吃鸡大队",
      "吃鸡二队",
    ],
    tabList: [
      "素材",
      "社群"
    ],
    contentListHeight: '100%'
  },
  onInput(event) {
    const text = event.detail.value; //获取输入框文字
    if(text) {
      this.setData({
        inputValue: text,
      })
    } else {
      this.setData({
        inputValue: text,
        isSearch: false
      })
    }
    
  },
  tabSelect(e) {
    this.setData({
      TabCur: e.currentTarget.dataset.id
    })
  },

  // 返回上一页面
  fanhui: function () {
    wx.navigateBack({
      delta: 1,
    })
  },
  // 搜索
  searchResultMess() {
    this.setData({
      isSearch: true
    }, () => {
      const query = this.createSelectorQuery()
      query.select('#header').boundingClientRect();
      query.select('#selectBar').boundingClientRect();
      query.exec(rect => {
        console.log('rect', rect)
        this.setData({
          contentListHeight: `calc(100vh - ${rect[0].height}px - ${rect[1].height}px)`
        })
      })
    })
    
    const content = this.selectComponent('#content')
    content?.dataQuery(-1, { messkey: this.data.inputValue }, true) 
    const squareList = this.selectComponent('#squarelist')
    squareList?.dataQuery(`${latitude},${longitude}`, 0, 2, {
      search: this.data.inputValue,
      type: 0
    })
  },
  //自动执行
  onLoad: function () {
    
    const that = this
    let data_field = {
      class_id: 0,
      order: 3,
      start: 0,
      end: 6
    }
    http.get('GetCommunity', data_field)
    .then(res => {
      // 考虑缓存一波？
      // res.map(i => {
      //   i.group_create_timeStr = app.getDateDiff((+i.group_create_time) * 1000)
      // })
      // wx.setStorageSync(classId + order + that.data.requestInterface, res)

      const list = res.map(item => {
        const name = item.group_name
        const id = item.id
        return {name, id}
      })
      console.log('searchlist', list)
      that.setData({
        hotTags: list
      })
      
    })
    .catch(err => {
      console.log('获取列表数据出错', err)
    })
  },
  toGroupPage(e) {
    const id = e.currentTarget.dataset.id;
    const posRes = wx.getStorageSync('pos')
    const pos = `${posRes.latitude},${posRes.longitude}`
    wx.navigateTo({
      url: `../../pages/SquareDetail/index?id=${id}&pos=${pos}`,
    })
  },
  // 子组件的打开评论事件监听
  togglecommentCover() {
    this.setData({
      commentShow: !this.data.commentShow
    })
  },

  //页面显示的时候触发
  onShow: function () {
    
  },
})