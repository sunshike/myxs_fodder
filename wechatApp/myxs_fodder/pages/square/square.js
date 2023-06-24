// 引入地图SDK核心类
var QQMapWX = require('../../dist/qqmap-wx-jssdk.js');
import http from '../../utils/request.js';
var qqmapsdk;
const app = getApp()
Page({

  data: {
    address: '正在获取位置信息',
    orderList: [
      {
        class_name: '默认排序',
        checked: true
      },
      {
        class_name: '离我最近',
      },
      {
        class_name: '最新创建',
      },
      {
        class_name: '人气最高',
      },
    ],
    classifyList: [{
        title: '默认分类',
        checked: true
      },
    ],
    classifyShow: false,
    orderShow: false,
    orderShowValue: '默认排序', // 排序默认显示文字
    classifyShowValue: '默认分类', // 分类按钮显示文字
    squareListHeight: '0',
    tabbarHeight: app.globalData.CustomBar,
  },
  
  onLoad: function (options) {

    const query = this.createSelectorQuery()
    query.select('#header').boundingClientRect(rect => {
      this.setData({
        squareListHeight: `calc(100vh - ${rect.height}px - calc(100rpx + env(safe-area-inset-bottom) / 2))`
      })
    }).exec();

    // 实例化地图API核心类
    qqmapsdk = new QQMapWX({
      key: app.globalData.map_key
    });




    var _this = this;
    wx.getLocation({
      success: posRes => {
        http.get('GetCommunityClass')
          .then(res => {
            res.class.unshift({
              class_name: '默认分类',
              checked: true,
              id: 0
            })
            this.setData({
              classifyList: res.class,
              classifyShowValue: res.class[0].class_name
            })
            return res.class
          })
          .then(classifyRes => {
            wx.setStorage({
              data: {
                latitude: posRes.latitude,
                longitude: posRes.longitude,
                classifyId: classifyRes[0].id
              },
              key: 'pos',
            })
            this.pos = `${posRes.latitude},${posRes.longitude}`
            this.classifyId = classifyRes[0].id
            const squarelist = this.selectComponent('#squarelist')
            squarelist.dataQuery(`${posRes.latitude},${posRes.longitude}`, classifyRes[0].id, 0)
          })
          .catch(err => {
            console.log('获取列表数据出现错误', err)
          })
        qqmapsdk.reverseGeocoder({
          location: `${posRes.latitude},${posRes.longitude}` || '', //获取表单传入的位置坐标,不填默认当前位置,示例为string格式
          success: function (res) {
            var res = res.result;
            _this.setData({
              address: res.address_component.city?.substr(0, res.address_component.city.length - 1),
            })
          },
          fail: function (error) {
            console.error('解析地址出错', error);
          },
        })
      },
    })

    
    
  },
  onShareAppMessage: function (e) {
    return {}
  },
  onShareTimeline: function(e) {
    return {}
  },
  onHide() {
    // 关闭发布菜单
    this.getTabBar().setData({
      boxShow: false
    })
    this.getTabBar().reset()
  },

  onShow: function () {
    
    // 针对不同实例tabbar 设置当前switchtab index
    this.getTabBar().setData({
      selected: 0,
    })

    
    
  },

  // 打开分类窗口
  openClassify() {
    this.setData({
      classifyShow: !this.data.classifyShow,
      orderShow: false
    })
  },
  // 排序
  openOrder() {
    this.setData({
      classifyShow: false,
      orderShow: !this.data.orderShow
    })
  },
  // 搜索
  toSearch() {
    wx.navigateTo({
      url: '../search/search',
    })
  },
  // 分类类别点击
  onClassifyClick(e) {
    const index = e.currentTarget.dataset.index
    let classifyList = this.data.classifyList
    classifyList.forEach(item => item.checked = false)
    classifyList[index].checked = true
    this.classifyId = classifyList[index].id
    const squarelist = this.selectComponent('#squarelist')
    squarelist.dataQuery(this.pos, classifyList[index].id, this.data.distanceSort ? 0 : 1)
    this.setData({
      "classifyList": classifyList,
      classifyShowValue: classifyList[index].class_name,
      classifyShow: false
    })
  },
  // 排序类别项点击
  onOrderItemClick(e) {
    const index = e.currentTarget.dataset.index
    let orderList = this.data.orderList
    orderList.forEach(item => item.checked = false)
    orderList[index].checked = true
    this.orderListId = index
    const squarelist = this.selectComponent('#squarelist')
    squarelist.dataQuery(this.pos, this.classifyId, index)
    this.setData({
      "orderList": orderList,
      orderShowValue: orderList[index].class_name,
      orderShow: false
    }) 
  },
  // 列表下拉刷新
  onListRefresh() {
    const squarelist = this.selectComponent('#squarelist')
    squarelist.dataQuery(this.pos, this.classifyId, this.orderListId)
  },

})