<template>
  <div data-v-f531b812="" class="app app-ti_green">
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
    <div data-v-1dc20f37="" data-v-f531b812="" class="allen-iverson-view">
      <img data-v-1dc20f37="" src="/static/image/bg-one.16c076dc.png" class="topimg" />
      <div data-v-1dc20f37="" class="top">
        <div data-v-1dc20f37="" class="video-title-view"><img data-v-1dc20f37="" src="/static/image/video-title.02a38747.png" alt="" style="width: 100%" /></div>
        
      </div>
      <div data-v-1dc20f37="" class="bottom">
        <div data-v-1dc20f37="" class="paragraph-info">
          <img data-v-1dc20f37="" src="/static/image/bg-tow.cace17cc.png" alt="" class="paragraph-info-pic" />
          <div data-v-1dc20f37="" class="paragraph" style="display: none">
            <h3 style="color: #fee418">个人简介</h3>
            <p>2007年NBA选秀，尼克·杨在第一轮第16顺位被华盛顿奇才队选中。</p>
            <p>2007-08赛季，尼克·杨代表奇才队出战75场常规赛比赛，场均得到7.5分、1.5个篮板。</p>
            <p>2008-09赛季，由于奇才后场大将吉尔伯特·阿里纳斯的报销，尼克·杨获得了更多的机会，出战82场首发5次，场均得到10.9分。</p>
            <p>2009-10赛季，奇才后场补充了兰迪·弗耶、迈克·米勒等优秀球员，抢占了尼克·杨的出场时间，尼克·杨出战74场比赛，场均得到8.6分</p>
          </div>
        </div>
        <div data-v-33c420b2="" data-v-1dc20f37="" class="swiper-view" style="width:100%;">
          <div data-v-33c420b2="" class="swiper-container swiper swiper-container-coverflow swiper-container-3d swiper-container-initialized swiper-container-horizontal swiper-container-ios">
            <div class="swiper-wrapper" style="transition-duration: 0ms; transform: translate3d(0.461px, 0px, 0px); perspective-origin: 131.039px 50%">
              <div data-v-33c420b2="" class="swiper-slide swiper-slide-active" style="transition-duration: 0ms; transform: translate3d(0px, 0px, 0px) rotateX(0deg) rotateY(0deg); z-index: 1">
                <img data-v-33c420b2="" alt="0" data-src="/static/image/slide-1.20447b9d.png" src="/static/image/slide-1.20447b9d.png" lazy="loaded" />
                <div class="swiper-slide-shadow-left" style="opacity: 0; transition-duration: 0ms"></div>
                <div class="swiper-slide-shadow-right" style="opacity: 0; transition-duration: 0ms"></div>
              </div>
              <div data-v-33c420b2="" class="swiper-slide swiper-slide-next" style="transition-duration: 0ms; transform: translate3d(-19.994px, 0px, -319.905px) rotateX(0deg) rotateY(0deg); z-index: -1">
                <img data-v-33c420b2="" alt="1" data-src="/static/image/slide-2.f937c5ad.png" src="/static/image/slide-2.f937c5ad.png" lazy="loaded" />
                <div class="swiper-slide-shadow-left" style="opacity: 0; transition-duration: 0ms"></div>
                <div class="swiper-slide-shadow-right" style="opacity: 1.9994; transition-duration: 0ms"></div>
              </div>
              <div data-v-33c420b2="" class="swiper-slide" style="transition-duration: 0ms; transform: translate3d(-39.9881px, 0px, -639.81px) rotateX(0deg) rotateY(0deg); z-index: -3">
                <img data-v-33c420b2="" alt="2" data-src="/static/image/slide-3.cf5139ce.png" src="/static/image/slide-3.cf5139ce.png" lazy="loaded" />
                <div class="swiper-slide-shadow-left" style="opacity: 0; transition-duration: 0ms"></div>
                <div class="swiper-slide-shadow-right" style="opacity: 3.99881; transition-duration: 0ms"></div>
              </div>
         
            </div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
          </div>
          <div data-v-33c420b2="" class="swiper-button-prev swiper-button swiper-button-disabled" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="true" data-src="/static/image/btn-left.44c513fd.png" lazy="loaded" style="background-image: url('/static/image/btn-left.44c513fd.png')"></div>
          <div data-v-33c420b2="" class="swiper-button-next swiper-button" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false" data-src="/static/image/btn-right.3593c9da.png" lazy="loaded" style="background-image: url('/static/image/btn-right.3593c9da.png')"></div>
        </div>
        
        <div data-v-1dc20f37="" class="bottom-img"></div>
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
      effect: 'coverflow',
      autoplay: true,
      loop: true,
      grabCursor: true, //箭头变小手
      slidesPerView: 1,
      spaceBetween: 0, //样式根据具体情况调，spaceBetween必须有且单位是px
      centeredSlides: true, // 设置为true时,带有active-slide类名的图片会居中
      centeredSlidesBounds: true,

      loopFillGroupWithBlank: true,
      observer: true,
      observeParents: true,
      pagination: {
        // 分页器
        el: '.swiper-pagination',
      },
    });
  },
  updated() {
    let that = this;
  },
};
</script>

<style lang="scss" scoped>
@import '../../static/css/sponsorship-allen.d472c7de.css';
@import '../../static/css/chunk-vendors.a58c2457.css';
@import '../../static/css/chunk-3de6e5e7.b3aa2600.css';
</style>
