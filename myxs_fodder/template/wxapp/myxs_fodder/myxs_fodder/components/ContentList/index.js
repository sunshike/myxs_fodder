// myxs_fodder/components/ContentList/index.js
import http from '../../utils/request.js';
const app = getApp();
const {
  $Toast
} = require('../../iview_ui/base/index');
// let rewardedVideoAd = null; //激励视频广告变量
let downloadIndex = -1;
let downloadId = -1;
const throttle = (fn, delay) => {
  var startTime = new Date();
  return function () {
    var context = this;
    var args = arguments; 
    var endTime = new Date();
    var resTime = endTime - startTime;
    //判断大于等于我们给的时间采取执行函数;
    if (resTime >= delay) {
      fn.apply(context, args);
      //执行完函数之后重置初始时间，等于最后一次触发的时间
      startTime = endTime;
    }
  }
}

Component({
  /**
   * 组件的属性列表
   */
  properties: {
    height: {
      type: String,
      value: '100%'
    },
    hidden: {
      type: Boolean,
      value: false
    },
    showComment: {
      type: Boolean,
      value: true
    },
    // 判断是否在我的发布页面
    isMyRelease: {
      type: Boolean,
      value: false
    },
    likeBtnShow: {
      type: Boolean,
      value: true
    },
    commentBtnShow: {
      type: Boolean,
      value: true
    },
    class_id: {
      type: String,
      value: '-1'
    },
    requestIntface: {
      type: String,
      value: 'ListContent'
    },
  },
  lifetimes: {
    attached() {
      wx.getSystemInfo({
        success: (res) => {
          let {
            windowHeight
          } = res;
          this.windowHeight = windowHeight;
        }
      })
      
    },
  },

  options: {
    styleIsolation: 'shared'
  },

  data: {
    refreshState: false, // 下拉加载状态
    isLoadMore: true, // 加载更多状态提示
    loadingTitle: '正在加载中',
    commentShow: false, // 是否展示评论
    progress: '0', // 下载进度
    loadModal: true, // 下载弹窗加载隐藏
    beforeVideo: null, // 上一个正在播放的视频

    list: [],
  },

  /**
   * 组件的方法列表
   */
  methods: {
    toDetail(e) {
      this.triggerEvent('todetail', {id: e.currentTarget.dataset.content_id})
    },
    //内容初始化加载
    dataQuery(id, classifyField, isStart) {
      this.canLoadMore = true // 是否执行加载更多请求
      const that = this;
      this.wholePageIndex = 0; // 第几屏的数据
      this.wholeVideoList = []; // 整个数据
      this.currentRenderIndex = 0;
      this.pageHeightArr = []; // 每屏数据的高度

      this.totalCount = 0
      this.queryCount = 3

      this.classifyField = classifyField

      if (isStart) {
        const storageArr = wx.getStorageSync(id + JSON.stringify(classifyField))
        this.messkey = classifyField?.messkey ?? ''
        this.setData({
          ['list']: [storageArr],
          isLoadMore: true, // 加载更多状态提示
          loadingTitle: '正在加载中',
        })
      }
      
      let field = {
        class_id: id,
        start: this.totalCount,
        end: this.queryCount
      }
      if (JSON.stringify(classifyField)) {
        Object.assign(field, classifyField)
        field.start = 0
      }
      http.get(that.data.requestIntface, field)
        .then(data => {
          console.log("列表数据", data.content);
          if (Object.keys(data.content).length <= that.queryCount) {
            that.setData({
              isLoadMore: false,
              loadingTitle: '暂无数据',
            })
          }
          const _list = []
          for (var k in data.content) {
            if (data.content[k].content_id != undefined) {
              data.content[k].isplay = 0;
              data.content[k].rightIconShow = false;
              data.content[k].create_time = app.getDateDiff(data.content[k].create_time * 1000);
              if (data.content[k].text.length > 55) {
                data.content[k].textTypeL = 1;
                data.content[k].textShow = 1;
                data.content[k].ShowBtn = '展开'
              } else {
                data.content[k].textTypeL = 0;
              }
            }
            _list.push(data.content[k])
          }
          
          wx.setStorage({
            data: _list,
            key: id + JSON.stringify(classifyField),
          })
          that.wholeVideoList[that.wholePageIndex] = _list;
          that.setData({
            ['list[' + that.wholePageIndex + ']']: _list
          }, () => {
            that.setHeight();
          })
          that.totalCount = that.totalCount + that.queryCount

        });
    },
    // 下拉加载
    onRefresherrefresh(e) {
      console.log(e)
      setTimeout(() => {
        this.setData({
          refreshState: false
        })
      }, 700);
      
    },
    // 加载更多
    getMore: throttle(function () {
      this.wholePageIndex = this.wholePageIndex + 1;
      const wholePageIndex = this.wholePageIndex;
      this.currentRenderIndex = wholePageIndex;

      if (!this.canLoadMore) return
      const that = this;
      const class_id = that.data.class_id
      const data = {
        type: 'jz',
        class_id: class_id,
        start: that.totalCount,
        end: that.queryCount
      }
      Object.assign(data, this.classifyField)
      if (that.data.isMyRelease) data.type = "my_content"
      if (that.data.requestIntface === "searchMess") data.messkey = that.messkey
      http.get(that.data.requestIntface, data)
        .then(data => {
          if (data.content.length === 0) {
            that.canLoadMore = false
            that.setData({
              loadingTitle: '暂无数据',
              isLoadMore: false
            })
            return
          }
          for (var k in data.content) {
            if (data.content[k].content_id != undefined) {
              data.content[k].isplay = 0;
              data.content[k].rightIconShow = false;
              data.content[k].create_time = app.getDateDiff(data.content[k].create_time * 1000);
              if (data.content[k].text.length > 55) {
                data.content[k].textTypeL = 1;
                data.content[k].textShow = 1;
                data.content[k].ShowBtn = '展开'
              } else {
                data.content[k].textTypeL = 0;
              }
            }
          }
          that.totalCount = that.totalCount + that.queryCount,
          that.wholeVideoList[wholePageIndex] = data.content;
          let datas = {};
          datas['list[' + wholePageIndex + ']'] = data.content
          that.setData(datas, () => {
            that.setHeight();
          })
        });
    }, 800),

    // 计算屏幕高度
    setHeight: function () {
      const that = this;
      const wholePageIndex = this.wholePageIndex;
      this.query = wx.createSelectorQuery().in(this);
      this.query.select(`#wrp_${wholePageIndex}`).boundingClientRect()
      this.query.exec(function (res) {
        that.pageHeightArr[wholePageIndex] = res[0] && res[0].height;
      });
      this.observePage(wholePageIndex);
    },
    observePage: function (pageIndex) {
      const that = this;
      const {
        hasShowAlreadySaw,
        showMoreVideosIcon
      } = this.data;
      const observerObj = wx.createIntersectionObserver(this).relativeToViewport({
        top: 2 * this.windowHeight,
        bottom: 2 * this.windowHeight
      });
      observerObj.observe(`#wrp_${pageIndex}`, (res) => {   
        if (res.intersectionRatio <= 0) {
          that.setData({
            ['list[' + pageIndex + ']']: {
              height: that.pageHeightArr[pageIndex]
            },
          })
        } else {
          that.setData({
            ['list[' + pageIndex + ']']: that.wholeVideoList[pageIndex],
          })
        }
      });
    },

    // 显示我发布的内容
    adminShow: function (e) {
      const that = this;
      const id = e.currentTarget.id
      const i = e.currentTarget.dataset.index
      let pageindex = e.currentTarget.dataset.pageindex;
      const data = {
        type: 'hide',
        content_id: id
      }
      let texts = ''
      if (that.data.list[pageindex][i].content_status === '1') {
        texts = '是否开启隐藏';
      } else {
        texts = '是否开启显示';
      }
      wx.showModal({
        title: '',
        content: texts,
        confirmText: '确定',
        complete: function (e) {
          if (e.confirm) {
            http.post('Content', data)
              .then(data => {
                if (data.status) {
                  if (that.data.list[pageindex][i].content_status === '1') {
                    that.setData({
                      [`list[${pageindex}][${i}].content_status`]: '2'
                    })
                  } else {
                    that.setData({
                      [`list[${pageindex}][${i}].content_status`]: '1'
                    })
                  }
                } else {
                  $Toast({
                    content: '未知错误',
                    type: 'error'
                  });
                }
              });
          }
        }
      })

    },
    onRightIconShow(e) {
      let i = e.currentTarget.dataset.index;
      let pageindex = e.currentTarget.dataset.pageindex;
      this.setData({
        [`list[${pageindex}][${i}].rightIconShow`]: !this.data.list[pageindex][i].rightIconShow,
      })
    },
    toSocialGroupPage(e) {
      const id = e.currentTarget.dataset.id;
      const posRes = wx.getStorageSync('pos')
      const pos = `${posRes.latitude},${posRes.longitude}`
      wx.navigateTo({
        url: `../../pages/SquareDetail/index?id=${id}&pos=${pos}`,
      })
    },
    // 删除我发布的内容
    adminDelete: function (e) {
      const that = this;
      const id = e.currentTarget.id
      const i = e.currentTarget.dataset.index
      let pageindex = e.currentTarget.dataset.pageindex;
      let list = that.data.list[pageindex]
      const data = {
        type: 'delete',
        content_id: id
      }
      wx.showModal({
        title: '',
        content: '是否删除这条内容',
        confirmText: '确定',
        complete: function (e) {
          if (e.confirm) {
            http.post('Content', data)
              .then(data => {
                if (data.status) {
                  list.splice(i, 1);
                  that.setData({
                    [`list[${pageindex}]`]: list
                  })
                } else {
                  $Toast({
                    content: '未知错误',
                    type: 'error'
                  });
                }
              });
          }
        }
      })

    },

    //展开文本
    DataTextTooger(e) {
      let i = e.currentTarget.dataset.l
      let pageindex = e.currentTarget.dataset.pageindex;
      if (this.data.list[pageindex][i].textShow == 0) {
        this.setData({
          [`list[${pageindex}][${i}].textShow`]: 1,
          [`list[${pageindex}][${i}].ShowBtn`]: '展开'
        })
      } else {
        this.setData({
          [`list[${pageindex}][${i}].textShow`]: 0,
          [`list[${pageindex}][${i}].ShowBtn`]: '收起'
        })
      }
    },

    // 删除评论事件
    onDelComment(e) {
      let data = this.data.list[this.data.pageindex]
      const num = +data[this.data.commentindex].discuss - e.detail.num
      this.setData({
        [`list[${this.data.pageindex}][${this.data.commentindex}].discuss`]: num
      })
    },
    // 添加评论事件
    onAddComment() {
      let data = this.data.list[this.data.pageindex]
      const num = +data[this.data.commentindex].discuss
      this.setData({
        [`list[${this.data.pageindex}][${this.data.commentindex}].discuss`]: num + 1
      })
    },
    // 关闭广告
    Closeadvert(e) {
      var that = this;
      var i = e.currentTarget.dataset.i
      let pageindex = e.currentTarget.dataset.pageindex;
      that.setData({
        [`list[${pageindex}][${i}].show`]: 0
      })
    },
    // 复制文字
    TextCopi(e) {
      var that = this;
      var index = e.currentTarget.dataset.index;
      let pageindex = e.currentTarget.dataset.pageindex;
      wx.setClipboardData({
        data: that.data.list[pageindex][index].text,
      })
    },
    
    onScroll(e) {
      if(this.data.videoCurrentId) {
        const that = this
        const query = this.createSelectorQuery()
        query.select(`#video_${this.data.videoCurrentId}`).boundingClientRect()
        query.selectViewport()
        query.exec(function (res) {
          if(((res[0].bottom - e.currentTarget.offsetTop) <= 0) || (((res[0].height + that.windowHeight) - res[0].bottom) <= 0)) {
            that.setData({
              videoCurrentId: ''
            })
          }
        })
      }
      
    },
    videoShow(e) {
      this.setData({
        videoCurrentId: e.currentTarget.dataset.id
      })
    },
    // 捕获广告视频播放错误
    adVideoError(e) {
      if (e.detail.errCode === 3001) {
        console.log('内容视频时长少于一定限制')
      } else if (e.detail.errCode === 3002) {
        console.log('启动一定时间内不能展示广告')
      } else if (e.detail.errCode === 3003) {
        console.log('上次播放广告间隔过短')
      } else {
        console.log('视频广告 未知错误')
      }
    },
    // 点击预览图片
    previewImage(e) {
      const that = this

      let i = e.target.dataset.index;
      let pageindex = e.currentTarget.dataset.pageindex;

      let originImgIndex = e.target.dataset.originimgindex
      let current = that.data.list[pageindex][i].content[originImgIndex]; 
      
      let list = []
      for (let z in that.data.list[pageindex][i].content) {
        list = list.concat(that.data.list[pageindex][i].content[z])
      }
      
      wx.previewImage({
        current: current, // 当前显示图片的http链接
        urls: list // 需要预览的图片http链接列表
      })
    },
    // 收藏按钮事件
    CollectionL(e) {
      const that = this;
      let i = e.currentTarget.dataset.index;
      let pageindex = e.currentTarget.dataset.pageindex;
      let id = e.currentTarget.id;
      const data = {
        types: 'sz',
        id: id
      }
      let dataList = this.data.list[pageindex]
      http.post('operation', data)
        .then(data => {
          const num = data.status ? parseInt(dataList[i].clnb) + 1 : parseInt(dataList[i].clnb) - 1;
          that.setData({
            [`list[${pageindex}][${i}].clstate`]: data.status,
            [`list[${pageindex}][${i}].clnb`]: num
          })
        })
    },

    // 点赞按钮事件
    onLike(e) {
      const that = this;
      let i = e.currentTarget.dataset.index;
      let pageindex = e.currentTarget.dataset.pageindex;
      let id = e.currentTarget.id;
      const data = {
        types: 'dz',
        id: id
      }
      http.post('operation', data)
        .then(data => {
          let num = +that.data.list[pageindex][i].likenum
          data.status ? num += 1 : num -= 1

          that.setData({
            [`list[${pageindex}][${i}].likenum`]: num,
            [`list[${pageindex}][${i}].is_like`]: data.status
          })
        })
    },
    // 评论按钮事件
    toggleComment(e) {
      this.triggerEvent("togglecomment")
      const data = this.data.commentShow ? {
        commentShow: !this.data.commentShow
      } : {
        pageindex: e.currentTarget.dataset.pageindex,
        commentindex: e.currentTarget.dataset.commentindex,
        commentid: e.currentTarget.dataset.commentid,
        commentShow: !this.data.commentShow
      }
      this.setData(data)
    },
    // 一键下载
    download(e) {
      
      this.setData({
        loadModal: false
      })
      const that = this;
      let index = downloadIndex = e.currentTarget.dataset.index //数组索引
      let pageindex = e.currentTarget.dataset.pageindex;
      let id = downloadId = e.currentTarget.id; //内容id
      let type = e.currentTarget.dataset.type; //文件类型
      let data = {
        types: 'xz',
        id: id,
        is_index: 1
      }
      http.post('operation', data)
        .then(data => {
          if (data.state == 0) {
            console.log('当前账户没有积分')
            if (app.globalData.videoAdId) {
              console.log('检测到有视频广告id，即将进入广告')
              that.setData({
                loadModal: true
              }, function () {
                rewardedVideoAd.show()
              })

            } else {
              console.log('当前无视频广告id，显示提示弹窗')
              that.setData({
                loadModal: true
              }, function () {
                wx.showModal({
                  title: data.title,
                  content: data.mess,
                  showCancel: false,
                  success(res) {
                    if (res.confirm) {
                      console.log('即将跳转到积分页面')
                      wx.navigateTo({
                        url: '../../pages/integral/integral',
                      })
                    }
                  }
                })
              })

            }
          } else {
            if (type == 'img') {
              app.globalData.member_water ? that.downImg(pageindex, index, id, true) : that.downImg(pageindex, index, id, false)
            } else if (type == 'video') {
              that.downVideo(pageindex, index, id)
            }
            // 复制内容到剪贴板
            wx.setClipboardData({
              data: that.data.list[pageindex][index].text,
              success: function (res) {
                wx.hideToast();
              }
            })
          }

        })
    },
    // 下载视频
    downVideo(pageindex, index, id) {
      const that = this;

      // 先给他加一手 
      that.setData({
        [`list[${pageindex}][${index}].donnb`]: parseInt(that.data.list[pageindex][index].donnb) + 1
      })

      const downloadTask = wx.downloadFile({
        url: that.data.list[pageindex][index].content[0],
        success: function (res) {
          console.log('下载视频成功，准备开始保存到相册')
          that.setData({
              loadModal: true,
            },
            () => {
              wx.saveVideoToPhotosAlbum({
                filePath: res.tempFilePath,
                success(res) {
                  $Toast({
                    content: '保存成功',
                    type: 'success'
                  });
                },
                fail(res) {
                  $Toast({
                    content: '保存已取消',
                    type: 'warning'
                  });
                },
              })
            })
        }
      })
      // 进度显示
      downloadTask.onProgressUpdate((res) => {
        if (res.progress === 100) {
          that.setData({
            progress: '',
            loadModal: true,
          })
        } else {
          that.setData({
            progress: res.progress
          })
        }
      })

      // 然后再发起请求
      const videodata = {
        types: 'xz',
        id: id,
        is_index: 2,
        counts: 1
      }
      http.post('operation', videodata)
        .then(data => {
          
        })
        .catch(err => {
          $Toast({
            content: '网络错误',
            type: 'error'
          });
          that.setData({
            [`list[${pageindex}][${index}].donnb`]: parseInt(that.data.list[pageindex][index].donnb) - 1
          })
        })

    },
    // 下载图片
    downImg(pageindex, itemIndex, id, isWaterImg) {
      const that = this;
      if (isWaterImg) {
        let dataw = {
          id: id,
        }
        http.post('WaterImg', dataw)
          .then(data => {
            let content = data.content;
            that.saveImgToLocal(content, itemIndex, id, isWaterImg, pageindex)
          })
      } else {
        that.saveImgToLocal(that.data.list[pageindex][itemIndex].content, itemIndex, id, isWaterImg, pageindex)
      }

    },
    saveImgToLocal(content, itemIndex, id, isWaterImg, pageindex) {
      
      const that = this;
      let showIndex = 1;
      for (let i = 0; i < content.length; i++) {
        const downloadTask = wx.downloadFile({
          url: content[i],
          success: function (res) {
            if (i === 0) {
              that.setData({
                [`list[${pageindex}][${itemIndex}].donnb`]: parseInt(that.data.list[pageindex][itemIndex].donnb) + 1
              })
              var data = {
                types: 'xz',
                id: id,
                is_index: 2,
                counts: 1
              }
              http.post('operation', data).then(data => {})
            }
            wx.saveImageToPhotosAlbum({
              filePath: res.tempFilePath,
              success(res) {
                $Toast({
                  content: '下载成功' + (showIndex++) + '/' + content.length,
                  type: 'success'
                });
              },
              fail(res) {
                $Toast({
                  content: '保存已取消',
                  type: 'warning'
                });
              },
              complete() {
                if (isWaterImg) http.post('DeleteImg', {
                  img: content[i]
                }).then(res => {})
              }
            })
          }
        })
        downloadTask.onProgressUpdate((res2) => {
          if (res2.progress === 100) {
            that.setData({
              progress: '',
              loadModal: true,
            })
          } else {
            that.setData({
              progress: res2.progress
            })
          }
        })
      }
    },

  }
})