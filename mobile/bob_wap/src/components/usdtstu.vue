<template>
  <div data-v-f531b812="" class="app app-ti_green">
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed fixed-top nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">虚拟货币支付</div>
          </div>
        </div>
      </div>
    </div>
    <div data-v-f531b812="" class="recharge-course-wrapper theme-green">
      <div class="step">
        <van-tabs v-model="tabindex"  @change="onClickTab">
          <van-tab title="1. 选择数字货币 "> </van-tab>
          <van-tab title="2. 输入充值金额">  </van-tab>
          <van-tab title="3. 复制地址或进行扫码">  </van-tab>
          <van-tab title="4. 打开并登录欧易软件">  </van-tab>
          <van-tab title="5. 进入“资金”选择“提现”"> </van-tab>
          <van-tab title="6. 选择USDT币种">  </van-tab>
          <van-tab title="7. 点击提交按钮">  </van-tab>
        </van-tabs>

      </div>
      <div class="swiper">
        <van-swipe @change="onChange" ref="swiper">
          <van-swipe-item v-for="item in 7" :key="item">
            <div class="mobile-warpper" lazy="loaded" style="background-image: url('/static/image/mobile-recharge-bg.10e835b3.png')"><img style="width: 6.48rem; padding: 0.4rem 0" alt="选择数字货币" :src="`/static/image/zhifu${item}.png`" /></div>
          </van-swipe-item>
        </van-swipe>
      </div>
    </div>
    <div data-v-f531b812="" class="float-divbox"></div>
    <span data-v-7b0f8a3e="" data-v-f531b812="" class="customer-service-container"></span><span data-v-f531b812=""></span>
    <div data-v-55ec3770="" data-v-f531b812="" class="select-service-line-view select-service-line-view">
      <dl data-v-55ec3770="" class="select-service-list"><div data-v-55ec3770="" style="height: 55px"></div></dl>
    </div>
  </div>
</template>
<script>
export default {
  name: 'usdtstu',
  data() {
    return {
      url: null,
      activeNames: [],
      tabindex: 0,
    };
  },
  created() {
    let that = this;
  },
  methods: {
    onClickTab(e) {
      let that = this;
      that.tabindex = e;
      this.$refs.swiper.swipeTo(e);
    },
    onChange(e) {
      let that = this;
      that.tabindex = e;
    },
  },
  mounted() {
    let that = this;
  },
  updated() {
    let that = this;
  },
};
</script>

<style lang="scss" scoped>
@import '../../static/css/chunk-vendors.a58c2457.css';
@import '../../static/css/course.d85a2eb9.css';
@import '../../static/css/chunk-3de6e5e7.b3aa2600.css';
.van-swipe {
  padding-bottom: 30px;
}


</style>
