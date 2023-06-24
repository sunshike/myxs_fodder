
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
var app = getApp()
let siteroot = app.siteInfo.siteroot;
siteroot = siteroot.replace('app/index.php', 'web/index.php');
let upurl = siteroot + '?i=' + app.siteInfo.uniacid + '&c=utility&a=file&do=upload&thumb=0';
Page({

  /** 
   * 页面的初始数据
   */
  data: {
    accounts1: ['默认分组'],
    fileType: 'all',
    account1Index: 0,
    dataNull: 2,
    userList: [],
    allpeople: 0,
    accountspass: [],
    updateNowCode:"",
    totalCount: 0,
    isEmpty: true,
    code:0,
    barHeight: app.globalData.CustomBar,
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
    this.setData({
      type: 'system',
      advshow: app.globalData.member_Advert.member.show,
      member_id: app.globalData.member.memberId
    })
    this.data.advCode = app.globalData.member_Advert.member.advert_text ? app.globalData.member_Advert.member.advert_text : "";

    this.categoryDataQuery(options);
  },
  NextF: function() {
    wx.navigateBack({
      delta: 1
    })
  },
  //分类数据加载
  categoryDataQuery: function(options) {
    var that = this;
    http.get('IsClassAdmin').then(data => {
      var accounts1 = []
      var accountsid1 = []
      var accountspass = []

      for (let z in data) {
        accounts1[z] = data[z].grouping_name
        accountsid1[z] = data[z].grouping_id
        accountspass[z] = data[z].grouping_passwd
      }
      that.setData({
        accounts1: accounts1,
        accountspass: accountspass,
      });
      that.data.accountsid1 = accountsid1;

      if (options.length == undefined){
        var id =accountsid1[0]
      }else{
        var id = options
      }
      
      var classdata = {
        class_id: id,
        start: that.data.totalCount,
        end: 7
      }

      http.get('GroupUsers', classdata).then(data => {
        if (data.length === 0) {
          that.setData({
            dataNull: 0
          })
        } else {
          that.setData({
            dataNull: 1,
            userList: data,
            allpeople: data[0].counts,
            code:that.data.accountspass[that.data.account1Index]
          })
          that.data.totalCount = data.length;
          that.data.isEmpty = false;
        }
      })
      that.data.fz_id = id;
    });

  },
  
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },
  bindAccount1Change: function(e) {
    var that = this;
    that.data.totalCount=0;
    var id = that.data.accountsid1[e.detail.value]
    var classdata = {
      class_id: id,
      start: that.data.totalCount,
      end: 7,
    }
    http.get('GroupUsers', classdata).then(data => {
      if (data.length === 0) {
        that.setData({
          dataNull: 0
        })
      } else {
        that.setData({
          dataNull: 1,
          userList: data,
          allpeople: data[0].counts,
          code: that.data.accountspass[that.data.account1Index]
        })
      }
      that.data.totalCount = data.length;
      that.data.isEmpty=false;  
    })
    that.setData({
      account1Index: e.detail.value,
    })
    that.data.fz_id = id;
  },
  updateCode: function(e) {
    this.setData({
      showModal: true
    })
  },
  updateNowcode:function(e){
    this.data.updateNowCode = e.detail.value
  },
  submit: function(e) {
    var that=this;
    var nowCode = e.currentTarget.dataset.now;
    var updateCode = that.data.updateNowCode;
    var class_id = that.data.fz_id;
    var data={
      nowCode: nowCode,
      updateCode: updateCode,
      class_id:class_id
    }
    http.post('UpdateClassPassWd', data).then(data => {
      if (data.status) {
        
        $Toast({
          content: data.mess,
      });
        that.setData({
          showModal: false, 
          code: updateCode
        })
        // setTimeout(function () {
        //   that.onLoad(class_id);
        // }, 2000)
      } else {
        $Toast({
          content: data.mess,
      });
        
      }

    });
  },
  go: function() {
    this.setData({
      showModal: false
    })
  },
  /**
   * 踢出分组
   * 
   */
  kickOutGroup: function(e) {
    var that = this;
    var userid = e.currentTarget.dataset.userid;
    var indexid = e.currentTarget.dataset.indexid;
    var data = {
      user_id: userid,
      fz_id: that.data.fz_id
    }
    wx.showModal({
      title: '',
      content: "是否将此成员踢出该分组",
      confirmText: '确定',
      complete: function(e) {
        if (e.confirm) {
          http.post('KickOutUserToGroup', data).then(data => {
            if (data) {
              $Toast({
                content: '删除成功',
                type: 'success'
            });
              that.data.userList.splice(indexid, 1)
              that.setData({
                userList: that.data.userList,
                allpeople: that.data.allpeople-1
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
  //上拉加载
  onReachBottom: function (e) {
    var that = this;
    var class_id = that.data.fz_id;
    var data = {
      start: that.data.totalCount,
      end: 7,
      class_id: class_id
    }
    http.get('GroupUsers', data).then(data => {
      if (data.length === 0) {
        
        $Toast({
          content: '加载已完成',
          type: 'success'
      });
        return false
      }
      var totalContents = {};
      if (!that.data.isEmpty) {
        totalContents = that.data.userList.concat(data);
      } else {
        totalContents = that.data.userList;
        that.data.isEmpty = false;
      }
      that.setData({
        userList: totalContents
      })
      that.data.totalCount += 7;
    });

  },

})