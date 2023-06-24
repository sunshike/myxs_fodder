import http from '../../utils/request.js';
// 防抖函数
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
const app = getApp()
Component({
  pageLifetimes: {
    show() {
      const that = this;
      let _isgetmobile = true;
      try {
        var value = wx.getStorageSync('UpdateMobileres');
        if(that.data.my_group || that.data.my_join) _isgetmobile = false;
        if (value) {
          
          _isgetmobile = false;
        } 
      } catch (e) {
        // Do something when catch error
      } finally {
        that.setData({
          isgetmobile: _isgetmobile,
        })
      }
      console.log(that.my_group, that.my_join);
      console.log("_isgetmobile", _isgetmobile);
    }
  },
  lifetimes: {
    created() {
      const that = this;
      
      // 加载缓存数据
      const classifyList = wx.getStorageSync('GetCommunityClass')
      if (classifyList) {
        this.classifyId = classifyList[0] ?.id
      }

      // 请求网络数据
      http.get('GetCommunityClass')
        .then(res => {
          that.classifyId = res.class[0].id
          wx.setStorageSync("GetCommunityClass", res.class) // 覆盖缓存数据
        })
        .catch(err => {
          console.log('获取分类数据出错', err)
        })
    },
  },
  properties: {
    // 判断在我的社群页面中 是否处于 “我加入的社群” 选项
    my_join: {
      type: Boolean,
      value: false
    },
    // 判断在我的社群页面中 是否处于 “我创建的社群” 选项
    my_group: {
      type: Boolean,
      value: false
    },
    // 请求的接口名称
    requestInterface: {
      type: String,
      value: 'GetCommunity'
    },
    // 隐藏组件
    hidden: {
      type: Boolean,
      value: false
    },
    // 组件的高度
    height: {
      type: String,
      value: '100%'
    }
  },

  data: {
    refreshState: false, // 下拉加载状态
    list: [], // 列表数据
    isLoadMore: true, // 是否显示加载更多动画
    loadingTitle: '加载中', // 加载更多时显示的文字
    canGetMore: true, // 是否可以请求更多数据
    isgetmobile: true,
  },

  /**
   * 组件的方法列表
   */
  methods: {
    /**
     * 请求数据
     * @param{String} pos 地址
     * @param{String} classId 分类id
     * @param{String} order 排序id
     * @param{object} field 额外的请求字段
     */
    dataQuery(pos, classId, order, field) {
      const that = this;

      const requestInterface = this.data.requestInterface;
      this.setData({
        isLoadMore: true,
        loadingTitle: '加载中'
      })

      let data_field = {
        location: pos,
        class_id: classId,
        order: order,
        start: 0,
        end: 5
      }
      if (pos === '') delete data_field.location
      Object.assign(data_field, field)

      this.data_field = data_field
      this.pos = pos

      http.get(requestInterface, data_field)
        .then(res => {
          // 1.返回的数据为空
          // 2.返回的数据数量小于请求的数量
          if (res.length === 0 || res.length < data_field.end) {
            that.setData({
              isLoadMore: false,
              loadingTitle: '暂无更多数据',
              canGetMore: false
            })
            
          } else {
            that.setData({
              canGetMore: true
            })
          }
          res.map(i => {
            i.group_create_timeStr = app.getDateDiff((+i.group_create_time) * 1000)
          })
          that.setData({
            list: res
          })
          wx.setStorageSync(JSON.stringify(data_field) + requestInterface, res)
        })
        .catch(err => {
          console.log('获取列表数据出错', err)
        })

    },
    /**
     * 格式化列表数据中的日期
     * @param{array} list 列表数据
     */
    convertData(list) {
      const that = this
      list.map(i => {
        i.group_create_timeStr = app.getDateDiff((+i.group_create_time) * 1000)
      })
      that.setData({
        list: list
      })
    },
    // 下拉加载
    onRefresherrefresh(e) {
      const that = this
      this.triggerEvent('refresh')

      setTimeout(() => {
        this.setData({
          refreshState: false
        })
      }, 700);
    },

    // 加载更多
    getMore: throttle(function () {
      if (!this.data.canGetMore) return
      const that = this
      console.log('社群列表 加载更多')
      this.data_field.start += this.data_field.end

      const requestInterface = this.data.requestInterface
      // 获取缓存数据
      // const storagelist = wx.getStorageSync(JSON.stringify(this.data_field) + requestInterface)
      // if(storagelist) {
      //   let list = this.data.list.concat(storagelist)
      //   this.setData({
      //     list: list,
      //   })
      // }

      http.get(requestInterface, this.data_field)
        .then(res => {
          if (res.length === 0) {
            that.setData({
              isLoadMore: false,
              loadingTitle: '暂无更多数据',
              canGetMore: false,
            })
            return
          }
          res.map(i => {
            i.group_create_timeStr = app.getDateDiff((+i.group_create_time) * 1000)
          })
          let list = that.data.list.concat(res)
          that.setData({
            list: list,
          })
          wx.setStorageSync(JSON.stringify(that.data_field) + requestInterface, res)
        })
        .catch(err => {
          console.log('获取列表数据出错', err)
        })
    }, 500),
    catchBubble() {

    },
    getPhoneNumber(e) {
      const that = this
      console.log("getPhoneNumber");
      if (e.detail.errMsg == "getPhoneNumber:ok") {
        if (this.data.my_join) {
          this.triggerEvent('quit', {
            group_id: e.currentTarget.dataset.group_id
          })
          return
        }
        if (this.data.my_group) {
          wx.navigateTo({
            url: `../../pages/SocialRelease/index?group_id=${e.currentTarget.dataset.group_id}`,
          })
        }

        const hasJoin = e.currentTarget.dataset.join
        if (!hasJoin) {
          wx.showModal({
            content: '是否加入该社群?',
            success(res) {
              if (res.confirm) {
                const index = e.currentTarget.dataset.index;
                wx.login({
                  success (loginres) {
                    if (loginres.code) {
                      
                      //发起网络请求
                      http.get('UpdateMobile', {
                        encryptedData: e.detail.encryptedData,
                        iv: e.detail.iv,
                        code: loginres.code
                      })
                      .then(UpdateMobileres => {
                        
                        if(UpdateMobileres.status == 1) {
                          
                          wx.setStorageSync('UpdateMobileres', UpdateMobileres.mobile);
                          that.setData({
                            isgetmobile: false
                          })
                          http.get('JoinCommunity', {
                            group_id: e.currentTarget.dataset.group_id
                          })
                          .then(res => {
                            if (res.status === 1) {
                              that.setData({
                                [`list[${index}].is_join`]: true
                              })
                            } else {
                              wx.showModal({
                                content: res.msg,
                                showCancel: false,
                                confirmText: '知道了'
                              })
                            }
                          })
                          .catch(err => {
                            wx.showToast({
                              title: '发生错误',
                              icon: "none",
                            })
                            console.log('加入时发生错误', err)
                          })
                        } else {
                          wx.showToast({
                            title: UpdateMobileres.msg,
                            icon: "none",
                          })
                        }
                        
                      })
                        
                    } else {
                      wx.showToast({
                        title: '获取信息失败',
                        icon: "none",
                      })
                      console.log('登录失败！' + loginres.errMsg)
                    }
                  }
                })
                
                
                
              }
            }
          })
        }
      } else {
        wx.showToast({
          title: '授权失败无法加入',
          icon: "none",
        })
      }

    },
    // 加入社群/编辑社群/退出社群
    onJoin(e) {
      console.log("onJoin");
      const that = this
      
      if (this.data.my_join) {
        this.triggerEvent('quit', {
          group_id: e.currentTarget.dataset.group_id
        })
        return
      }
      if (this.data.my_group) {
        wx.navigateTo({
          url: `../../pages/SocialRelease/index?group_id=${e.currentTarget.dataset.group_id}`,
        })
      }

      const hasJoin = e.currentTarget.dataset.join
      if (!hasJoin) {
        wx.showModal({
          content: '是否加入该社群?',
          success(res) {
            if (res.confirm) {
              const index = e.currentTarget.dataset.index;
              
              http.get('JoinCommunity', {
                group_id: e.currentTarget.dataset.group_id
              })
              .then(res => {
                if (res.status === 1) {
                  that.setData({
                    [`list[${index}].is_join`]: true
                  })
                } else {
                  wx.showModal({
                    content: res.msg,
                    showCancel: false,
                    confirmText: '知道了'
                  })
                }
              })
              .catch(err => {
                wx.showToast({
                  title: '发生错误',
                  icon: "none",
                })
                console.log('加入时发生错误', err)
              })
              
              
            }
          }
        })
      }
    },
    // 去详情页
    toDetail(e) {
      wx.navigateTo({
        url: `../../pages/SquareDetail/index?id=${e.currentTarget.dataset.id}&pos=${this.pos}`,
      })
    },
  }
})