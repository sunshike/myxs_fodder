// pages/Daysign/Daysign.js
import http from '../../utils/request.js';
let ewm = null;
Page({
  data: {
    animationData: '',
    shareText: '分享',
    iconBl: 1
  },

  onLoad: function (options) {
    const that = this;
    this.dataShow();
    this.dayObtain();
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          pixel: res.pixelRatio
        })
      },
    })

  },

  ReturnNew: function () {
    wx.navigateBack({
      delta: 1
    }) 
  },

  dataShow: function () {
    const that = this;

    http.get('ListDaySign')
    .then(data => {
      wx.getImageInfo({
        src: data.sign_img,
        success: function (res) {
          that.setData({
            imgBd: res.path
          })
        }
      })
      if (data.qr_img!=''){
        wx.getImageInfo({
          src: data.qr_img,
          success: function (res) {
            ewm = res.path
          }
        })
      }
      wx.getImageInfo({
        src: data.day_url,
        success: function (res) {
          that.setData({
            Rqiimg:res.path
          })
        }
      })
      that.setData({
        DaySign: data
      })
    });
  },
  dayObtain: function () {
    var week = "周" + "日一二三四五六".charAt(new Date().getDay());
    var e = { '周一': 'MONDAY', '周二': 'TUESDAY', '周三': 'WEDNESDAY', '周四': 'THURSDAY', '周五': 'FRIDAY', '周六': 'SATURDAY', '周日': 'SUNDAY' }
    var day = new Date().getDate();
    var n = new Date().getFullYear()
    var y = new Date().getUTCMonth() + 1

    this.setData({
      week: week, //周几
      day: day,//日期
      n: n, //年
      y: y, // 月
      eW: e[week] //周几的英语
    })
  },
  //点击下载
  formSubmit: function (e) {
    const that = this;
    var animation = wx.createAnimation({
      duration: 1500,
      timingFunction: "ease",
      delay: 0,
      transformOrigin: "50% 50%",
    })
    animation.width(180).step()

    this.setData({
      animationData: animation.export(),
      shareText: '保存成功',
      iconBl: 0
    })
    setTimeout(function () {
      that.drawImg();
    }, 1000)
  },
  //点击保存到相册

  baocun : function () {
    var animation = wx.createAnimation({
      duration: 800,
      timingFunction: "ease",
      delay: 0,
      transformOrigin: "50% 50%",
    })
    animation.width(62).step()

    this.setData({
      animationData: animation.export(),
      shareText: '分享',
      iconBl: 1
    })

  },

  drawImg() {
    var that = this;
    var cW = 750;
    var cH = 1134;
    var imgW = cW;

    var imgH = 500;
    var pixel = this.data.pixelRatio;
    let context = wx.createCanvasContext('share')
                                   
    context.setFillStyle('#fff')
    context.fillRect(0, 0, cW, cH)

    context.drawImage(that.data.imgBd, 0, 0, imgW, imgH);//头图
    if (ewm != null){
      context.drawImage(ewm, (cW - 160) / 2, cH - 220, 160, 160);//二维码

    }
    context.drawImage(that.data.Rqiimg, (cW - 320) / 2, imgH - 150, 320, 320);//日期

    context.setFontSize(38)
    context.setFillStyle("rgb(177, 177, 177)")
    context.fillText(that.data.n + '/' + that.data.y, 30, imgH + 50)

    context.setFontSize(32)
    context.fillText(that.data.eW, cW - context.measureText(that.data.eW).width-30, imgH + 100)

    context.setFontSize(38)
    context.setFillStyle("#000")
    context.fillText(that.data.week, cW - context.measureText(that.data.week).width-30, imgH + 50)


    var text = that.data.DaySign.sign_content;//这是要绘制的文本
    var chr = text.split("");//这个方法是将一个字符串分割成字符串数组
    var temp = "";
    var row = [];
    context.setFontSize(28)
    context.setFillStyle("#000")
    for (var a = 0; a < chr.length; a++) {
      if (context.measureText(temp).width < 520) {
        temp += chr[a];
      }
      else {
        a--; //这里添加了a-- 是为了防止字符丢失，效果图中有对比
        row.push(temp);
        temp = "";
      }
    }
    row.push(temp);
    //如果数组长度大于2 则截取前两个
    if (row.length > 2) {
      var rowCut = row.slice(0, 2);
      var rowPart = rowCut[1];
      var test = "";
      // var empty = [];
      for (var a = 0; a < rowPart.length; a++) {
        if (context.measureText(test).width < 220) {
          test += rowPart[a];
        }
        else {
          break;
        }
      }

    }
    for (var b = 0; b < row.length; b++) {
      context.fillText(row[b],115 , imgH + 280 + b * 50);
    }
    context.setFontSize(58)
    context.setFillStyle("#000")

    context.fillText(that.data.DaySign.sign_title, (imgW - context.measureText(that.data.DaySign.sign_title).width) / 2, imgH + 210)

    context.draw()
    setTimeout(function () {
      wx.canvasToTempFilePath({
        canvasId: 'share',
        success: function (res) {
          var tempFilePath = res.tempFilePath;
          that.setData({
            imagePath: tempFilePath,
          });
          wx.saveImageToPhotosAlbum({
            filePath: tempFilePath,
            success(res) {
              wx.showModal({
                title:'日签已成功保存至相册',
                content: '快去分享朋友圈，叫朋友给你点赞 ~',
                showCancel: false,
                confirmText:'我知道了',
                confirmColor:'#ff4081',
                success: function (res) {
                  var animation = wx.createAnimation({
                    duration: 800,
                    timingFunction: "ease",
                    delay: 0,
                    transformOrigin: "50% 50%",
                  })
                  animation.width(62).step()

                  that.setData({
                    animationData: animation.export(),
                    shareText: '分享',
                    iconBl: 1
                  })

                  
                }
              });
            }
          })
        },
        fail: function (res) {
        }
      });
    }, 200);


  },
  //分享自定义
  onShareAppMessage: function (e) {
    var that = this;
    var i = e.target.dataset.index
    var data = {
      types: 'fx',
      id: e.target.dataset.id
    }

    http.post('operation', data).then(data => {
      that.data.dataList[i].sharenb = parseInt(that.data.dataList[i].sharenb) + 1
      that.setData({
        dataList: that.data.dataList
      })
    })
    return {
      path: 'myxs_fodder/pages/start/start' // 路径，传递参数到指定页面。
    }

  },
})