// myxs_fodder/pages/MySet/MySet.js
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
const app = getApp()
let siteroot = app.siteInfo.siteroot;
// let upurl = siteroot + '?i='+app.siteInfo.uniacid+'&c=utility&a=file&do=upload&thumb=0';
siteroot = siteroot.replace('web/index.php', 'app/index.php');
let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&t=0&from=wxapp&c=entry&m=myxs_fodder&a=wxapp&do=FileSubmit2';

import {
  chooseImage, 
  upload
} from '../../utils/image.js';
Page({
 
  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      banquan: app.globalData.banquan,
      advCode: app.globalData.member_Advert.member_set.advert_text ? app.globalData.member_Advert.member_set.advert_text:"",
      advshow: app.globalData.member_Advert.member_set.show
    })
    this.myinfo();

  },
  Closeadvert: function () {
    this.setData({
      advshow: 0
    })
  },
  myinfo: function () {
    var that = this;
    http.get('member')
    .then(data => {
      that.setData({
        UserImages: data.status.member_head_portrait,
        Name: data.status.member_name,
        memberid: data.status.member_id,
        Phone:data.status.member_mobile
      })
    });
  },

  chooseImages() {
    var that = this;
    chooseImage()
    .then(path => {
      wx.uploadFile({
        url: upurl,
        filePath: path,
        name: 'file',
        success: function (res) {
          console.log(JSON.parse(res.data).data.url);
          
          if(JSON.parse(res.data).data.status){
            that.setData({
              UserImages : JSON.parse(res.data).data.url,
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
          console.log('图片上传失败', err)
        }
      })
      }, err => {
        toast('选择图片失败'); 
      })
  },
  NameData: function (e) {
    
    if(e.detail.value == ''){
      $Toast({
        content: '请输入昵称',
        type: 'warning'
      });
      this.setData({
        Name : ''
      })
    }else{
      var mess = {
        content: e.detail.value
      }
      http.get('CheckMsg',mess).then(data => {
        if(data.message.errcode == 87014){
          $Toast({
            content: '内容含不良信息',
            type: 'warning'
          });
          this.setData({
            Name : ''
          })
        }else{
          this.setData({
            Name: e.detail.value
          })
        }

    });
    }
    
   

  },
  PhoneData: function (e) {
    if(e.detail.value == ''){
      $Toast({
        content: '请输入联系方式',
        type: 'warning'
      });
      this.setData({
        Phone : ''
      })
    }else{
      var mess = {
        content: e.detail.value
      }
      http.get('CheckMsg',mess).then(data => {
          if(data.message.errcode == 87014){
            $Toast({
              content: '内容含不良信息',
              type: 'warning'
            });
            this.setData({
              Phone : ''
            })
          }else{
            this.setData({
              Phone: e.detail.value
            })
          }

      });
    }

  },
  Sub: function () {
    var Name = this.data.Name;
    var UImg = this.data.UserImages;
    var Phone =this.data.Phone;
    if(Name == ''){
      $Toast({
        content: '请输入昵称',
        type: 'warning'
      });
      return false;
    }
    var data = {
      type:'update',
      nickName: Name,
      avatarUrl: UImg,
      mobile:Phone
    }

    http.post('UpdateMember', data).then(data => {
      if (data){
        wx.navigateBack({
          delta: 1,
        })
        app.globalData.member.memberName = Name,
        app.globalData.member.memberImg = UImg
      }else{
        $Toast({
          content: '保存失败',
          type: 'error'
        });
      }
    });
    
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  },
   copyright: function () {
    wx.navigateTo({
      url: '../copyright/copyright',
    })
  },
  fanhui:function(){
    wx.navigateBack({
      delta: 1
    })
  },
})