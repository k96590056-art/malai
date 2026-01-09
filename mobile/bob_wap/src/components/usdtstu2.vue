<template>
  <div data-v-f531b812="" class="app app-ti_green" >
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed fixed-top nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">虚拟币提现</div>
          </div>
        </div>
      </div>
    </div>
    <div data-v-f531b812="" class="recharge-course-wrapper theme-green">
      <div data-v-4d81d42e="" class="tip" style="width:90%;margin:10px auto">提现前，请先确保您已成功登录账号。</div>
      <div class="step">
        <van-tabs v-model="tabindex"  @change="onClickTab">
          <van-tab title=" 1. 点击我的 (个人信息)"> </van-tab>
          <van-tab title=" 2. 卡片管理"> </van-tab>
          <van-tab title=" 3. 添加虚拟币地址"> </van-tab>
          <van-tab title=" 4. 填写虚拟币地址信息"> </van-tab>
          <van-tab title=" 5. 选择虚拟币提现/立即取款"> </van-tab>
          <van-tab title=" 6. 等待审核"> </van-tab>


        </van-tabs>

      </div>
      <div class="swiper">
        <van-swipe @change="onChange" ref="swiper">
          <van-swipe-item v-for="item in 6" :key="item">
            <div class="mobile-warpper" lazy="loaded" style="background-image: url('/static/image/mobile-recharge-bg.10e835b3.png')"><img style="width: 6.48rem; padding: 0.4rem 0" alt="选择数字货币" :src="`/static/image/tixian${item}.png`" /></div>
          </van-swipe-item>
        </van-swipe>
      </div>
    </div>
    <div data-v-4d81d42e="" class="text" style="width:90%;margin:30px auto;padding-bottom:50px"><p data-v-4d81d42e=""> 1.第一次提现到数字货币时，请先绑定提币地址，绑定提币地址的步骤： <span data-v-4d81d42e="" style="color: rgb(255, 12, 0);">登录平台-个人信息-卡片管理-添加数字货币地址</span></p><p data-v-4d81d42e="">2.输入提币金额，点击‘立即取款’，提币申请提交成功</p><p data-v-4d81d42e="">3.提币审核成功后请登录交易网站或数字货币官方网站核实到账金额</p></div>
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
