let load = true;
const app = getApp();
import http from '../../utils/request.js';
let hasRequest = false 
Component({
  data: {
    TabCur: 0,
    MainCur: 0,
    VerticalNavTop: 0,
    list: [],
    
    listItems: [],
  },
  properties: {
    dataList: {
      type: Array,
      value: [],
    },
    canRequest: {
      type: Boolean,
      value: false
    }
  }, 
  lifetimes: {
    // 在组件实例进入页面节点树时执行
    attached: function() {
      const tempArr = JSON.parse(JSON.stringify(app.globalData.class))
      tempArr.forEach(item => {
        item.class_id = (+item.class_id) - 1
      })
      this.setData({
        list: tempArr
      })
    },
    detached: function() {
      // 在组件实例被从页面节点树移除时执行
    },
  },
  methods: {
    formatDate(date) {
      var date = new Date(date);
      var YY = date.getFullYear() + '-';
      var MM = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
      var DD = (date.getDate() < 10 ? '0' + (date.getDate()) : date.getDate());
      var hh = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
      var mm = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) + ':';
      var ss = (date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds());
      return YY + MM + DD;
    },
    requestData(class_id, index) {
      const that = this;
      http.get('GetListByClassId', { class_id })
      .then(res => {
        res.forEach(item => {
          item.create_time = that.formatDate((+item.create_time) * 1000) 
        })
        that.setData({
          [`listItems[${index}]`]: res
        })
        
      })
    },

    tabSelect(e) {
      this.requestData((+e.currentTarget.dataset.id) + 1, e.currentTarget.dataset.id)
      this.setData({
        TabCur: e.currentTarget.dataset.id,
        MainCur: e.currentTarget.dataset.id,
        VerticalNavTop: (e.currentTarget.dataset.id - 1) * 50
      })
    },
    VerticalMain(e) {
      let that = this;
      let list = this.data.list;
      let tabHeight = 0;
      if (load) {
        for (let i = 0; i < list.length; i++) {
          let view = this.createSelectorQuery().select("#main-" + list[i].class_id);
          view.fields({
            size: true
          }, data => {
            list[i].top = tabHeight;
            tabHeight = tabHeight + data.height;
            list[i].bottom = tabHeight;     
          }).exec();
        }
        load = false;
        that.setData({
          list: list
        })
      }
      let scrollTop = e.detail.scrollTop + 20;
      for (let i = 0; i < list.length; i++) {
        if (scrollTop > list[i].top && scrollTop < list[i].bottom) {
          that.setData({
            VerticalNavTop: ((+list[i].class_id) - 1) * 50,
            TabCur: (+list[i].class_id)
          })
          return false
        }
      }
    },
    toDetail(e) {
      wx.navigateTo({
        url: `../../pages/material-detail/index?id=${e.currentTarget.dataset.id}`,
      })
    },
  }
  
})