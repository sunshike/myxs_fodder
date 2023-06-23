// myxs_fodder/components/Loading/Loading.js
Component({
  /** 
   * 组件的属性列表
   */
  properties: {
    progress: {
      type: String,
      value: '0',
    },
    isDownload: {
      type: String,
      value: '',
    },
    title: {
      type: String,
      value: '水印图片生成中',
    }
  },

})
