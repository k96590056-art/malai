<template>
  <div class="app-wrapper">
    <div class="app-title">{{ $store.state.appInfo.title }}app下载</div>

    <img style="width: 100%; margin-top: 20px" src="/static/image/sports_and_2_title-eb044a43aca57221f5bdc248cbb1356d.png" alt="" />

    <van-swipe class="my-swipe" :autoplay="3000" indicator-color="white">
      <van-swipe-item><img style="width: 100%" src="/static/image/sports_8.png" alt="" /></van-swipe-item>
      <van-swipe-item><img style="width: 100%" src="/static/image/sports_71.png" alt="" /></van-swipe-item>
      <van-swipe-item><img style="width: 100%" src="/static/image/sports_aa.png" alt="" /></van-swipe-item>
      <van-swipe-item><img style="width: 100%" src="/static/image/sports_and4.png" alt="" /></van-swipe-item>
    </van-swipe>
    <img style="width: 100%; margin-top: 20px" src="/static/image/sports_and_1_icon-c53b51a31998cee56a723ab1dcaa4f91.png" alt="" />
    <div class="app-desc">可扫描下发二维码进行app下载</div>

    <img class="app-qrcode" :src="$store.state.appInfo.ios_download_qrcode" alt="" />

    <van-button class="download-btn" style="width: 90%; margin: 0 auto; margin-top: 30px; display: block" @click="$parent.goNav('/gamePage?app=1')">立即下载</van-button>
    <div class="app-tips">需在同一网络环境下下载安装注册，请勿切换网络； 若无法正常安装，请使用手机自带浏览器打开本页面</div>
    <div style="height: 100px"></div>
  </div>
</template>
<script>
export default {
  name: 'app',
  data() {
    return {};
  },
  created() {
    let that = this;
  },
  methods: {
    //下载文件
    downloadUrl(url) {
      let that = this;
      this.$parent.showLoading();

      const iframe = document.createElement('iframe');
      iframe.src = url;
      iframe.style.display = 'none'; // 防止影响页面
      iframe.style.height = 0; // 防止影响页面
      document.body.appendChild(iframe); // 必写，iframe挂在到dom树上才会发请求
      // 定时删除节点
      setTimeout(() => {
        that.$parent.hideLoading();

        document.body.removeChild(iframe);
      }, 2000);
    },
  },
  mounted() {
    let that = this;
  },
  updated() {
    let that = this;
  },
  beforeDestroy() {},
};
</script>
<style lang="scss" scoped>
// 页面容器 - 参考首页的深色主题
.app-wrapper {
  width: 100%;
  min-height: 100vh;
  background: #000000;
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
  padding-bottom: 20px;
}

.app-title {
  font-size: 0.5rem;
  height: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: #9d4edd;
  text-shadow: 0 0 10px rgba(157, 78, 221, 0.8);
  padding-top: 20px;
}

.app-desc {
  width: 90%;
  margin: 0 auto;
  color: rgba(255, 255, 255, 0.8);
  text-align: center;
  margin-top: 20px;
}

.app-qrcode {
  width: 70%;
  margin: 0 auto;
  display: block;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.download-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border: none !important;
  color: #ffffff !important;
  font-weight: 600 !important;
}

.app-tips {
  width: 90%;
  margin: 10px auto;
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.28rem;
  line-height: 1.6;
  text-align: center;
}
</style>
