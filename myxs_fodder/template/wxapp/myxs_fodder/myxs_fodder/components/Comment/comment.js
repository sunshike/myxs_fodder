import http from '../../utils/request.js'; 
import datas from "../../dist/emojis.js"
import loadsh from '../../utils/util.js'
const { $Toast } = require('../../iview_ui/base/index');
const app = getApp()

let isReply = false;
let replyData = {}
let parentLoadStart = 0
let loadEndNum = 10
let loadStart = 2
let loadEnd = 5
let loadMoreId = '-1'

Component({
  /**
   * 组件的属性列表
   */
  properties: {
    isDetail: {
      type: Boolean,
      value: false
    },
    show: {
      type: Boolean,
      value: false
    },
    commentId: {
      type: String, 
      value: ''
    }
  },
  options: {
    styleIsolation: 'shared'
  },
  lifetimes:{ 
    attached: function() {
      this.secondLoad = true // 解决ios下自定义tabbar层级过高问题
    },
  },
  observers:{ 
    'show'(data) { 
      if(this.secondLoad) this.getTabBar()?.hiddenBar() // 隐藏自定义tabbar
      
      if(!data) {
        parentLoadStart = 0
        loadStart = 2
        isReply = false
        this.parentLoadEnd = false,
        this.setData({
          hint: '评论',
          inputValue: '',
          dataList: [],
          isLoading: true,
          loadingTip: '加载中',
          totalComment: 0,
        })
      }
      
      const that = this;
      if(data) {
        http.get('GetDiscuss', { content_id: that.data.commentId, start: parentLoadStart, end: loadEndNum })
        .then( res => {
          res.list.forEach(item => {
            item.create_time = that.getDateDiff((+item.create_time) * 1000)
            item.total = +item.total
            if(item.down.length > 0) {
              item.down.forEach(child => {
                child.create_time = that.getDateDiff((+child.create_time) * 1000)
              })
            }
          })
          console.log('评论数据', res.list)
          parentLoadStart += 10
          that.triggerEvent('getcommentnum', {num: +res.count})
          that.setData({
            ["dataList"]: that.convertEmojis(res.list),
            totalComment: +res.count,
            loadingTip: '暂无数据',
            isLoading: false,
          })
        })
      }
    }
  },
  /**
   * 组件的初始数据
   */
  data: {
    inputBottom: 0,  // 评论输入框高度
    dataList: [],
    totalComment: 0,
    hint: '评论',
    inputValue: '',
    isfocus: false,
    
    isLoading: true,
    loadingTip: '加载中',
    loadMoreLoading: false,
    loadMoreLoadingId: '-1',

    emjoisHide: true, // 是否隐藏表情选择框
  },

  /**
   * 组件的方法列表
   */
  methods: {
    // 子评论加载更多
    loadMore(e) {
      this.setData({
        loadMoreLoading: true,
        loadMoreLoadingId: e.currentTarget.dataset.id
      })
      if(e.currentTarget.dataset.id != loadMoreId) loadStart = 2
      console.log('loadStart', loadStart)
      const that = this
      const parentIndex = e.currentTarget.dataset.parentindex
      let dataList = this.data.dataList 
      let index = dataList[parentIndex].down.length
      const data = {
        content_id: this.data.commentId,
        id: e.currentTarget.dataset.id,
        start: loadStart,
        end: loadEnd
      }
      http.post('GetMoreDiscuss', data)
      .then(res => {
        res.list.forEach(item => {
          item.create_time = app.getDateDiff((+item.create_time) * 1000)
          dataList[parentIndex].down[index++] = item
        })

        loadMoreId = e.currentTarget.dataset.id
        loadStart += 5
        that.setData({
          loadMoreLoading: false,
          loadMoreLoadingId: '-1',
          [`dataList[${parentIndex}].down`]: that.convertEmojis(dataList[parentIndex].down)
        })
      })
      .catch(err => {
        console.log(err)
      })
    },
    onParentLoadMore: loadsh.throttle(function() {
      if(this.parentLoadEnd) return
      const that = this;
      http.get('GetDiscuss', { content_id: that.data.commentId, start: parentLoadStart, end: loadEndNum })
        .then( res => {
          if(res.list.length === 0) { 
            that.parentLoadEnd = true
            return
          }
          res.list.forEach(item => {
            item.create_time = that.getDateDiff((+item.create_time) * 1000)
            item.total = +item.total
            if(item.down.length > 0) {
              item.down.forEach(child => {
                child.create_time = that.getDateDiff((+child.create_time) * 1000)
              })
            }
          })
          console.log('更多评论数据', res)
          parentLoadStart += 10
          that.setData({
            ["dataList"]: that.convertEmojis(that.data.dataList.concat(res.list))
          })
        })
    }, 2000),
    changeHint(e) {
      replyData = e.detail
      if(replyData.isChild) replyData.idArr.unshift(replyData.parentId)
      replyData.idArr = replyData.idArr.join(',');
      replyData.isChild = replyData.isChild ? 1 : 0
      
      isReply = true
      this.setData({
        hint: `回复：${replyData.userName}`,
        isfocus: true
      })
    },
    toggleComment() {
      this.closeEmoijs()
      this.triggerEvent("hide")
    },
    submitComment(e) {
      console.log('isReply', isReply)
      const that = this
      let dataList = this.data.dataList
      if(!this.data.inputValue) {
        $Toast({
          content: '内容不能为空',
          type: 'warning'
        });
        return
      }
      let postName = "Discuss"
      let datas = {
        content_id: this.data.commentId,
        discuss_mess: this.data.inputValue
      }
      replyData.idArr = replyData.idArr ?? 0
      if(isReply) {
        Object.assign(datas, replyData)
        postName = 'DiscussReply'
      }
      this.setData({
        hint: '评论',
        inputValue: '',
        inputBottom: 0,
        emjoisHide: true,
      })
      http.post(postName, datas)
      .then(res => {
        console.log('评论结果', res)
        if(res.status === 0) {
          $Toast({
            content: res.msg,
            type: 'warning'
          });
          return
        }
        that.triggerEvent("addcomment")
        res.data.create_time = app.getDateDiff(res.data.create_time * 1000)

        console.log('回复', res.data)
        if(!isReply) {
          dataList.unshift(that.convertEmojis(res.data))
          
          that.setData({
            ["dataList"]: dataList,
            totalComment: that.data.totalComment + 1
          })
        }else {
          const index = replyData.isChild === 1 ? dataList.findIndex(item => item.id === replyData.parentId) : dataList.findIndex(item => item.id === replyData.discuss_id)
          if(replyData.isChild === 1) res.data.discuss_name = dataList[index].down.find(item => item.id == res.data.discuss_id).member_nickname
          dataList[index].down.push(that.convertEmojis(res.data))
          
          that.setData({
            [`dataList[${index}].down`]: dataList[index].down,
            totalComment: that.data.totalComment + 1
          })
        }
        
        isReply = false
      })
      .catch(err => {
        console.log(err)
      })
      
    },
    getDateDiff(dateTimeStamp) {
      var result;
      var minute = 1000 * 60;
      var hour = minute * 60;
      var day = hour * 24;
      var halfamonth = day * 15;
      var month = day * 30;
      var now = new Date().getTime();
      var date = new Date(dateTimeStamp)
      var diffValue = now - dateTimeStamp;
      if (diffValue < 0) {
        return;
      }
      var monthC = diffValue / month;
      var weekC = diffValue / (7 * day);
      var dayC = diffValue / day;
      var hourC = diffValue / hour;
      var minC = diffValue / minute;
      if (dayC >= 1) {
        if (parseInt(dayC) < 2) {
          result = "昨天";
        } else {
          var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1)
          var D = date.getDate() < 10 ? '0' + date.getDate() : date.getDate()
          var h = date.getHours();
          var m = date.getMinutes();
          var s = date.getSeconds();
          result = M + '月' + D + '日';
        }
      } else if (hourC >= 1) {
        result = "" + parseInt(hourC) + "小时前";
      } else if (minC >= 1) {
        result = "" + parseInt(minC) + "分钟前";
      } else {
        result = "刚刚";
      }
      return result;
    },
    onInput(e) {
      this.setData({
        inputValue: e.detail.value
      })
    },
    onLike(e) {
      let index = -1
      let arr = this.data.dataList
      let flag = false
      if(e.detail.isChild) {
        const parentIndex = arr.findIndex(item => item.id === e.detail.parent_id)
        index = arr[parentIndex].down.findIndex(item => item.id === e.detail.discuss_id)
        flag = arr[parentIndex].down[index].is_like
        const num = +arr[parentIndex].down[index].discuss_likenum
        this.setData({
          [`dataList[${parentIndex}].down[${index}].is_like`]: !flag,
          [`dataList[${parentIndex}].down[${index}].discuss_likenum`]: flag ? num -1 : num + 1,
        })
      } else {
        index = arr.findIndex(item => item.id === e.detail.discuss_id)
        flag = arr[index].is_like
        const num = +arr[index].discuss_likenum
        this.setData({
          [`dataList[${index}].is_like`]: !flag,
          [`dataList[${index}].discuss_likenum`]: flag ? num -1 : num + 1,
        })
      }
    },
    onDel(e) {
      
      let index = -1
      let dataList = this.data.dataList
      let delNum = 0
      if(e.detail.isChild) {
        const parentIndex = dataList.findIndex(item => item.id === e.detail.parentId)
        index = dataList[parentIndex].down.findIndex(item => item.id === e.detail.discuss_id)
        dataList[parentIndex].down.splice(index, 1)
        delNum += 1
      } else {
        index = dataList.findIndex(item => item.id === e.detail.discuss_id)
        delNum += (dataList[index].total + 1)
        dataList.splice(index, 1)
        
      }
      this.triggerEvent("delcomment", { num: delNum })
      this.setData({
        ["dataList"]: dataList,
        totalComment: this.data.totalComment - delNum
      })
    },
    // 键盘高度发生变化
    onkeyboard(e) {
      console.log('键盘高度', e.detail.height)
      this.setData({
        inputBottom: e.detail.height + 50
      })
    },
    // 点击输入框
    onFocus() {
      this.setData({
        emjoisHide: true,
      })
    },
    openEmojis() {
      const that = this
      const flag = this.data.emjoisHide
      if(flag) {
        wx.hideKeyboard({
          complete: res => {
            that.setData({
              emjoisHide: false,
              inputBottom: 220,
            })
          }
        })
      } else {
        that.setData({
          emjoisHide: !this.data.emjoisHide,
          inputBottom: flag ? 220 : 0,
        })
      }
      
    },
    // 点击添加表情
    addemoijs(e) {
      this.setData({
        inputValue: this.data.inputValue + e.detail.item.value
      })
      console.log('添加表情', e.detail.item)
    },
    // 关闭表情框
    closeEmoijs() {
      this.setData({
        emjoisHide: true,
        inputBottom: 0
      })
    },
    convertData(emotions, weibo_icon_url) {
      const emojisList = {}
      for (const key in emotions) {
        if (emotions.hasOwnProperty(key)) {
          const ele = emotions[key];
          for (const item of ele) {
            emojisList[item.value] = {
              id: item.id,
              value: item.value,
              icon: item.icon.replace('/', '_'),
              url: weibo_icon_url + item.icon
            }
          }
        }
      }
      return emojisList
    },
    convertEmojis(data) {
      
      const { emotions, weibo_icon_url } = datas
      const replaceData = this.convertData(emotions, weibo_icon_url)
      let dataArr = [].concat(data)
      
      dataArr.forEach(item => {
        if(item.down?.length) this.convertEmojis(item.down)
        item.content = item?.content?.replace(/\[.*?\]/g, convertContent => {
          return `<img mode="aspectFit" height="17" width="17" src="${replaceData[convertContent]?.url}"></img>`
        })
      })
      
      if(dataArr.length === 1 && (!(data instanceof Array))) return dataArr[0]
      return dataArr
    },




  }
})
