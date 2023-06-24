import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
var app = getApp()
let siteroot = app.siteInfo.siteroot;
siteroot = siteroot.replace('web/index.php', 'app/index.php');
let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&t=0&from=wxapp&c=entry&m=myxs_fodder&a=wxapp&do=FileSubmit';
const videoCoverUrl = siteroot + '?i=' + app.siteInfo.uniacid + '&t=0&from=wxapp&c=entry&m=myxs_fodder&a=wxapp&do=FileSubmit2';
let duration = 0;

Page({

  data: { 
    coverShow: false,
    accounts: ['请选分类'], 
    accounts1:['默认分组'],
    groupList: [{
      id: '0',
      name: '无社群信息'
    }],
    groupListIndex: 0,
    fileType: 'img',  
    accountIndex: 0, // 素材分类
    account1Index:0, // 分组分类
    barHeight: app.globalData.CustomBar,
    files: [],
    szie: 9,
    addbl: 1,
    ShowBl: true,
    isshow:false,
    textData:'' 
  },
  onLoad: function (options) {
    const that = this
    http.get('GetMyAllCommunity')
    .then(res => {
      console.log('社群列表', res)
      if(res.length != 0) {
        that.setData({
          groupList: res
        })
      }
      
    })
    .catch(err => {
      console.log('获取社群列表错误', err)
    })

    let type = ''
    if(options.id == 0) type = 'img' 
    else if(options.id == 1) type = 'video' 
    this.setData({
      fileType: type,
      type: 'system',
      advCode: app.globalData.member_Advert.member.advert_text ? app.globalData.member_Advert.member.advert_text:"",
      advshow: app.globalData.member_Advert.member.show
    })
    this.categoryDataQuery();
  },

  // 关闭广告
  Closeadvert: function () {
    this.setData({
      advshow: 0
    })
  },

  // 返回上一层
  NextF: function () {
    wx.navigateBack({
      delta: 1
    })
  },
  // 默认分组选择
  bindAccount1Change: function (e) {
    var id = this.data.accountsid1[e.detail.value]
    this.setData({
      account1Index: e.detail.value,
      fz_id: id
    })
  },
  selectGroup(e) {
    this.setData({
      groupListIndex: e.detail.value,
    })
  },
  //分类数据加载
  categoryDataQuery: function () {
    var that = this;
    http.get('listClass').then(data => {
      var accounts = []
      var accountsid = []
      for (let z in data) {
        accounts[z] = data[z].class_name
        accountsid[z] = data[z].class_id
      }
      that.setData({
        accounts: accounts,
        class_id: accountsid[0]
      });
      that.data.accountsid= accountsid

    });
    http.get('UserGrouping').then(data => {
      var accounts1 = []
      var accountsid1 = []

      for (let z in data) {
        accounts1[z] = data[z].grouping_name
        accountsid1[z] = data[z].grouping_id
      }
      that.setData({
        accounts1: accounts1,
      });
      that.data.accountsid1=accountsid1;
      that.data.fz_id= accountsid1[0];


    });

  },

  // 素材分类
  bindAccountChange: function (e) {

    var id = this.data.accountsid[e.detail.value]

    this.setData({
      accountIndex: e.detail.value,
      class_id: id
    })
  },

  // 选择上传 -> 保存图片 || 保存视频
  chooseImage: function (e) {
    if(this.data.fileType === 'img') {
      if(this.data.files.length >= 9) {
        $Toast({
          content: '上传数量已达上限',
          type: 'warning'
        });
      }else {
        this.SaveImg()
      }
    }else {
      
      if(this.data.files.length >= 1) {
        $Toast({
          content: '上传数量已达上限',
          type: 'warning'
        });
      }else {
        this.SaveVideo();
      }
    }
  },
// delete select img
  DelImg: function(e) {
    let tempData = this.data.files
    tempData.splice(e.currentTarget.dataset.index, 1)
    this.setData({
      files: tempData
    })
  },
  DelVideo(e) {
    this.setData({
      files: [],
      yulanvideo: ''
    })
  },

  //保存图片 -> 上传文件
  SaveImg: function () {
    var that = this;
    wx.chooseImage({
      count: that.data.szie,
      sizeType: ['original'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
      success: function (res) {

        that.data.szie = that.data.szie - res.tempFilePaths.length
        that.data.szie = that.data.szie
        if (that.data.szie == 0) {
          that.setData({
            addbl: 0,
            gifH: 264
          })
        }
        for (let k in res.tempFilePaths) {
          that.uploadfile(res.tempFilePaths[k], true)
        }
      }
    })
  },

  // 切换上传文件类型
  changeFileType: function(e) {
    this.setData({
      fileType: e.target.dataset.type,
      files: [],
      yulanvideo: ''
    })
  },

  //保存视频 -> 上传文件
  SaveVideo: function (path) {
    const that = this;
    wx.chooseMedia({
      count: 1,
      mediaType: ['video'],
      sourceType: ['album', 'camera'],
      maxDuration: 60,
      // camera: 'back',
      success(res) {
        console.log(res.tempFiles)

        duration = res.tempFiles[0].duration;
        that.setData({
          addbl: 0,
          videoW: 200,
          videoh: 125,
          yulanvideo: res.tempFiles[0].tempFilePath
        });
        that.thumbTempFilePath = res.tempFiles[0].thumbTempFilePath
        that.uploadfile(res.tempFiles[0].tempFilePath, false);
      },
      fail(err) {
        console.log(err)
      }
    })
  },

  // 上传文件
  uploadfile: function (url, isImg) {
    var that = this;
    that.setData({
      coverShow: true
    })
    wx.uploadFile({
      url: upurl,
      filePath: url,
      name: 'file',
      success: function (res) {
        console.log('上传文件结果', res)
        that.setData({
          coverShow: false
        })
        if(JSON.parse(res.data).data.status){
            
            that.setData({
              isshow : true,
              files: that.data.files.concat(JSON.parse(res.data).data.url),
            })
        }
        if (JSON.parse(res.data).data.message != undefined ){
          $Toast({
            content: JSON.parse(res.data).data.message,
        });
          return false;
        }
      },
      fail: function(err) {
        console.log('上传失败', err)
      }
    })
    if(!isImg) {
      wx.uploadFile({
        url: videoCoverUrl,
        filePath: this.thumbTempFilePath,
        name: 'file',
        success: function (res) {
          if(JSON.parse(res.data).data.status) {
            that.data.files.concat(JSON.parse(res.data).data.url);
            that.videoCover = JSON.parse(res.data).data.url;
            console.log("前端封面地址", that.thumbTempFilePath);
            console.log("返回封面图片地址", JSON.parse(res.data).data.url);
          } else {
            wx.showToast({
              title: JSON.parse(res.data).data.message,
            })
          }
          
        },
        fail: function(err) {
          console.log('上传失败', err)
        }
      })
    }
    
  },


  // 预览图片
  previewImage: function (e) {
    wx.previewImage({
      current: e.currentTarget.id, // 当前显示图片的http链接
      urls: this.data.files // 需要预览的图片http链接列表
    })
  },


  // 分享内容输入 -> data数据改变
  TextData: function (e) { 
    this.data.textData = e.detail.value;
    this.setData({
      textData : e.detail.value
    })
    var mess = {
      content: this.data.textData
    }
    http.get('CheckMsg',mess).then(data => {
        if(data.message.errcode == 87014){
         
          $Toast({
            content: '内容含不良信息',
            type: 'warning'
        });
          this.setData({
            textData : ''
          })
        }

    });
  },
 
 

  // 是否显示
  ShowBl: function (e) {
    this.setData({
      ShowBl: e.detail.value
    })
  },

  //提交
  Release: function () {
    var that = this;

    if (this.data.fileType == 'video') {
      that.data.files[1] = that.data.videoW
      that.data.files[2] = that.data.videoh
      that.setData({
        files: that.data.files
      })

    }

    if (this.data.textData == undefined || this.data.textData == '') {
      
      $Toast({
        content: '内容不能为空',
        type: 'warning'
      });
      return false
    }else{
      var mess = {
        content: this.data.textData
      }
      http.get('CheckMsg',mess).then(data => {
          if(data.message.errcode == 87014){
            
            $Toast({
              content: '内容含不良信息',
              type: 'warning'
          });
            this.setData({
              textData : ''
            })
            return false
          }
  
      });
    }
    if (this.data.files.length < 1) {
     
      $Toast({
        content: '请至少上传一张图片',
        type: 'warning'
    });
      return false
    }
    var data = {
      textDat: this.data.textData,
      files: this.data.files.toString(),
      class_id: this.data.class_id,
      grouping_id: this.data.fz_id,
      ShowBl: this.data.ShowBl,
      file_type: this.data.fileType,
      video_img: this.videoCover,
      circle_id: this.data.groupList[this.data.groupListIndex].id
    }
    http.post('Content', data).then(data => {
      if (data) {
        getApp().globalData.releaseBack = true
        
        $Toast({
          content: '发布成功，请等待审核',
          icon: 'success',
          duration: 0,
        });
        setTimeout(() => {
          $Toast.hide();
          wx.redirectTo({
            url: '../MyRelease/MyRelease',
          })
        }, 1000);

      } else {
        $Toast({
          content: '未知错误',
          type: 'error'
      });
      }
    });
  }

})