//index.js
import http from '../../utils/request.js'; 
const app = getApp();
const adUnitId = 'adunit-37f744dedb423770'
let defaultCatName = '' // 提交时的 title
let defaultLeftCatName = ''
Page({ 
  data: {  
    commentShow: false,
    sortBarOpen: false,
    loadingTitle: '正在加载',
    isLoadMore: true,
    categoryBoxShow: false,
    isDownload: false, 
    isAdmin: 0,
    tabbarHeight: app.globalData.CustomBar,
    isChange: false,
    categroryList: [], 
    categroryChildrenObject: {}, //  字典数据
    leftCatId: 1, // 左边的ID
    activeCatName: '', // 选着时的 title
    catname: '',
    contentListHeight: '100%',
  }, 
  onHide() {
    this.getTabBar().setData({
      boxShow: false
    })
    this.getTabBar().reset()
  },
  //页面显示的时候触发
  onShow: function() {
    this.getTabBar().setData({
      selected: 1,
    })
    this.setData({
      releaseCoverShow: false
    })
    const content = this.selectComponent('#content')
    if(app.globalData.releaseBack) content.dataQuery(1, {}, true)
    app.globalData.releaseBack = false
  },
  
  // 页面初始化
  onLoad: function() {
    const query = this.createSelectorQuery()
    query.select('#header').boundingClientRect();
    query.select('#selectBar').boundingClientRect();
    query.exec(rect => {
      console.log(rect) 
      this.setData({
        contentListHeight: `calc(100vh - ${rect[0].height}px - ${rect[1].height}px - calc(100rpx + env(safe-area-inset-bottom) / 2))`
      })
    })

    const that = this;
    // 内容列表组件开始加载数据
    const content = this.selectComponent('#content')
    content.dataQuery(app.globalData.class[0].class_id, {}, true)
    
    // 分类组件开始加载数据
    const tempArr = JSON.parse(JSON.stringify(app.globalData.class))
    const categoryNav = this.selectComponent('#categoryNav')
    categoryNav.requestData(tempArr[0].class_id, 0)

    
    // 排序初始化
    let menuList = app.globalData.class; //菜单列表（数组）
    let menuFirst = this.data.menubl; //默认菜单id
    let menuNow = 0; //加载数据用菜单id
    if (!menuFirst) { //判断首次加载菜单id是否为空
      menuNow = menuList[0].class_id; //如果为空就默认选择菜单列表中的第一个id
    } else {
      menuNow = menuFirst; //不为空则菜单id为用户选择菜单id
    }

    const week = "周" + "日一二三四五六".charAt(new Date().getDay());
    const day = new Date().getDate();
    
    this.fetchCategory(); //排序

    let daySignFlag = 0
    wx.getStorage({
      key: 'Day',
      success: function(res) {
        let date = new Date().getDate();
        if ((res.data == '') || (res.data < date)) {
          daySignFlag = 1
        }
      },
      complete: function(e) {
        if (e.errMsg == 'getStorage:fail data not found') {
          daySignFlag = 1
        }
      }
    })
    that.setData({
      week: week,
      day: day,
      dot: daySignFlag,
      tatle: app.globalData.title,
      topimg: app.globalData.topimg,
      name: app.globalData.member.memberName,
      isAdmin: app.globalData.member.memberAdmin,
      isday: app.globalData.day_sign_status,
      menulist: app.globalData.class,
      menubl: app.globalData.class[0].class_id != menuNow ? menuNow : app.globalData.class[0].class_id,
      class_id: app.globalData.class[0].class_id != menuNow ? menuNow : app.globalData.class[0].class_id,
    })

    wx.showShareMenu({
      withShareTicket: true,
      menus: ['shareAppMessage', 'shareTimeline']
    })
  },
  // 打开分类栏
  toggleCategoryBox: function() {
    this.setData({
      categoryBoxShow: !this.data.categoryBoxShow,
      sortBarOpen: false,
      isChange: false,
    })
  },
  // 打开排序栏 
  openSortBar: function() {
    this.setData({
      sortBarOpen: !this.data.sortBarOpen,
      isChange: !this.data.isChange,
      categoryBoxShow: false
    })
  },
  //排序选择事件
  scrollSelect: function(e) {
    this.data.catname = "";

    this.setData({
      menubl: e.currentTarget.id,
      menuleft: e.currentTarget.offsetLeft - 100,
      class_id: e.currentTarget.id
    })
    const listContent = this.selectComponent('#content');
    listContent.dataQuery(e.currentTarget.id, {}, true)
  },

  
  fetchCategory: function() {
    const that = this
    let categoryData = [{
        "id": 1,
        "catName": "默认排序",
        "children": [{
          "id": 1001,
          "catName": "降序"
        }, {
          "id": 1002,
          "catName": "升序"
        }]
      },
      {
        "id": 2,
        "catName": "热度排序",
        "children": [{
          "id": 2001,
          "catName": "收藏数量"
        }, {
          "id": 2002,
          "catName": "下载数量"
        }, {
          "id": 2003,
          "catName": "转发数量"
        }]
      },
      {
        "id": 3,
        "catName": "专属排序",
        "children": []
      }
    ]
    http.get('Grouping')
    .then(data => {
      if (data.length === 0) {
        categoryData[2].children.push({
          id: 10086,
          catName: "暂无分组"
        })
      } else {
        for (let key in data) {
          categoryData[2].children.push({
            id: parseInt(data[key].grouping_id),
            catName: data[key].grouping_name
          })
        }
      }

      // 获取分类数据
      let catList = [],
        catChildrenObject = {}
        categoryData.map((item, index) => {
          catList.push({
            id: item.id,
            catName: item.catName
          })
          catChildrenObject[item.id] = item
        })
      // 默认title 和 激活的id 
      let rightActive = catChildrenObject[catList[0].id].children[0]
      let leftActive = catChildrenObject[catList[0].id].catName;
      defaultCatName = rightActive.catName,
      defaultLeftCatName = leftActive,
      that.setData({
        categroryList: catList,
        categroryChildrenObject: catChildrenObject,
        leftCatId: catList[0].id, // 左边的ID
      })
    })

  },


  leftCatId: function(e) {
    let leftCatId = e.currentTarget.dataset.id
    let activeCatName = this.data.categroryChildrenObject[leftCatId].children[0].catName
    defaultLeftCatName = this.data.categroryList[leftCatId - 1].catName
    this.setData({
      leftCatId: leftCatId,
      activeCatName: activeCatName
    })
  },

  // 右边选择 id 当前激活的 name 
  selectCatId: function(e) {
    let catName = e.currentTarget.dataset.name
    let catId = e.currentTarget.dataset.id
    this.setData({
      catId: catId,
      sortBarOpen: !this.data.sortBarOpen,
      activeCatName: catName
    })
  },

  // 确认提交 当前选中的 name 
  submitSelect: function(e) {
    defaultCatName = this.data.activeCatName,
    this.setData({
      isChange: false
    })
    const activeCatName = this.data.activeCatName;
    const menuNow = this.data.menubl;

    this.sortMess(menuNow, activeCatName)
  },
  //排序内容加载
  sortMess: function(id, catname) {
    const data = {
      catname: catname,
      catId: this.data.catId,
      defaultLeftCatName: defaultLeftCatName,
    }
    const content = this.selectComponent('#content')
    content.dataQuery(this.data.class_id, data, true)
  },
  // 跳转至搜索页面
  GoToSearchPage: function() {
    wx.navigateTo({
      url: '../search/search',
    })
  },
  


  //日签入口
  Daysign: function(e) {
    const that = this;
    const date = new Date().getDate();

    wx.setStorage({
      key: 'Day',
      data: date,
    })
    that.setData({
      dot: 0
    })
    wx.navigateTo({
      url: '../Daysign/Daysign',
    })
  },

  // 子组件的打开评论事件监听
  togglecommentCover() {
    this.setData({
      commentShow: !this.data.commentShow
    })
  },

  closeAll() {
    this.setData({
      categoryBoxShow: false,
      isChange: false,
      sortBarOpen: false,
    })
  },
  
})