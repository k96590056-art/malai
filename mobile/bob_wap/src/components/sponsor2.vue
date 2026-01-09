<template>
  <div data-v-f531b812="" id="app" class="app app-ti_green">
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed fixed-top rounded-corners nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">赞助信息</div>
          </div>
        </div>
      </div>
    </div>
    <div data-v-0b72266c="" data-v-f531b812="" class="wolf-fort-view" style="background: url('/static/image/topImg.94c4d572.png') rgb(34, 0, 0)" data-src="/static/image/topImg.94c4d572.png" lazy="loaded">
      <div data-v-0b72266c="" class="bg-img"><img data-v-0b72266c="" class="content-img" data-src="/static/image/content-img.9abd96a4.png" src="/static/image/content-img.9abd96a4.png" lazy="loaded" /></div>
      <div data-v-0b72266c="" class="video-title-view"></div>
      <div data-v-33c420b2="" data-v-0b72266c="" class="swiper-view">
        <div data-v-33c420b2="" class="swiper-container conventionalSwiper swiper-container-coverflow swiper-container-3d swiper-container-initialized swiper-container-horizontal swiper-container-ios">
          <div class="swiper-wrapper">
            <div data-v-33c420b2="" class="swiper-slide">
              <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-1.97b7c8b0.png" src="/static/image/slide-1.97b7c8b0.png" lazy="loaded" />
            </div>
            <div data-v-33c420b2="" class="swiper-slide">
              <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-2.df9fc9a6.png" src="/static/image/slide-2.df9fc9a6.png" lazy="loaded" />
            </div>
            <div data-v-33c420b2="" class="swiper-slide">
              <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-3.70ec1ea7.png" src="/static/image/slide-3.70ec1ea7.png" lazy="loaded" />
            </div>
            <div data-v-33c420b2="" class="swiper-slide">
              <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-4.50ed3e12.png" src="/static/image/slide-4.50ed3e12.png" lazy="loaded" />
            </div>

            <div data-v-33c420b2="" class="swiper-slide">
              <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-6.8a224934.png" src="/static/image/slide-6.8a224934.png" lazy="loaded" />
            </div>
            <div data-v-33c420b2="" class="swiper-slide">
              <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-7.5420a6e8.png" src="/static/image/slide-7.5420a6e8.png" lazy="loaded" />
            </div>
          </div>
          <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
        <!-- <div
          data-v-33c420b2=""
          class="swiper-button-prev swiper-button swiper-button-prev-wolf swiper-button-wolf swiper-button-disabled"
          tabindex="0"
          role="button"
          aria-label="Previous slide"
          aria-disabled="true"
          data-src="/static/image/wolf-btn-left.e3bc065b.png"
          lazy="loaded"
          style="background-image: url('/static/image/wolf-btn-left.e3bc065b.png')"
        ></div>
        <div
          data-v-33c420b2=""
          class="swiper-button-next swiper-button swiper-button-next-wolf swiper-button-wolf"
          tabindex="0"
          role="button"
          aria-label="Next slide"
          aria-disabled="false"
          data-src="/static/image/wolf-btn-right.606e5356.png"
          lazy="loaded"
          style="background-image: url('/static/image/wolf-btn-right.606e5356.png')"
        ></div> -->
      </div>
      <div data-v-0b72266c="" class="paragraph">
        <p>2019年6月，卢卡 · 东契奇-奇迹男孩正式与{{$store.state.appInfo.title}}平台达成合作，成为{{$store.state.appInfo.title}}平台官方形象代言人之一。来自皇马的欧洲超新星，球风全面，传控意识佳，无惧大场面。在欧冠联赛上多次拿MVP，2018年西甲冠军MVP。{{$store.state.appInfo.title}}平台将与卢卡 · 东契奇强强联手，将共同打造全球体育界的新领袖，共创辉煌！</p>
      </div>
      <img data-v-0b72266c="" class="bottom-img" src="/static/image/bottom-img.47c4036e.png" lazy="loading" />
      <div data-v-0b72266c="" class="bottom-img"></div>
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
    var mySwiper = new Swiper('.swiper-container', {
      loop: true, //循环模式
      autoplay: true, //自动轮播  默认3s  delay: 3000,//自动切换时间；
      grabCursor: true, //箭头变小手
      slidesPerView: 1, //显示几个
      observer: true, // observer observeParents 加上后 更新也能显示
      observeParents: true, //observer observeParents 加上后 更新也能显示
      //左右箭头
      navigation: {
        nextEl: '.wolf-swiper-button-next',
        prevEl: '.wolf-swiper-button-prev',
      },
    });
  },
  updated() {
    let that = this;
  },
};
</script>

<style lang="scss" scoped>
@import '../../static/css/sponsorship-fort.eb808bb6.css';
@import '../../static/css/sponsorship-allen_sponsorship-fort.a8cb1353.css';
</style>
