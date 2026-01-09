<template>
  <div class="app app-ti_green">
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed fixed-top rounded-corners nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"><!----></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">会员教程</div>
          </div>
        </div>
      </div>
    </div>
    <div data-v-4f48bf73="" data-v-f531b812="" class="course-container green custom">
      <van-collapse v-model="activeNames" class="custom">
        <van-collapse-item title="充值教程" name="1">
          <div data-v-4f48bf73="" class="item" @click="$parent.goNav('/usdtstu')">
            <div data-v-4f48bf73="" class="outer">
              <img data-v-4f48bf73="" alt="message.withdraw.usdt" class="icon" src="/static/image/uacPlmH1IC6AdoExAAAFIR6dmJ8010.png" lazy="loaded" />
            </div>
            <span data-v-4f48bf73="" class="name">虚拟币支付</span><i data-v-4f48bf73="" class="arrow van-icon van-icon-arrow"></i>
          </div>
          <div data-v-4f48bf73="" class="item"  @click="$parent.goNav('/usdtstu1')">
            <div data-v-4f48bf73="" class="outer" >
              <img data-v-4f48bf73="" alt="message.withdraw.BuyVirtual" class="icon" src="/static/image/uacPoGKgtxWANoPqAAARywJjaQU325.png" lazy="loaded" />
            </div>
            <span data-v-4f48bf73="" class="name">购买虚拟币</span><i data-v-4f48bf73="" class="arrow van-icon van-icon-arrow"></i>
          </div>
        </van-collapse-item>
        <van-collapse-item title="提现教程" name="2" class="custom">
          <div data-v-4f48bf73="" class="item" @click="$parent.goNav('/usdtstu2')">
            <div data-v-4f48bf73="" class="outer">
              <img data-v-4f48bf73="" alt="message.virtual.virtualWithdraw" class="icon" src="/static/image/uacPlmH1HyeAR5PqAAAFIR6dmJ8211.png" lazy="loaded" />
            </div>
            <span data-v-4f48bf73="" class="name">虚拟币提现</span><i data-v-4f48bf73="" class="arrow van-icon van-icon-arrow"></i>
          </div>
        </van-collapse-item>
        <!-- <van-collapse-item title="投注教程" name="3" class="custom">
          <div data-v-4f48bf73="" class="item">
            <div data-v-4f48bf73="" class="outer">
              <img data-v-4f48bf73="" alt="course.betting.sport" class="icon" src="/static/image/uacPlmIA_OiASr_JAAAGUsh6BfY388.png" lazy="loaded" />
            </div>
            <span data-v-4f48bf73="" class="name">体育教程</span><i data-v-4f48bf73="" class="arrow van-icon van-icon-arrow"></i>
          </div>
        </van-collapse-item> -->
      </van-collapse>
   
    </div>
  </div>
</template>
<script>
export default {
  name: 'vipstu',
  data() {
    return {
      url: null,
      activeNames: [],
    };
  },
  created() {
    let that = this;
  },
  methods: {},
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
.van-collapse-item__content {
  background-color: none !important;
}
.van-collapse-item__wrapper {
  background-color: #f6f7f9 !important;
}
</style>
