// pages/Release/Release.js
import {
  chooseImage,
  upload
} from '../../utils/image.js';
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
var app = getApp()
let siteroot = app.siteInfo.siteroot;
// siteroot = siteroot.replace('app/index.php', 'web/index.php');
// let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&c=utility&a=file&do=upload&thumb=0';
siteroot = siteroot.replace('web/index.php', 'app/index.php');
// let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&c=utility&a=file&do=upload&thumb=0';
let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&t=0&from=wxapp&c=entry&m=myxs_fodder&a=wxapp&do=FileSubmit';
let nowFiles = [];
let szie = 9;
let stat_bg = null;
Page({

  /**
   * 页面的初始数据
   */ 
  data: {
    coverShow: false,
    fileType: 'all',
    accounts1: ['默认分组'],
    account1Index: 0,
    files: [],
    
    
    addbl: 1,
    groupBg:"",
    groupBgstatus:2

  },
  Closeadvert: function() {
    this.setData({
      advshow: 0
    })
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    nowFiles = nowFiles.concat(app.globalData.stat_bg)
    stat_bg = app.globalData.stat_bg;
    this.setData({
      type: 'system',
      advCode: app.globalData.member_Advert.member.advert_text ? app.globalData.member_Advert.member.advert_text : "",
      advshow: app.globalData.member_Advert.member.show,
      
      
    })
    this.categoryDataQuery(options);
  },
  openRuleBox: function(e) {
    this.setData({
      modalName: e.currentTarget.dataset.target
    })
  },
  hideModal(e) {
    this.setData({
      modalName: null
    })
  },
  //分类数据加载
  categoryDataQuery: function(options) {
    var that = this;
    http.get('IsClassAdmin').then(data => {
      var accounts1 = []
      var accountsid1 = []

      for (let z in data) {
        accounts1[z] = data[z].grouping_name
        accountsid1[z] = data[z].grouping_id
      }
      that.setData({
        accounts1: accounts1,
      });
      that.data.accountsid1 = accountsid1;
      if (options.length == undefined) {
        that.data.fz_id = accountsid1[0];
        var id = accountsid1[0]
      } else {
        that.data.fz_id = options;
        var id = options
      }
      var bgData = {
        group_id: id,
        type:"look"
      }
      http.get('GetGroupBg', bgData).then(data => {
        if(data){
          that.setData({
            groupBgstatus: 1,
            groupBg:data.stat_bg
          })
        }else{
          that.setData({
            groupBgstatus:0
          })
        }
      });
    });

  },
  bindAccount1Change: function(e) {
    var that = this;
    var id = that.data.accountsid1[e.detail.value]
    that.setData({
      account1Index: e.detail.value,
    })
    that.data.fz_id = id;
    var bgData = {
      group_id: id,
      type: "look"
    }
    http.get('GetGroupBg', bgData).then(data => {
      if (data) {
        that.setData({
          groupBgstatus: 1,
          groupBg: data.stat_bg
        })
      } else {
        that.setData({
          groupBgstatus: 0
        })
      }
    });
  },
  NextF: function() {
    wx.navigateBack({
      delta: 1
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  chooseImage: function(e) {
    var that = this;
    if (that.data.fileType == 'all') {
      wx.showActionSheet({
        itemList: ['照片'],
        success(res) {
          if (res.tapIndex === 0) {
            that.SaveImg()
          }
        }
      })
    } else if (that.data.fileType == 'img') {
      that.SaveImg()
    }




  },
  //保存图片
  SaveImg: function() {
    var that = this;
    wx.chooseImage({
      count: szie,
      sizeType: ['original'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
      success: function(res) {

        szie = szie - res.tempFilePaths.length
        that.setData({
          fileType: 'img',
        })
        if (szie == 0) {
          that.setData({

            addbl: 0,
            gifH: 264
          })
        }
        for (let k in res.tempFilePaths) {
          that.uploadfile(res.tempFilePaths[k])

        }

      }
    })
 
  },
  uploadfile: function(url) {
    var that = this;
    var types = '';
    if (that.data.fileType == 'video') {
      types = 'video'
    }
    that.setData({
      coverShow: true
    })
    const jd = wx.uploadFile({
      url: upurl,
      filePath: url,
      name: 'file',
      header: {                
        "Content-Type": "multipart/form-data"                            
      },
      success: function(res) {
        if (JSON.parse(res.data).data.message != undefined) {
          
          $Toast({
            content: JSON.parse(res.data).data.message,
        });
        }
        that.setData({
          files: that.data.files.concat(JSON.parse(res.data).data.url),
        })
      }


    })
    jd.onProgressUpdate((res) => {
      if (res.progress == 100) {
        that.setData({
          coverShow: false
        })
      }
      that.setData({
        addbl: 0,
      })
    })
  },

  previewImage: function(e) {
    wx.previewImage({
      current: e.currentTarget.id, // 当前显示图片的http链接
      urls: this.data.files // 需要预览的图片http链接列表
    })
  },
  previewNowImage: function(e) {
    wx.previewImage({
      current: e.currentTarget.id, // 当前显示图片的http链接
      urls: nowFiles // 需要预览的图片http链接列表
    })
  },
  //文本值
  TextData: function(e) {
    this.setData({
      textData: e.detail.value
    })

  },
  //提交
  Release: function() {
    var that = this;

    if (this.data.fileType == 'video') {
      that.data.files[1] = that.data.videoW
      that.data.files[2] = that.data.videoh
      that.setData({
        files: that.data.files
      })

    }
    if (this.data.files.length < 1) {
      
      $Toast({
        content: '请上传一张图片',
        type: 'warning'
    });
      return false
    }
    var saveData = {
      files: this.data.files.toString(),
      group_id: this.data.fz_id,
      file_type: this.data.fileType,
      type:"update"

    }
    http.post('GetGroupBg', saveData).then(data => {
      if (data) {
        nowFiles = nowFiles.concat(data.stat_bg)
        that.setData({
          groupBgstatus: 1,
          groupBg: data.stat_bg,
          files:[],
          addbl:1,
          
        })

        $Toast({
          content: '修改成功',
          type: 'success'
      });
        setTimeout(function () {
          that.onLoad(that.data.fz_id);
        }, 2000)
      } else {
        that.setData({
          groupBgstatus: 0
        })
        $Toast({
          content: '修改失败',
          type: 'error'
      });
      }
    });
  }

})