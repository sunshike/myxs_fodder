// myxs_fodder/components/CommentItem/commentItem.js
import http from '../../utils/request.js'; 

Component({
  /**
   * 组件的属性列表
   */
  properties: {
    parentId: {
      type: String,
      value: '-1'
    },
    replyChild: {
      type: Boolean,
      value: false
    },
    replyChildName: {
      type: String,
      value: ''
    },
    userId: {
      type: String,
      value: '-1'
    },
    discuessId: {
      type: String,
      value: '-1'
    },
    commentId: {
      type: String,
      value: '-1'
    },
    isChild: {
      type: Boolean,
      value: false
    },
    content: {
      type: String,
      value: ''
    },
    // 是否是自己发布的评论
    isDel: {
      type: Boolean,
      value: false
    },
    isAdmin: {
      type: Boolean,
      value: false
    },
    likeNum: {
      type: String,
      value: '0'
    },
    hasLike: {
      type: Boolean,
      value: false
    },
    date: {
      type: String,
      value: '1-1'
    },
    avatar: {
      type: String,
      value: ''
    },
    name: {
      type: String,
      value: ''
    },
    replyName: {
      type: String,
      value: ''
    },
  },
  
  options: {
    styleIsolation: 'shared'
  },
  /**
   * 组件的初始数据
   */
  data: {
    
  },

  /**
   * 组件的方法列表
   */
  methods: {
    openOption() {
      const that = this
      let btnList = ['复制']
      if(this.data.isDel) btnList.push('删除')
      wx.showActionSheet({
        itemList: btnList,
        success (res) {
          if(btnList[res.tapIndex] === '删除') {
            that.onDel()
          } else {
            wx.setClipboardData({
              data: that.data.content,
            })
          }
          
        },
        fail (res) {
          console.log(res.errMsg)
        }
      })
    },
    onReply() {
      const data = {
        discuss_id: this.data.discuessId, 
        member_id: this.data.userId, 
        userName: this.data.name,
        isChild: this.data.isChild,
        parentId: this.data.parentId,
        idArr: [this.data.discuessId],
      }
      this.triggerEvent("changeHint", data)
    },
    onLike() {
      const data = {
        content_id: this.data.commentId,
        discuss_id: this.data.discuessId,
        parent_id: this.data.parentId,
        isChild: this.data.isChild,
      }
      this.triggerEvent('like', data)
      http.post('DiscussLike', data)
      .then(res => {
        console.log(res)
      })
      .catch(err => {
        console.log(err)
      })
    },
    onDel() {
      const data = {
        discuss_id: this.data.discuessId,
        parentId: this.data.parentId,
        isChild: this.data.isChild,
      }
      this.triggerEvent('del', data)
      http.post('DelteDiscuss', data)
      .then(res => {
        console.log(res)
      })
      .catch(err => {
        console.log(err)
      })
    },


    
  }
})
