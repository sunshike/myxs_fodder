import http from '../../utils/request.js';
const app = getApp()
let rewardedVideoAd = null;
const {
  latitude,
  longitude
} = wx.getStorageSync('pos')
Page({
  data: {
    contentListHeight: '100%',
    commentShow: false,
    selectId: 2,
    cardCur: 0,
    DotStyle: true,
    swiperList: [],
    showSwiper: true,
    noticeTitle: '',
    noticeTime: '',
    noticeMessage: '',
    menulist: [{
        id: 2,
        name: '素材'
      },
      {
        id: 3,
        name: '社群'
      },
    ]
  },
  togglecommentCover() {
    this.setData({
      commentShow: !this.data.commentShow
    })
  },
  scrollSelect(e) {
    const that = this
    this.setData({
      selectId: e.currentTarget.id
    }, () => {
      if(e.currentTarget.id == 2) {
        const listContent = that.selectComponent('#content');
        listContent?.dataQuery(e.currentTarget.id, {}, true)
      }else {
        const squareList = that.selectComponent('#squarelist')
        squareList?.dataQuery(`${latitude},${longitude}`, 0, 2)
      }
    })
    
  },
  onNotice() {
    this.setData({
      modalName: 'Modal'
    })
  },
  hideModal() {
    this.setData({
      modalName: ''
    })
  },
  onShow: function () {
    this.getTabBar().setData({
      selected: 2
    })
    this.setData({
      releaseCoverShow: false
    })
  },
  onHide() {
    this.getTabBar().setData({
      boxShow: false
    })
    this.getTabBar().reset()
    const noticeBar = this.selectComponent('#noticeBar')
    noticeBar.destroyTimer()
  },
  onShareAppMessage: function (e) {
    return {}
  },
  onShareTimeline: function(e) {
    return {}
  },
  onLoad() {

    const query = this.createSelectorQuery()
    query.select('#swiper').boundingClientRect();
    query.select('#selectBar').boundingClientRect();
    query.select('#noticeBar').boundingClientRect();
    query.exec(rect => {
      console.log(rect) 
      this.setData({
        contentListHeight: `calc(100vh - ${rect[0].height}px - ${rect[1].height}px - ${rect[2].height}px - calc(100rpx + env(safe-area-inset-bottom) / 2))`
      })
    })

    const that = this
    const content = this.selectComponent('#content')
    content.dataQuery(1, {}, true)



    // if (wx.createRewardedVideoAd) {
    //     rewardedVideoAd = wx.createRewardedVideoAd({
    //       adUnitId: app.globalData.videoAdId
    //     })
    //     console.log("rewardedVideoAd", rewardedVideoAd);
    //     rewardedVideoAd.onLoad(() => {
    //       console.log("激励视频创建完成")
    //     })
    //     rewardedVideoAd.onError((err) => {
    //       console.log('监听激励视频错误事件', err)
    //     })
    //     rewardedVideoAd.onClose((res) => {
    //       if (res.isEnded) {
    //         console.log('广告视频播放完成,开始下载')
    //         // 下载
    //         app.globalData.member_water ? content.downImg(downloadIndex, downloadId, true) : content.downImg(downloadIndex, downloadId, false)
    //       } else {
    //         console.log('广告视频未播放完成,显示提示弹窗')
    //         wx.showModal({
    //           title: "下载失败",
    //           content: "积分不足，下载失败",
    //           showCancel: false,
    //           success(res) {
    //             if (res.confirm) {
    //               console.log('跳转到积分页面')
    //               wx.navigateTo({
    //                 url: '../../pages/integral/integral',
    //               })
    //             }
    //           }
    //         })
    //       }
    //     })
    //   }



    let data = {
      shuffling_position: "found"
    }

    http.get('GetShuffling', data)
      .then(res => {
        if (res.content.length === 0) {
          that.setData({
            showSwiper: false,
          })
          return
        }
        let list = res.content.map((item, index) => {
          return {
            id: index,
            type: 'image',
            url: item
          }
        })
        that.setData({
          swiperList: list,
        })
        that.towerSwiper('swiperList');
      })
    http.get('GetNotice')
      .then(res => {
        that.setData({
          noticeTitle: res.title,
          noticeTime: res.time,
          noticeMessage: res.content
        })
      })



  },

  DotStyle(e) {
    this.setData({
      DotStyle: e.detail.value
    })
  },
  // towerSwiper
  // 初始化towerSwiper
  towerSwiper(name) {
    let list = this.data[name];
    for (let i = 0; i < list.length; i++) {
      list[i].zIndex = parseInt(list.length / 2) + 1 - Math.abs(i - parseInt(list.length / 2))
      list[i].mLeft = i - parseInt(list.length / 2)
    }
    this.setData({
      swiperList: list
    })
  },
  // towerSwiper触摸开始
  towerStart(e) {
    this.setData({
      towerStart: e.touches[0].pageX
    })
  },
  // towerSwiper计算方向
  towerMove(e) {
    this.setData({
      direction: e.touches[0].pageX - this.data.towerStart > 0 ? 'right' : 'left'
    })
  },
  // towerSwiper计算滚动
  towerEnd(e) {
    let direction = this.data.direction;
    let list = this.data.swiperList;
    if (direction == 'right') {
      let mLeft = list[0].mLeft;
      let zIndex = list[0].zIndex;
      for (let i = 1; i < list.length; i++) {
        list[i - 1].mLeft = list[i].mLeft
        list[i - 1].zIndex = list[i].zIndex
      }
      list[list.length - 1].mLeft = mLeft;
      list[list.length - 1].zIndex = zIndex;
      this.setData({
        swiperList: list
      })
    } else {
      let mLeft = list[list.length - 1].mLeft;
      let zIndex = list[list.length - 1].zIndex;
      for (let i = list.length - 1; i > 0; i--) {
        list[i].mLeft = list[i - 1].mLeft
        list[i].zIndex = list[i - 1].zIndex
      }
      list[0].mLeft = mLeft;
      list[0].zIndex = zIndex;
      this.setData({
        swiperList: list
      })
    }
  }
})