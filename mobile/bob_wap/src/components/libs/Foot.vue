<template>
  <van-tabbar v-model="type" @change="onChange" active-color="#9d4edd" inactive-color="#999999" class="fixed-bottom-tabbar">
    <van-tabbar-item>
      <span>首页</span>
      <template #icon="props">
        <svg v-if="props.active" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M10 20V14H14V20H19V12H22L12 3L2 12H5V20H10Z" fill="#9d4edd"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M10 20V14H14V20H19V12H22L12 3L2 12H5V20H10Z" fill="#999999"/>
        </svg>
      </template>
    </van-tabbar-item>
    <van-tabbar-item>
      <span>优惠</span>
      <template #icon="props">
        <svg v-if="props.active" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M20 6H4C2.9 6 2 6.9 2 8V16C2 17.1 2.9 18 4 18H20C21.1 18 22 17.1 22 16V8C22 6.9 21.1 6 20 6ZM20 16H4V8H20V16Z" fill="#9d4edd"/>
          <path d="M7 10H9V14H7V10ZM11 10H13V14H11V10ZM15 10H17V14H15V10Z" fill="#9d4edd"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M20 6H4C2.9 6 2 6.9 2 8V16C2 17.1 2.9 18 4 18H20C21.1 18 22 17.1 22 16V8C22 6.9 21.1 6 20 6ZM20 16H4V8H20V16Z" fill="#999999"/>
        </svg>
      </template>
    </van-tabbar-item>
    <van-tabbar-item class="kefu-tabbar-item">
      <template #icon="props">
        <img 
          v-if="props.active" 
          src="/static/image/diy/kfed.png" 
          class="kefu-icon-img"
        />
        <img 
          v-else 
          src="/static/image/diy/kf.png" 
          class="kefu-icon-img"
        />
      </template>
    </van-tabbar-item>
    <van-tabbar-item>
      <span>赚钱</span>
      <template #icon="props">
        <svg v-if="props.active" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M11.8 10.9C9.53 10.31 8.8 9.7 8.8 8.75C8.8 7.66 9.81 6.9 11.5 6.9C13.28 6.9 13.94 7.75 14 9H16.21C16.14 7.28 15.09 5.7 13 5.19V3H10V5.16C8.06 5.58 6.5 6.84 6.5 8.77C6.5 11.08 8.41 12.23 11.2 12.9C13.7 13.5 14.2 14.38 14.2 15.31C14.2 16 13.71 17.1 11.5 17.1C9.28 17.1 8.63 16.18 8.5 15H6.32C6.44 17.19 8.08 18.42 10 18.83V21H13V18.85C14.95 18.5 16.5 17.35 16.5 15.3C16.5 12.46 14.07 11.5 11.8 10.9Z" fill="#9d4edd"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M11.8 10.9C9.53 10.31 8.8 9.7 8.8 8.75C8.8 7.66 9.81 6.9 11.5 6.9C13.28 6.9 13.94 7.75 14 9H16.21C16.14 7.28 15.09 5.7 13 5.19V3H10V5.16C8.06 5.58 6.5 6.84 6.5 8.77C6.5 11.08 8.41 12.23 11.2 12.9C13.7 13.5 14.2 14.38 14.2 15.31C14.2 16 13.71 17.1 11.5 17.1C9.28 17.1 8.63 16.18 8.5 15H6.32C6.44 17.19 8.08 18.42 10 18.83V21H13V18.85C14.95 18.5 16.5 17.35 16.5 15.3C16.5 12.46 14.07 11.5 11.8 10.9Z" fill="#999999"/>
        </svg>
      </template>
    </van-tabbar-item>
    <van-tabbar-item>
      <span>我的</span>
      <template #icon="props">
        <svg v-if="props.active" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="#9d4edd"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px;">
          <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="#999999"/>
        </svg>
      </template>
    </van-tabbar-item>
  </van-tabbar>
</template>
<script>
//
export default {
  name: 'Foot',
  data() {
    return { type: 0 };
  },
  methods: {
    onChange(type) {
      let that = this;
      if (type == 0) {
        that.$parent.goNav(`/`);
      }
      if (type == 1) {
        that.$parent.goNav(`/activity`);
      }
      if (type == 2) {
        that.$parent.openKefu();
      }
      if (type == 3) {
        that.$parent.goNav(`/wallet`);
      }
      if (type == 4) {
        that.$parent.goNav(`/mine`);
      }
    },
    getCurrentType() {
      let that = this;
      let path = this.$route.path;
      if (path == '/') {
        return 0;
      }
      if (path == '/activity') {
        return 1;
      }
      if (path == '/kefu') {
        return 2;
      }
      if (path == '/wallet') {
        return 3;
      }
      if (path == '/mine') {
        return 4;
      }
      return 0;
    },
    changPath() {
      let that = this;
      that.type = that.getCurrentType();
    },
  },
  mounted() {},
  watch: {
    //监听路由地址的改变
    $route: {
      immediate: true,
      handler() {
        let that = this;
        that.changPath();
      },
    },
  },
};
</script>

<style lang="scss" scoped>
.van-tabbar-item {
  font-size: 12px;
  color: #999999;
}
.van-tabbar {
  height: 60px;
  position: relative;
}

.fixed-bottom-tabbar {
  position: fixed !important;
  bottom: 0 !important;
  left: 0 !important;
  right: 0 !important;
  width: 100% !important;
  border-top: 1px solid rgba(255,255,255,0.1) !important;
  box-sizing: border-box !important;
  background-color: rgba(0, 0, 0, 0.9) !important;
  backdrop-filter: blur(10px) !important;
  z-index: 200 !important;
}
.van-tabbar-item--active {
  background-color: transparent;
  color: #9d4edd;
}
.van-tabbar-item__icon {
  margin-bottom: 4px;
}

// 客服图标特殊样式
.kefu-tabbar-item {
  position: relative;
  
  .van-tabbar-item__icon {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    height: auto;
  }
  
  .kefu-icon-img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    border-radius: 50%;
    background-color: rgba(23, 24, 25, 0.9);
    backdrop-filter: blur(10px);
    padding:6px;
    box-sizing: border-box;
    display: block;
  }
  
  span {
    margin-top: 25px;
    position: relative;
    z-index: 1;
  }
}
</style>
