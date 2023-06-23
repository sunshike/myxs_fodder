// myxs_fodder/components/CommentEmojis/index.js
import datas from "../../dist/emojis.js"
Component({
  /**
   * 组件的属性列表
   */
  properties: {
    height: {
      type: String,
      value: '300'
    },
    isHide: {
      type: Boolean,
      value: true
    }
  },

  lifetimes: {
    created() {

    },
    attached: function () {},
    ready() {

      const {
        category,
        emotions,
        weibo_icon_url
      } = datas
      this.setData({
        emojis: this.convertData(emotions, weibo_icon_url)
      })
    },
    pageLifetimes: {

      show: function () {

      },
      hide: function () {
        // 页面被隐藏
      },
      resize: function (size) {
        // 页面尺寸变化
      }
    },

  },
  /**
   * 组件的初始数据
   */
  data: {
    emojis: [],
  },

  /**
   * 组件的方法列表
   */
  methods: {
    onload() {
      console.log('表情图片预加载完成')
    },
    convertData(emotions, weibo_icon_url) {
      const emojisList = {}
      for (const key in emotions) {
        if (emotions.hasOwnProperty(key)) {
          const ele = emotions[key];
          for (const item of ele) {
            let imgUrl = weibo_icon_url + item.icon
            emojisList[item.value] = {
              id: item.id,
              value: item.value,
              icon: item.icon.replace('/', '_'),
              url: imgUrl,
              className: `IMG_00${ 60 + ( +item.id ) }`
            }
          }
        }
      }
      return emojisList
    },

    addEmoijs(e) {
      this.triggerEvent("addemoijs", {
        "item": e.currentTarget.dataset.item
      })
    },
    closeEmojis() {
      this.triggerEvent("closeemoijs")
    },
  }
})