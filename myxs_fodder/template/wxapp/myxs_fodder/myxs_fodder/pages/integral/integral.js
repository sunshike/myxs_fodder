const app = getApp()
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');

Page({
  data: {
    intergral: 0,
    today_intergral: 0,
    is_login :false,
    modalName: '',
    coverShow: false,
    releaseSendIntergral:0,
    inviteIntergral:0,
    LoginIntergral:0,
    SignIntergral:0,
    isLoginPrizeTake: false,

    calendarShow: false,
    
    signLabel: '签到', // 日历签到按钮显示文字
    isSign: false, // 是否禁用签到按钮
    isTodaySign: false, // 判断今天是否已经签到
    calendarConfig: {
      markToday: '今', // 当天日期展示不使用默认数字，用特殊文字标记
      defaultDay: true, // 默认选中指定某天；当为 boolean 值 true 时则默认选中当天，非真值则在初始化时不自动选中日期，
      highlightToday: true, // 是否高亮显示当天，区别于选中样式（初始化时当天高亮并不代表已选中当天）
      // takeoverTap: true, // 是否完全接管日期点击事件（日期不会选中），配合 onTapDay() 使用
      onlyShowCurrentMonth: true, // 日历面板是否只显示本月日期
    },
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    const that = this;
    http.get('index')
    .then(data => {
      that.setData({
        isLoginPrizeTake: data.is_login_send,
        intergral: data.member.intergral,
        today_intergral:data.today_intergral,
        is_login:data.is_login_send,
        intergralTip: data.intergral.intergralTip,
        releaseSendIntergral:data.intergral.releaseSendIntergral,
        inviteIntergral:data.intergral.inviteIntergral,
        LoginIntergral:data.intergral.LoginIntergral,
        SignIntergral:data.intergral.SignIntergral
      })
    })

    wx.showShareMenu({
      withShareTicket: true,
      menus: ['shareAppMessage', 'shareTimeline']
    })
  },
  
  // 跳转至积分记录页面
  lookIntergralLog:function(){
    wx.navigateTo({
      url: '../integralLog/integralLog',

    })
  },
  
  
  // 获取日期中月份的最后一天
  getLastDay(d){
    let current = new Date(d); 
    let currentMonth = current.getMonth();
    let nextMonth = ++currentMonth;
    let nextMonthDayOne = new Date(current.getFullYear(), nextMonth, 1);
    let minusDate = 1000 * 60 * 60 * 24;
    return new Date(nextMonthDayOne.getTime() - minusDate);
  },
  // 日历组件渲染完成事件
  afterCalendarRender() {
    console.log('日历组件初始化')
    const ym = this.calendar.getCurrentYM();
    
    this.loadCalendarSign(ym.month, ym.year, true)
  },

  // 渲染当前月份的签到信息
  loadCalendarSign(monthValue, yearValue, isThisMonth) {
    
    const that = this;
    let lastDay = this.getLastDay(`${yearValue}/${monthValue}/10`);
    const data = {
      year: yearValue,
      month: monthValue,
      one: 1,
      two: lastDay.getDate()
    }
    http.get('SignLog', data)
    .then(res => {
      
      if(!res.day) return
      const resArr = res.day.map(item => {
        if(isThisMonth) {
          if(item === new Date().getDate()) {
            that.setData({
              isTodaySign: true,
              signLabel: '已签到',
              isSign: true
            })
          }
        }
        return {
          year: res.year,
          month: res.month,
          day: item,
          todoText: '已签到',
          color: '#f40' 
        }
      })
      that.calendar.setTodoLabels({
        pos: 'bottom', // 待办点标记位置 ['top', 'bottom']
        dotColor: 'purple', // 待办点标记颜色
        showLabelAlways: true, // 点击时是否显示待办事项（圆点/文字），在 circle 为 true 及当日历配置 showLunar 为 true 时，此配置失效
        days: resArr
      });
    })
    .catch(err => {
      console.log('请求错误', err)
    })
  },



  // 改变月份事件
  whenChangeMonth(e) {
    console.log('whenChangeMonth', e)
    this.loadCalendarSign(e.detail.next.month, e.detail.next.year, false)
  },

  // 点击日历中的某天后的事件
  afterTapDay(e) {
    console.log(e.detail)
   
    if(e.detail.hasTodo || e.detail.todoText === '已签到') {
      this.setData({
        signLabel: '已签到',
        isSign: true
      })
    }else {
      this.setData({
        signLabel: '签到',
        isSign: false
      })
    }
  },

  // 签到事件
  onSign() {
    const that = this;
    const selectedDay = this.calendar.getSelectedDay({ lunar: true });
    console.log(selectedDay)
    console.log('今天', new Date().getDate())
    if(selectedDay[0].day != new Date().getDate()) {
      wx.showToast({
        title: '只能在今天签到',
        icon: 'none',
        duration: 1300
      })
      return
    }
    if(selectedDay[0].hasTodo || that.data.isTodaySign) {
      wx.showToast({
        title: '今日已签到',
        icon: 'none',
        duration: 1300
      })
      return 
    }
    this.calendar.setTodoLabels({
      // 待办点标记设置
      pos: 'bottom', // 待办点标记位置 ['top', 'bottom']
      dotColor: 'purple', // 待办点标记颜色
      // circle: true, // 待办圆圈标记设置（如圆圈标记已签到日期），该设置与点标记设置互斥
      showLabelAlways: true, // 点击时是否显示待办事项（圆点/文字），在 circle 为 true 及当日历配置 showLunar 为 true 时，此配置失效
      days: [
        {
          year: selectedDay[0].year,
          month: selectedDay[0].month,
          day: selectedDay[0].day,
          todoText: '已签到',
          color: '#f40' // 单独定义代办颜色 (标记点、文字)
        },
      ]
    });
    this.setData({
      isSign: true,
      signLabel: '已签到'
    })
    wx.showToast({
      title: '签到成功',
      icon: 'success',
      duration: 1300
    })
    

    http.get('Sign')
    .then(res => {
      that.setData({
        intergral: res.total,
        today_intergral: res.inter
      })
      console.log('签到结果', res)
    })
    .catch(err => console.log('签到同步服务器错误'))
  },

  // 显示日历组件
  showCalendar() {
    this.setData({
      calendarShow: true
    })
  },
  
  
  // 关闭日历组件
  closeCalendar() {
    this.setData({
      calendarShow: false
    })
  },

  // 返回上一页
  fanhui: function() {
    wx.navigateBack({
      delta: 1
    })
  },

  // 打开规则弹窗
  openRuleBox() {
    this.setData({
      modalName: 'Modal'
    })
  },
  // 关闭规则弹窗
  hideModal() {
    this.setData({
      modalName: ''
    })
  },

  // 领取积分
  getPrize(e) {
    const that = this;
    that.setData({
      isLoginPrizeTake: true,
    })
    http.get('LoginSend')
    .then(data => {
      if(data.status == 0){
        $Toast({
          content: data.msg,
          type: 'error'
        });
      }else{
        that.setData({
          today_intergral: data.inter,
          intergral:data.total
        })
        $Toast({
          content: data.msg,
          type: 'success'
        });
        
      }   
    })
  },

  // 发布
  onRelease() {
    wx.navigateTo({
      url: '../Release/Release',
    })
  },

  // 打开邀请弹窗提示
  inviteCoverHint() {
    this.setData({
      coverShow: !this.data.coverShow
    })
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
  },

  //分享自定义
  onShareAppMessage: function(e) {
      return {
        title: app.globalData.toptext,
        imageUrl: app.globalData.topimg,
        path: 'myxs_fodder/pages/start/start?id='+app.globalData.member.memberId, // 路径，传递参数到指定页面。
      }
  },
  //分享自定义
  onShareTimeline: function() {
      return {
        title: app.globalData.toptext,
        imageUrl: app.globalData.topimg
      }
  },
})