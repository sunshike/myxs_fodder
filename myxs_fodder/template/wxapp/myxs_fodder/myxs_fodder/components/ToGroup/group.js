Component({

  properties: {
    isDetail: {
      type: Boolean,
      value: false
    },
    hidden: {
      type: Boolean,
      value: true
    },
    groupId: {
      type: String,
      value: ''
    }
  },

  methods: {
    // 加入群聊：点击按钮开始
    startmessage() {
      const that = this
      wx.showModal({
        title: '提示',
        content: '邀请消息已发出，请退出小程序查看',
        showCancel: false,
        success (res) {
          if (res.confirm) {
            that.toggleGroup()
          } 
        }
      })
    },
    // 加入群聊：点击按钮结束
    completemessage() {
      
    },
    toggleGroup() {
      this.triggerEvent("hide")
    }
  }
})
