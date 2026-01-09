<template>
  <div class="acts" v-if="activitytypeList.length > 0">
    <div class="pageTop">优惠活动</div>
    <van-tabs v-model="actType" class="topsa" @click="activitylist">
      <van-tab title="全部" :name="''"> </van-tab>
      <van-tab v-for="(item, index) in activitytypeList" :name="item.id" :title="item.name" :key="index"> </van-tab>
    </van-tabs>
    <div class="consg">
      <div class="lis" v-for="(item, index) in activitylistList" :key="index" @click="$parent.goNav(`/activityInfo?id=${item.id}`)">
        <img :src="item.banner" alt="" />
        <div class="tite_sf">{{ item.title }}</div>
      </div>
      <van-divider dashed class="end-divider">END</van-divider>
    </div>
  </div>
</template>
<script>
export default {
  name: 'activity',
  data() {
    return {
      activitytypeList: [],
      actType: '',
      activitylistList: [],
    };
  },
  created() {
    let that = this;
    that.activitytype();
    that.activitylist();
  },
  methods: {
    activitytype() {
      let that = this;
      that.$apiFun.post('/api/activitytype', {}).then(res => {
        console.log(res);
        if (res.code !== 200) {
          that.$parent.showTost(0, res.message);
        }
        if (res.code === 200) {
          that.activitytypeList = res.data;
        }
      });
    },
    activitylist() {
      let that = this;
      let info = that.actType == '' ? {} : { type: that.actType };
      that.$parent.showLoading();
      that.$apiFun.post('/api/activitylist', info).then(res => {
        console.log(res);
        if (res.code !== 200) {
          that.$parent.showTost(0, res.message);
        }
        if (res.code === 200) {
          that.activitylistList = res.data.data;
        }
        that.$parent.hideLoading();
      });
    },
    changActType(val) {
      let that = this;
      if (val == that.actType) {
        return;
      }
      that.actType = val;
      that.activitylist();
    },
  },
  mounted() {
    let that = this;
    // 直接操作 DOM 移除内边距
    that.$nextTick(() => {
      const navElements = document.querySelectorAll('.topsa .van-tabs__nav--line.van-tabs__nav--complete');
      navElements.forEach(el => {
        if (el instanceof HTMLElement) {
          el.style.paddingLeft = '0';
          el.style.paddingRight = '0';
          el.style.padding = '0';
        }
      });
    });
  },
  updated() {
    let that = this;
  },
  beforeDestroy() {},
};
</script>
<style lang="scss" scoped>
  .topsa :deep(.van-tabs__nav) {
    padding: 0px !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 强制移除 .van-tabs__nav--line.van-tabs__nav--complete 的左右内边距
  .topsa :deep(.van-tabs__nav--line.van-tabs__nav--complete) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  .topsa :deep(.van-tabs__nav.van-tabs__nav--line.van-tabs__nav--complete) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 更具体的选择器，确保覆盖所有可能的样式
  .topsa :deep(.van-tabs__wrap .van-tabs__nav--line.van-tabs__nav--complete) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  .topsa :deep(.van-tabs__wrap .van-tabs__nav.van-tabs__nav--line.van-tabs__nav--complete) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 直接针对所有包含这些类的元素
  .topsa :deep([class*="van-tabs__nav--line"][class*="van-tabs__nav--complete"]) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 使用属性选择器确保覆盖
  .topsa :deep(div[role="tablist"].van-tabs__nav--line.van-tabs__nav--complete) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 针对所有可能的变体
  .topsa :deep(.van-tabs__nav--complete) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
.acts {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
}

.pageTop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 50px;
  line-height: 50px;
  text-align: center;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(10px);
  color: #ffffff;
  z-index: 200;
  font-weight: bold;
}

.topsa {
  position: fixed;
  top: 49px;
  left: 0;
  width: 100%;
  z-index: 200;
  
  :deep(.van-tabs__wrap) {
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  :deep(.van-tabs) {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  :deep(.van-tab) {
    color: rgba(255, 255, 255, 0.8);
  }
  
  :deep(.van-tab--active) {
    color: #9d4edd;
  }
  
  :deep(.van-tabs__line) {
    background-color: #9d4edd;
  }
}

.consg {
  padding: 90px 15px 120px 15px;
  .lis {
    margin-top: 20px;
    border-radius: 10px;
    -webkit-box-shadow: 0 2px 4px 0 rgb(0 0 0 / 10%);
    box-shadow: 0 2px 4px 0 rgb(0 0 0 / 10%);
    padding-bottom:10px ;
    img {
      width: 100%;
      min-height: 200px;
    }
    .tite_sf {
      font-size: 12px;
      font-weight: 700;
      color: #ffffff;
      height: 30px;
      line-height: 30px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      box-sizing: border-box;
      padding: 0 20px;
      background: rgba(0, 0, 0, 0.3);
    }
  }
}

.end-divider {
  :deep(.van-divider__text) {
    color: rgba(255, 255, 255, 0.6) !important;
  }
  
  :deep(.van-divider__line) {
    border-color: rgba(255, 255, 255, 0.2) !important;
  }
}
</style>

<style lang="scss">
// 全局样式，确保覆盖所有情况
.topsa {
  padding-left: 0 !important;
  padding-right: 0 !important;
  
  .van-tabs {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  .van-tabs__wrap {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  .van-tabs__nav--line.van-tabs__nav--complete {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .van-tabs__nav.van-tabs__nav--line.van-tabs__nav--complete {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .van-tabs__wrap .van-tabs__nav--line.van-tabs__nav--complete {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .van-tabs__wrap .van-tabs__nav.van-tabs__nav--line.van-tabs__nav--complete {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 使用属性选择器
  [class*="van-tabs__nav--line"][class*="van-tabs__nav--complete"] {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  
  // 针对所有可能的元素
  div[role="tablist"] {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
}
</style>
