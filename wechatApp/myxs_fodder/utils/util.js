function formatTime(date) {
  var year = date.getFullYear()
  var month = date.getMonth() + 1
  var day = date.getDate()

  var hour = date.getHours()
  var minute = date.getMinutes()
  var second = date.getSeconds()


  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

function formatNumber(n) {
  n = n.toString()
  return n[1] ? n : '0' + n
}
/**
 * 函数节流
 * @param fn 需要进行节流操作的事件函数
 * @param interval 间隔时间
 * @returns {Function}
 */
function throttle(fn, interval) {
  let enterTime = 0; //触发的时间
  let gapTime = interval || 500; //间隔时间，如果interval不传，则默认500ms
  return function () {
    let context = this;
    let backTime = new Date(); //第一次函数return即触发的时间
    if (backTime - enterTime > gapTime) {
      fn.call(context, arguments[0]); //arguments[0]是事件处理函数默认事件参数event call绑定当前page对象
      enterTime = backTime; //赋值给第一次触发的时间，这样就保存了第二次触发的时间
    }
  };
}


/**
 * 部署格式化日期工具
 * @param date
 * @param fmt
 * @returns {*}
 */
function timeFormat(date, fmt) {
  var o = {
      "M+": date.getMonth() + 1,                 //月份
      "d+": date.getDate(),                    //日
      "h+": date.getHours(),                   //小时
      "m+": date.getMinutes(),                 //分
      "s+": date.getSeconds(),                 //秒
      "q+": Math.floor((date.getMonth() + 3) / 3), //季度
      "S": date.getMilliseconds()             //毫秒
  };
  if (/(y+)/.test(fmt))
      fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
  for (var k in o)
      if (new RegExp("(" + k + ")").test(fmt))
          fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
  return fmt;
}
/**
 * 函数防抖
 * @param fn 需要进行防抖操作的事件函数
 * @param interval 间隔时间
 * @returns {Function}
 */
function debounce(fn, interval) {
  let timer;
  let gapTime = interval || 1000; //间隔时间，如果interval不传，则默认1000ms
  return function () {
    clearTimeout(timer);
    let context = this;
    let args = arguments[0]; //保存此处的arguments，因为setTimeout是全局的，arguments无法在回调函数中获取，此处为闭包。
    timer = setTimeout(function () {
      fn.call(context, args); //args是事件处理函数默认事件参数event  call绑定当前page对象
    }, gapTime);
  };
}

function imageUtil(e) {
  var imageSize = {};
  var originalWidth = e.detail.width;//图片原始宽
  var originalHeight = e.detail.height;//图片原始高
  var originalScale = originalHeight/originalWidth;//图片高宽比
  console.log('originalWidth: ' + originalWidth)
  console.log('originalHeight: ' + originalHeight)
  //获取屏幕宽高
  wx.getSystemInfo({
    success: function (res) {
      var windowWidth = res.windowWidth;
      var windowHeight = res.windowHeight;
      var windowscale = windowHeight/windowWidth;//屏幕高宽比
      console.log('windowWidth: ' + windowWidth)
      console.log('windowHeight: ' + windowHeight)
      if(originalScale < windowscale){//图片高宽比小于屏幕高宽比
        //图片缩放后的宽为屏幕宽
         imageSize.imageWidth = windowWidth;
         imageSize.imageHeight = (windowWidth * originalHeight) / originalWidth;
      }else{//图片高宽比大于屏幕高宽比
        //图片缩放后的高为屏幕高
         imageSize.imageHeight = windowHeight;
         imageSize.imageWidth = (windowHeight * originalWidth) / originalHeight;
      }
    }
  })
  console.log('缩放后的宽: ' + imageSize.imageWidth)
  console.log('缩放后的高: ' + imageSize.imageHeight)
  return imageSize;
}


module.exports = {
  formatTime: formatTime,
  throttle,
  debounce,
  timeFormat,
  imageUtil,
}
