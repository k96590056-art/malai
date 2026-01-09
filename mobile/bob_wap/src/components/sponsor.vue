<template>
  <div data-v-f531b812=""  class="app app-ti_green">
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed rounded-corners nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">赞助信息</div>
          </div>
        </div>
        
      </div>
    </div>
    <ul data-v-7b352665="" data-v-f531b812="" class="sponsorship-list">
      <li data-v-7b352665="" @click="$parent.goNav('sponsor1')"><img data-v-7b352665="" alt=""  src="/static/image/uacPoGKLdDWAC1LXAAXGwqGkRIA062.png" lazy="loaded" /></li>
      <li data-v-7b352665="" @click="$parent.goNav('sponsor2')"><img data-v-7b352665="" alt=""  src="/static/image/uacPoGKLdHGAGTzSAAXmQer4sMQ909.png" lazy="loaded" /></li>
    </ul>
    <div data-v-f531b812="" class="float-divbox"></div>
    <span data-v-7b0f8a3e="" data-v-f531b812="" class="customer-service-container"></span><span data-v-f531b812=""></span>
    <div data-v-55ec3770="" data-v-f531b812="" class="select-service-line-view select-service-line-view">
      <dl data-v-55ec3770="" class="select-service-list"><div data-v-55ec3770="" style="height: 55px"></div></dl>
    </div>
    
  </div>
</template>
<script>
export default {
  name: 'sponsor',
  data() {
    return {
      url: null,
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
@import '../../static/css/sponsorship.85b0e97d.css';
</style>
