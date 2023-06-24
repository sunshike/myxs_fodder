// myxs_fodder/pages/MyRelease/MyRelease.js
//index.js
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
var urlCommon = /.*\/attachment\/([0-9]*$)/;
var app = getApp();
Page({
  data: {
    leng2: wx.getSystemInfoSync().windowWidth / 3,
    photoWidth: wx.getSystemInfoSync().windowWidth / 5,
    dataNull: 2,
    judi: wx.getSystemInfoSync().windowHeight - 80,
    totalCount: 0, 
    isEmpty: true,
    commentShow: false, // 评论出现时遮罩
    contentListHeight: '100%',
    barHeight: app.globalData.CustomBar,
  }, 
  //自动执行
  onLoad: function() {
    const query = this.createSelectorQuery()
    query.select('#header').boundingClientRect();
    query.select('#selectBar').boundingClientRect();
    query.exec(rect => {
      console.log(rect) 
      this.setData({
        contentListHeight: `calc(100vh - ${rect[0].height}px - ${rect[1].height}px)`
      })
    })

    var menuList = app.globalData.class; //菜单列表（数组）
    var menuFirst = this.data.menubl; //默认菜单id
    var menuNow = 0; //加载数据用菜单id
    if (!menuFirst) { //判断首次加载菜单id是否为空
      menuNow = menuList[0].class_id; //如果为空就默认选择菜单列表中的第一个id
    } else {
      menuNow = menuFirst; //不为空则菜单id为用户选择菜单id
    }
    wx.showShareMenu({
      withShareTicket: true,
    })
    var that = this;
    that.data.menuNow = menuNow;
    that.data.totalCount = 0;
    this.categoryDataQuery(menuNow); //分类加载   传入菜单id  用户点击菜单后即使刷新也是留在本菜单内

    const content = this.selectComponent('#content')
    content.dataQuery(1, {type: "my_content"}, true)
  },
  //页面显示的时候触发
  onShow: function() {
    
    
  },
  // 返回上一页
  ReturnNew: function() {
    wx.navigateBack({
      delta: 1
    })
  },
  //分类 选择
  scrollSelect: function(e) {
    this.data.totalCount = 0;
    this.data.isEmpty = true;
    this.data.catname = "";

    this.setData({
      menubl: e.currentTarget.id,
      menuleft: e.currentTarget.offsetLeft - 100,
      class_id: e.currentTarget.id
    })
    console.log('scrollSelect id:', e.currentTarget.id)
    const listContent = this.selectComponent('#content');
    listContent.dataQuery(e.currentTarget.id, {type: "my_content"}, true)
  },
  // 子组件的打开评论事件监听
  togglecommentCover() {
    this.setData({
      commentShow: !this.data.commentShow
    })
  },
  //分类数据加载
  categoryDataQuery: function(menuNow) {
    const that = this;
    this.setData({
      menulist: app.globalData.class,
      menubl: app.globalData.class[0].class_id != menuNow ? menuNow : app.globalData.class[0].class_id,
      class_id: app.globalData.class[0].class_id != menuNow ? menuNow : app.globalData.class[0].class_id,
    });
    // this.dataQuery(app.globalData.class[0].class_id != menuNow ? menuNow : app.globalData.class[0].class_id)


  },
})