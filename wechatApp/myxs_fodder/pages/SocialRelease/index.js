const app = getApp()
import http from '../../utils/request.js';
const {
  $Toast
} = require('../../iview_ui/base/index');
let siteroot = app.siteInfo.siteroot.replace('web/index.php', 'app/index.php');
const uploadUrl = siteroot + '?i=' + app.siteInfo.uniacid + '&t=0&from=wxapp&c=entry&m=myxs_fodder&a=wxapp&do=FileSubmit2';
const chooseLocation = requirePlugin('chooseLocation');
var QQMapWX = require('../../dist/qqmap-wx-jssdk.js');
var qqmapsdk;
Page({
  data: {
    imgList: [],
    picker: [
      '你好',
      '我好',
      '大家好'
    ],
    files: '', // 小图片文件
    files2: '', // 大图片文件
    modalName: '',
    address: '',
    titleValue: '', // 群名称
    desValue: '', // 群介绍
    headerValue: '', // 群主微信
    agreeRule: true, // 是否同意发布须知
    tip: '', // 发布须知
    isHandle: false,
  },
  onLoad(option) {
    this.requestInterface = 'Community'

    // 实例化地图API核心类
    qqmapsdk = new QQMapWX({
      key: app.globalData.map_key
    });

    if(option.group_id) {
      const that = this
      this.requestInterface = 'UpdateCommunity'
      this.setData({
        isHandle: true
      })
      this.group_id = option.group_id
      http.get('GetCommunityMess', {
        group_id: option.group_id,
        location: ``
      })
      .then(res => {
        console.log(res)
        that.pos = res.group_location
        qqmapsdk.reverseGeocoder({
          location: res.group_location, //获取表单传入的位置坐标,不填默认当前位置,示例为string格式
          success: function (mapRes) {
            var addressObj = mapRes.result;
            that.setData({
              index: ((+res.group_class) - 1).toString(),
              titleValue: res.group_name,
              desValue: res.group_message,
              [`imgList[0]`]: res.group_logo_s,
              [`files`]: res.group_logo,
              [`files2`]: res.group_logo_s,
              headerValue: res.group_user_wx,
              address: addressObj.address,
            })
          },
          fail: function (error) {
            console.error('解析地址出错', error);
          },
        })
      })
      .catch(err => {
        console.log('获取修改原信息出错', err)
      })
    }
  },
  onShow() {
    http.get('GetCommunityClass')
      .then(res => {
        console.log(res)
        this.picker = res.class
        this.setData({
          picker: res.class.map(item => item.class_name),
          tip: res.tip
        })
      })
      .catch(err => {
        console.log('获取分类数据失败', err)
      })
    const location = chooseLocation.getLocation(); // 如果点击确认选点按钮，则返回选点结果对象，否则返回null
    console.log('location', location)
    if (location) {
      this.setData({
        address: location.address
      })
      this.pos = `${location.latitude},${location.longitude}`
    }
  },
  onAgreeRule() {
    this.setData({
      agreeRule: !this.data.agreeRule
    })
  },
  // 输入群主微信
  onHeaderInput(e) {
    let value = (e.detail.value || '').trim()

    if (value) {
      const reg = /[\u4e00-\u9fa5]/ig
      if ((reg.test(value))) {
        value = value.replace(reg, '')
        $Toast({
          content: '微信号不合法',
        });
      }
    }


    this.setData({
      headerValue: value
    })
  },
  // 输入群介绍
  textareaBInput(e) {
    this.setData({
      desValue: e.detail.value
    })
  },
  // 输入群名称
  onNameInput(e) {
    this.setData({
      titleValue: e.detail.value
    })
  },
  ChooseImage() {
    wx.chooseImage({
      count: 1, //默认9
      sizeType: ['original', 'compressed'], //可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album'], //从相册选择
      success: (res) => {
        if (this.data.imgList.length != 0) {
          this.setData({
            imgList: this.data.imgList.concat(res.tempFilePaths)
          })
        } else {
          this.setData({
            imgList: res.tempFilePaths
          })
        }
        this.uploadfile(res.tempFilePaths[0])
      }
    });
  },
  ViewImage(e) {
    wx.previewImage({
      urls: this.data.imgList,
      current: e.currentTarget.dataset.url
    });
  },
  DelImg(e) {
    this.data.imgList.splice(e.currentTarget.dataset.index, 1);
    this.setData({
      imgList: this.data.imgList
    })
  },
  onDelete() {
    http.get('DeleteMyCommunity', {
      group_id: this.group_id
    })
    .then(res => {
      console.log(res)

      $Toast({
        content: '删除成功！',
        icon: 'success',
        duration: 0,
      });
      setTimeout(() => {
        $Toast.hide();
        wx.navigateBack({
          delta: 1
        })
      }, 1000);
    })
    .catch(err => {
      console.log('删除失败', err)
    })
  },
  // 上传文件
  uploadfile: function (url) {
    const that = this;
    wx.uploadFile({
      url: uploadUrl,
      filePath: url,
      name: 'file',
      success: function (res) {
        console.log('上传结果', JSON.parse(res.data))
        if (JSON.parse(res.data).data.status) {
          that.setData({
            files: JSON.parse(res.data).data.url50,
            files2: JSON.parse(res.data).data.url
          })
        }
        if (JSON.parse(res.data).data.message != undefined) {
          $Toast({
            content: JSON.parse(res.data).data.message,
          });
          return;
        }

      },
      fail: function (err) {
        console.log('上传失败', err)
      }
    })
  },
  // 发布须知
  openRuleBox: function () {
    console.log(111)
    this.setData({
      modalName: 'Modal'
    })
  },
  hideModal(e) {
    this.setData({
      modalName: ''
    })
  },

  // 选择位置
  getPos() {
    wx.getStorage({
      key: 'pos',
      success(res) {
        const key = app.globalData.map_key; //使用在腾讯位置服务申请的key
        const referer = '社群素材'; //调用插件的app的名称
        const location = JSON.stringify({
          latitude: res.data.latitude,
          longitude: res.data.longitude
        });
        wx.navigateTo({
          url: `plugin://chooseLocation/index?key=${key}&referer=${referer}&location=${location}`
        });
      }
    })

  },

  // 分类选择
  PickerChange(e) {
    this.setData({
      index: e.detail.value
    })
  },
  // 发布 
  Release() {
    
    if (!this.data.index) {
      $Toast({
        content: '请选择群类别',
      });
      return
    }
    if (!this.data.agreeRule) {
      $Toast({
        content: '需同意用户须知',
      });
      return
    }
    if (!this.pos) {
      $Toast({
        content: '获取地址信息失败',
      });
      return
    }
    const that = this
    const mess = {
      content: this.data.titleValue + this.data.desValue + this.data.headerValue
    }
    http.get('CheckMsg', mess)
      .then(data => {
        if (data.message.errcode == 87014) {
          $Toast({
            content: '内容含不良信息',
            type: 'warning'
          });
          return Promise.reject()
        }
      })
      .then(() => {
        let data_field = {
          group_class: that.picker[that.data.index].id,
          group_name: that.data.titleValue,
          group_message: that.data.desValue,
          group_location: that.pos,
          group_logo: that.data.files,
          group_logo_s: that.data.files2,
          group_user_wx: that.data.headerValue
        }
        if(that.data.isHandle) Object.assign(data_field, { group_id: that.group_id })
        http.post(that.requestInterface, data_field)
          .then(res => {
            console.log(res)
            if (res.status === 1) {
              $Toast({
                content: res.msg,
                icon: 'success',
                duration: 0,
              });
              setTimeout(() => {
                $Toast.hide();
                wx.redirectTo({
                  url: '../MySocialGroup/index',
                })
              }, 1500);

            } else {
              $Toast({
                content: res.msg,
              });
            }

          })
          .catch(err => {
            console.log('提交失败', err)
          })
      })


  },
})