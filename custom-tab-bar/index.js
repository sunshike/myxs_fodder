const app = getApp()
Component({
  
  data: {
    selected: 0,
    boxShow: false,
    tabbarHidden: false
  },
  
  methods: {
    hiddenBar() {
      console.log('this.data.tabbarHidden', this.data.tabbarHidden)
      this.setData({
        tabbarHidden: !this.data.tabbarHidden
      })
    },
    switchTab(e) {
      this.setData({
        selected: e.currentTarget.dataset.index
      })
      wx.switchTab({
        url: e.currentTarget.dataset.url,
      })
    },
    reset() {
      this.clearAnimation('#release', () => {})
    },
    showList() {
      if(this.data.boxShow) {
        this.animate('#release', [
          { rotate: 45 },
          { rotate: 0 },
        ], 100, () => {  })

        this.animate('.anim1', [
          { translateY: 0 },
          { translateY: 100 },
        ], 200, () => {})
        this.animate('.anim2', [
          { translateY: 0 },
          { translateY: 100 },
        ], 250, () => {})
        this.animate('.anim3', [
          { translateY: 0 },
          { translateY: 100 },
        ], 300, () => {})
        this.animate('.anim4', [
          { translateY: 0 },
          { translateY: 100 },
        ], 350, () => {})
        this.animate('.anim5', [
          { translateY: 0 },
          { translateY: 100 },
        ], 400, () => {
          this.setData({
            boxShow: !this.data.boxShow
          })
        })
      } else {
        this.animate('#release', [
          { rotate: 0 },
          { rotate: 45 },
        ], 100, () => {  })

        this.animate('.anim1', [
          { translateY: 100 },
          { translateY: 0 },
        ], 200, () => {})
        this.animate('.anim2', [
          { translateY: 100 },
          { translateY: 0 },
        ], 250, () => {})
        this.animate('.anim3', [
          { translateY: 100 },
          { translateY: 0 },
        ], 300, () => {})
        this.animate('.anim4', [
          { translateY: 100 },
          { translateY: 0 },
        ], 350, () => {})
        this.animate('.anim5', [
          { translateY: 100 },
          { translateY: 0 },
        ], 400, () => {})

        this.setData({
          boxShow: !this.data.boxShow
        })
      }
    },
    toRelease(e) {
      if(!app.globalData.member.memberName) {
        wx.navigateTo({
          url: '/myxs_fodder/pages/login/login',
        })
      }
      if(e.currentTarget.dataset.url === "") {
        wx.showToast({
          title: '暂未开发',
          icon: 'none'
        })
        return
      }
      wx.navigateTo({
        url: e.currentTarget.dataset.url,
      })
    },
  }
})