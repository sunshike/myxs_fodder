var t = getApp();
import http from '../../utils/request.js';
const { $Toast } = require('../../iview_ui/base/index');
let siteroot = t.siteInfo.siteroot;
siteroot = siteroot.replace('app/index.php', 'web/index.php');
let upurl = siteroot + '?i=' + t.siteInfo.uniacid + '&c=utility&a=file&do=upload&thumb=0';

Page({
  data: {
    coverShow: false,
    first: !0,
    windowWidth: 0,
    windowHeight: 0,
    previewCtxWidth: 0,
    previewCtxHeight: 0, 
    downImgCtxWidth: 0,
    downImgCtxHeight: 0,
    arrIndex: 0,
    array: ["倾斜平铺", "四角及中间", "居中", "左上角", "右上角", "左下角", "右下角"],
    index: 0,
    images: ["../../images/rqian.jpg"],
    imageSize: [],
    text: '水印文字',
    colorArr: ["#9A9A9A", "#cccccc", "#FF8C00", "#ff0062", "#4169E1", "#458B00"],
    colorIndex: 0,
    erwei: ""
  },
  onLoad: function(o) {
    var e = this;
    wx.getStorage({
      key: "text",
      success: function(t) {
        var a = t.data;
        e.setData({
          text: a
        });
      }
    }), wx.getStorage({
      key: "colorIndex",
      success: function(t) {
        var a = t.data;
        e.setData({
          colorIndex: a
        });
      }
    }), wx.getStorage({
      key: "arrIndex",
      success: function(t) {
        var a = t.data;
        e.setData({
          arrIndex: a
        });
      }
    });
    e.setData({
      text: t.globalData.member.watermark.text,
      colorIndex: t.globalData.member.watermark.colorIndex,
      arrIndex: t.globalData.member.watermark.arrIndex,

    })
    if (t.globalData.member.watermark.erwei != '' && t.globalData.member.watermark.erwei != undefined) {
      e.setData({
        erweiType: 1,
        erwei: t.globalData.member.watermark.erwei,
      })
    }
  },
  onReady: function() {
    var t = this;
    wx.getSystemInfo({
      success: function(e) {
        var a = e.windowWidth,
          i = .5 * e.windowHeight;
        t.setData({
          windowWidth: a,
          windowHeight: i
        }), t.drawPreviewCursor(t.data.images[0]);
      }
    });
  },
  onShareAppMessage: function() {
    return {
      title: "一键批量给图片加水印",
      path: "/pages/index/index",
      success: function(t) {},
      fail: function(t) {}
    };
  },
  selectColor: function(t) {
    var e = t.currentTarget.dataset.index;
    this.setData({
      colorIndex: e
    }), wx.setStorage({
      key: "colorIndex",
      data: e
    }), this.drawPreviewCursor(this.data.images[this.data.index]);
  },
  bindPickerChange: function(t) {
    var e = t.detail.value;
    this.setData({
      arrIndex: e
    }), wx.setStorage({
      key: "arrIndex",
      data: e
    }), this.drawPreviewCursor(this.data.images[this.data.index]);
  },
  
  getLineTexts: function(t) {
    for (var e = "", a = 0; a < 12; a++) e = e + t + "                  ";
    return e;
  },
  fillText: function(t, e, a, i) {
    var s = this.data.colorArr[this.data.colorIndex];
    t.setFillStyle(s), t.setFontSize(15);
    var r = this.data.arrIndex,
      n = a / 2,
      o = i / 2;
    if (0 == r) {
      t.setTextAlign("left"), t.translate(n, o), t.rotate(30 * Math.PI / 180);
      for (var d = this.getLineTexts(e), g = 0; g < 6; g++) t.fillText(d, -n - 100, 100 * g + 30 - o);
    } else 1 == r ? (t.setTextAlign("center"), t.fillText(e, n, o), t.setTextAlign("left"),
      t.fillText(e, 20, 30), t.setTextAlign("right"), t.fillText(e, a - 20, 30), t.setTextAlign("left"),
      t.fillText(e, 20, i - 30 + 15), t.setTextAlign("right"), t.fillText(e, a - 20, i - 30 + 15)) : 2 == r ? (t.setTextAlign("center"),
      t.fillText(e, n, o)) : 3 == r ? (t.setTextAlign("left"), t.fillText(e, 20, 30)) : 4 == r ? (t.setTextAlign("right"),
      t.fillText(e, a - 20, 30)) : 5 == r ? (t.setTextAlign("left"), t.fillText(e, 10, i - 30 + 15)) : 6 == r && (t.setTextAlign("right"),
      t.fillText(e, a - 20, i - 30 + 15));
  },
  getPreviewRate: function(t, e) {
    var a = t / this.data.windowWidth,
      i = e / this.data.windowHeight,
      s = i;
    return a > i && (s = a), s;
  },
  drawPreviewCursor: function(t) {
    var e = this;
    wx.getImageInfo({
      src: t,
      success: function(a) {
        var i = a.width,
          s = a.height,
          r = e.getPreviewRate(i, s),
          n = i / r,
          o = s / r,
          d = wx.createCanvasContext("previewCanvas");

        d.restore(),
          d.draw(),
          e.setData({
            previewCtxWidth: n,
            previewCtxHeight: o
          }),
          d.drawImage(t, 0, 0, n, o);
        if (e.data.erwei != undefined && e.data.erwei != " ") {
          d.drawImage(e.data.erwei, n - n / 8 - 20, o - n / 8 - 30, n / 8, n / 8)
        }
        e.fillText(d, e.data.text, n, o);


        d.draw();
      }
    });
  },
  drawDownImgCursor: function(t, e, a) {
    var i = this;
    wx.getImageInfo({
      src: e,
      success: function(s) {
        var r = s.width,
          n = s.height,
          o = (i.getPreviewRate(r, n), r),
          d = n;
        t.restore(), t.draw(), i.setData({
          downImgCtxWidth: o,
          downImgCtxHeight: d
        }), t.drawImage(e, 0, 0, o, d), i.fillText(t, i.data.text, o, d), t.draw(), a();
      }
    });
  },
  
  inputHandler: function(t) {
    var e = t.detail.value;
    this.setData({
      text: e
    }), this.drawPreviewCursor(this.data.images[this.data.index]);
  },
  inputBlur: function(t) {
    var e = t.detail.value;
    wx.setStorage({
      key: "text",
      data: e
    });
  },
  //保存图片
  SaveImg: function() {
    var that = this;
    wx.chooseImage({
      count: that.data.szie,
      sizeType: ['original'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
      success: function(res) {

        that.data.szie = that.data.szie - res.tempFilePaths.length
        that.setData({
          fileType: 'img',
          szie: that.data.szie
        })
        if (that.data.szie == 0) {
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

    that.setData({
      coverShow: true
    })
    const jd = wx.uploadFile({
      url: upurl + '&upload_type=img',
      filePath: url,
      name: 'file',

      success: function(res) {
        if (JSON.parse(res.data).message != undefined) {
         
          $Toast({
            content: JSON.parse(res.data).message,
        });
        }


        that.setData({
          erweiType: 1,
          erwei: JSON.parse(res.data).url,
        }), that.drawPreviewCursor(that.data.images[that.data.index]);
      }


    })

    jd.onProgressUpdate((res) => {

      if (res.progress == 100) {
        that.setData({
          coverShow: false
        })
      }

    })

  },
  Sub: function() {
    var data = {
      index: this.data.index,
      text: this.data.text,
      colorArr: this.data.colorArr,
      colorIndex: this.data.colorIndex,
      array: this.data.array,
      arrIndex: this.data.arrIndex,
      erwei: this.data.erwei
    }

    http.post('UpdateWatermark', data).then(data => {
      if (data) {
        this.setData({
          erwei: this.data.erwei
        })
        $Toast({
          content: '保存成功',
          type: 'success'
      });
        setTimeout(function() {
          wx.navigateBack({
            delta: 1
          })
        }, 2000)
      } else {
        
        $Toast({
          content: '未知错误',
          type: 'error'
      });
      }
    });
  },
  fanhui: function() {
    wx.navigateBack({
      delta: 1
    })
  }
});