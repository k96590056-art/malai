<template>
  <div style="background-color: #f1f1f1; min-height: 100vh" v-if="vipLis.length > 0">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="VIP特权" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>

    <img src="/static/image/xiangqing@2x.8b5b315d4cf3a4593fab14da84b35192.jpg" style="width: 100%" alt="" />
    <div class="vipType">
      <div class="vipLis" v-for="(item, index) in 10" :key="index" @click="changvipTab(item)">
        <img :class="item == vipTab ? 'imgsd' : ''" :src="`/static/image/vip-${item}.png`" alt="" />
        VIP{{ item }}
      </div>
    </div>
    <div class="vipshow">
      <div class="bisx">
        <img :src="`/static/image/vip-${vipTab}_bg.png`" alt="" />
        <div class="ishw">{{ $store.state.userInfo.vip * 1 >= vipTab * 1 ? '已达标' : '未达标' }}</div>
        <div class="vip">VIP{{ vipTab }}</div>
        <div class="leiji">
          <span>累计存款</span><span class="megs">{{ vipLis[vipTab - 1]['recharge'] }} </span>
        </div>
        <div class="liushui">
          <span>流水要求</span><span class="megs">{{ vipLis[vipTab - 1]['flow'] }}</span>
        </div>
      </div>
    </div>
    <div class="fanshiw">
      <img data-v-f2b5b3f8="" src="/static/image/xiangqing.af99e65ed557473a7e18a21288144c2d.png" alt="" />
      VIP最高返水比例
    </div>
    <!-- 返回列表 -->
    <div data-v-f2b5b3f8="" class="vipDetailStyle__table__2KDLc guize_con">
      <div data-v-f2b5b3f8="" class="month__table__vcSJ7">
        <ul data-v-f2b5b3f8="">
          <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">VIP等级</span></li>
          <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
            <span data-v-f2b5b3f8="">VIP{{ item }}</span>
          </li>
        </ul>
        <div data-v-f2b5b3f8="" class="month__tableContent__q7JRn">
          <ul data-v-f2b5b3f8="">
            <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">真人(%)</span></li>
            
            <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
              <span data-v-f2b5b3f8="">{{ vipLis[index].realperson }}</span>
            </li>
          </ul>
          <ul data-v-f2b5b3f8="">
            <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">体育(%)</span></li>
            <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
              <span data-v-f2b5b3f8="">{{ vipLis[index].sport }}</span>
            </li>
          </ul>
          <ul data-v-f2b5b3f8="">
            <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">电竞(%)</span></li>
            <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
              <span data-v-f2b5b3f8="">{{ vipLis[index].e_sport }}</span>
            </li>
          </ul>
          <ul data-v-f2b5b3f8="">
            <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">棋牌(%)</span></li>
            <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
              <span data-v-f2b5b3f8="">{{ vipLis[index].joker }}</span>
            </li>
          </ul>
          <ul data-v-f2b5b3f8="">
            <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">电子(%)</span></li>
            <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
              <span data-v-f2b5b3f8="">{{ vipLis[index].electron }}</span>
            </li>
          </ul>
          <ul data-v-f2b5b3f8="">
            <li data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">彩票(%)</span></li>
            <li data-v-f2b5b3f8="" v-for="(item, index) in 10" :key="index">
              <span data-v-f2b5b3f8="">{{ vipLis[index].lottery }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="vipDetailStyle__vipDetail__2q9TP" style="">
      <div data-v-f2b5b3f8="" class="vipDetailStyle__vipInfo__3IZIT">
        <div data-v-f2b5b3f8="" class="vipDetailStyle__rule__2s4Rz">
          <h2 data-v-f2b5b3f8="" style="margin: 0"><span data-v-f2b5b3f8="">活动规则</span></h2>
          <h4 data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">1</span>晋升标准</h4>
          <p data-v-f2b5b3f8="">会员的累计存款以及累计流水达到相应级别的要求，即可在次日24点前晋级相应VIP等级。</p>
          <h4 data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">2</span>晋升顺序</h4>
          <p data-v-f2b5b3f8="">VIP等级达到相应的要求可每天晋升一级，但VIP等级不可越级晋升。</p>
          <h4 data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">3</span>保级要求</h4>
          <p data-v-f2b5b3f8="">会员在达到某VIP等级后，90天内投注需要完成保级流水要求。如果在此期间完成晋升，保级要求重新按照当前等级计算。</p>
          <h4 data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">4</span>降级标准</h4>
          <p data-v-f2b5b3f8="">如果会员在一个季度（90天计算）内没有完成相应的保级要求流水，系统会自动降级一个等级，相应的返水及其它优惠也会随之调整至降级后的等级。</p>
          <h4 data-v-f2b5b3f8=""><span data-v-f2b5b3f8="">8</span>VIP返水</h4>
          <p data-v-f2b5b3f8="">VIP返水优惠发放金额根据会员当天北京时间00:00—23:59之间的有效投注进行计算，当天所有的投注额返水将在注单结算当天结束后24小时内发放到福利中心，进入个人中心点击福利中心进行手动领取。（VIP返水1倍流水即可提款）</p>
          <div data-v-f2b5b3f8="" class="vipDetailStyle__note__2kqnK">欧宝娱乐保留对活动的修改，停止及最终解释权</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'vip',
  data() {
    return {
      url: null,
      vipLis: [],
      vipTab: 1,
      bfNum: 0,
    };
  },
  created() {
    let that = this;
    if (that.$store.state.token) {
      that.vipTab = that.$store.state.userInfo.vip;
    }
    that.uservip();
  },
  methods: {
    swiperChang(e) {
      console.log(e);
    },
    changvipTab(val) {
      this.vipTab = val;
    },
    uservip() {
      let that = this;
      that.$parent.showLoading();

      that.$apiFun
        .post('/api/uservip', {})
        .then(res => {
          if (res.code == 200) {
            that.vipLis = res.data;
            that.getbfNum();
            var mySwiper = new Swiper('.swiper-container', {
              loop: false, //循环模式
              autoplay: false, //自动轮播  默认3s  delay: 3000,//自动切换时间；
              grabCursor: true, //箭头变小手
              slidesPerView: 1, //显示几个
              initialSlide: that.vipTab - 1, //默认显示第几个元素
              observer: true, // observer observeParents 加上后 更新也能显示
              observeParents: true, //observer observeParents 加上后 更新也能显示
              on: {
                slideChangeTransitionEnd: function () {
                  that.vipTab = this.realIndex + 1;
                },
              },
            });
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    getbfNum() {
      let that = this;
      let num = 0; //当前vip充值钱
      let vip = that.$store.state.userInfo.vip * 1; //当前vip等级
      that.vipLis.forEach((el, index) => {
        console.log();
        if (index == vip) {
          num = el.recharge * 1;
        }
      });
      let userMey = that.$store.state.userInfo.paysum * 1;
      let bfNum = userMey == 0 || num == 0 ? 0 : Math.round((userMey / num) * 100);
      that.bfNum = bfNum > 100 ? 100 : bfNum;
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
// @import '../../static/css/chunk-37e7907a.c21ed25e.css';
.vipType {
  display: flex;
  flex-wrap: wrap;
  box-sizing: border-box;
  padding: 4px;
  justify-content: space-between;
  .vipLis {
    width: 19%;
    border-radius: 10px;
    background: #fff;
    margin-top: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: #9aa7b5;
    img {
      width: 28px;
      filter: grayscale(100%);
    }
    .imgsd {
      filter: grayscale(0%) !important;
    }
  }
}

.vipshow {
  background: #fff;
  box-sizing: border-box;
  padding: 20px 30px;
  margin-top: 12px;
  .bisx {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    img {
      width: 100%;
    }
    .ishw {
      position: absolute;
      top: 0;
      left: 0;
      color: #3c4b69;
      background-color: #e0edf9;
      padding: 4px 8px;
      border-radius: 8px 0px 8px 0px;
    }
    .vip {
      position: absolute;
      top: 35px;
      left: 40px;
      color: #fff;
      font-size: 22px;
      font-weight: 700;
      font-style: italic;
    }
    .leiji {
      position: absolute;
      top: 75px;
      left: 40px;
      color: #fff;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .liushui {
      position: absolute;
      top: 98px;
      left: 40px;
      color: #fff;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .megs {
      margin-left: 50px;
    }
  }
}

.fanshiw {
  height: 40px;
  line-height: 40px;
  text-align: center;
  color: rgba(0, 0, 0, 0.85);
  background: -webkit-gradient(linear, left top, right top, from(rgba(7, 226, 255, 0)), to(rgba(3, 193, 253, 0.09)));
  background: -webkit-linear-gradient(left, rgba(7, 226, 255, 0), rgba(3, 193, 253, 0.09));
  background: linear-gradient(90deg, rgba(7, 226, 255, 0), rgba(3, 193, 253, 0.09));
  border-bottom: 1px solid hsla(0, 0%, 86.7%, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
}

.vipStyle__headerTab__38yE-[data-v-f2b5b3f8] {
  height: 0.8rem;
}
.vipStyle__headerTab__38yE- > div[data-v-f2b5b3f8] {
  background: #0d1a26;
  position: fixed;
  top: 0;
  width: 100%;
  height: 0.8rem;
  z-index: 11;
}
.vipStyle__headerTab__38yE- > div > img[data-v-f2b5b3f8] {
  position: absolute;
  left: 0.2rem;
  top: 50%;
  bottom: 50%;
  margin-top: -0.13rem;
  width: 0.26rem;
  height: 0.26rem;
}
.vipStyle__headerTab__38yE- > div > div[data-v-f2b5b3f8] {
  width: 3.1rem;
  height: 0.8rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  margin: 0 auto;
}
.vipStyle__headerTab__38yE- > div > div > span[data-v-f2b5b3f8] {
  display: inline-block;
  line-height: 0.8rem;
  font-size: 0.32rem;
  color: hsla(0, 0%, 100%, 0.5);
}
.vipStyle__headerTab__38yE- > div > div > span.vipStyle__active__1mRjp[data-v-f2b5b3f8] {
  position: relative;
  color: #fff;
  font-size: 0.32rem;
  text-align: center;
}
.vipStyle__headerTab__38yE- > div > div > span.vipStyle__active__1mRjp[data-v-f2b5b3f8]:after {
  content: '';
  display: inline-block;
  margin: 0 auto;
  position: absolute;
  left: 0;
  right: 0;
  bottom: 1.12px;
  bottom: 0.07rem;
  width: 10.24px;
  width: 0.64rem;
  height: 2px;
  background: #fff;
}
.vipStyle__headerTab__38yE- > div > div h3[data-v-f2b5b3f8] {
  display: block;
  line-height: 0.8rem;
  color: #fff;
  font-size: 0.32rem;
  position: absolute;
  top: 0;
  left: 50%;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
}
.vipStyle__ruleHead__1Fx2G > div[data-v-f2b5b3f8] {
  background: #fff;
}
.vipStyle__ruleHead__1Fx2G > div > div > span[data-v-f2b5b3f8] {
  color: #333;
}
.vipDetail__head__DwMc6[data-v-f2b5b3f8] {
  height: 0.8rem;
}
.vipDetail__head__DwMc6 > div[data-v-f2b5b3f8] {
  position: fixed;
  top: 0;
  width: 100%;
  height: 0.8rem;
  z-index: 3333;
}
.vipDetail__head__DwMc6 > div img[data-v-f2b5b3f8] {
  position: absolute;
  left: 0.2rem;
  top: 50%;
  bottom: 50%;
  margin-top: -0.13rem;
  width: 0.26rem;
  height: 0.26rem;
}
.vipDetail__head__DwMc6 > div div[data-v-f2b5b3f8] {
  width: 7rem;
  height: 0.8rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: end;
  -webkit-justify-content: flex-end;
  -ms-flex-pack: end;
  justify-content: flex-end;
}
.vipDetail__head__DwMc6 > div div span[data-v-f2b5b3f8] {
  display: inline-block;
  line-height: 0.8rem;
  font-size: 0.36rem;
  color: #333;
  margin-left: 1.6rem;
}
.vipDetail__head__DwMc6 .vipDetail__activeClassName__2xnaj[data-v-f2b5b3f8] {
  position: relative;
  color: #333;
  font-size: 0.36rem;
  text-align: center;
}
.vipDetail__head__DwMc6 .vipDetail__activeClassName__2xnaj[data-v-f2b5b3f8]:after {
  content: '';
  display: inline-block;
  margin: 0 auto;
  position: absolute;
  left: 0;
  right: 0;
  bottom: 1.12px;
  bottom: 0.07rem;
  width: 10.24px;
  width: 0.64rem;
  height: 2px;
  background: #fff;
}
.vipDetail__head__DwMc6 > div[data-v-f2b5b3f8] {
  -webkit-justify-content: space-around;
  -ms-flex-pack: distribute;
  justify-content: space-around;
}
.vipDetail__head__DwMc6 > div[data-v-f2b5b3f8],
.vipDetail__viphead__1r99W[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
}
.vipDetail__viphead__1r99W[data-v-f2b5b3f8] {
  position: absolute;
  -webkit-box-orient: horizontal;
  -webkit-box-direction: normal;
  -webkit-flex-direction: row;
  -ms-flex-direction: row;
  flex-direction: row;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  height: 0.88rem;
  width: 100%;
  padding: 0 0.32rem;
  z-index: 10;
}
.vipDetail__viphead__1r99W .vipDetail__goback__Bt-fm[data-v-f2b5b3f8] {
  width: 0.48rem;
  height: 0.48rem;
}
.vipDetail__viphead__1r99W .vipDetail__titleText1__2UDwA[data-v-f2b5b3f8] {
  font-size: 0.36rem;
  color: #333;
}
.vipDetail__viphead__1r99W .vipDetail__titleText2__3SUun[data-v-f2b5b3f8] {
  color: #263440;
}
.vipDetail__active__ORTq9[data-v-f2b5b3f8] {
  position: relative;
  color: #fff;
  font-weight: 700;
}
.vipDetail__active__ORTq9[data-v-f2b5b3f8]:after {
  content: '';
  display: block;
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 1px;
  background: #fff;
}
.vipDetail__banner__3_aPW[data-v-f2b5b3f8] {
  width: 100%;
  height: 4.56rem;
  background-size: cover;
}
.vipDetail__list_top__3-YHY[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-justify-content: space-around;
  -ms-flex-pack: distribute;
  justify-content: space-around;
  overflow: hidden;
  padding: 0.2rem 0 0;
}
.vipDetail__list_top__3-YHY li[data-v-f2b5b3f8]:first-of-type,
.vipDetail__list_top__3-YHY li[data-v-f2b5b3f8]:nth-child(6) {
  border-left: none;
}
.vipDetail__list_top__3-YHY li[data-v-f2b5b3f8] {
  width: 1.36rem;
  height: 0.7rem;
  text-align: center;
  line-height: 0.7rem;
  background: #fff;
  border-radius: 0.3rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  margin-bottom: 0.2rem;
}
.vipDetail__list_top__3-YHY li img[data-v-f2b5b3f8] {
  width: 0.7rem;
  display: inline-block;
  border: none;
}
.vipDetail__list_top__3-YHY li span[data-v-f2b5b3f8] {
  font-size: 0.2rem;
  color: #9aa7b5;
}
.vipDetail__list_top__3-YHY li[data-v-f2b5b3f8]:nth-child(10):after {
  border-right: none;
}
.vipDetail__list_top__3-YHY li div[data-v-f2b5b3f8]:first-child {
  padding: 0.2rem 0;
}
.vipDetail__list_top__3-YHY li div:first-child span[data-v-f2b5b3f8] {
  width: 1.22rem;
  display: block;
  height: 1.28rem;
  cursor: pointer;
}
.vipDetail__list_top__3-YHY li div[data-v-f2b5b3f8]:last-child {
  padding-bottom: 0.2rem;
  width: 0.5rem;
  text-align: center;
  color: #666;
  font-size: 0.24rem;
}
.vipDetail__contentWrapper__2WBjZ[data-v-f2b5b3f8] {
  background: #fff;
  overflow: hidden;
  padding: 0.2rem 0;
}
.vipDetail__vipCardContent__1nLu0[data-v-f2b5b3f8] {
  margin: 0.65rem 0 0 0.72rem;
}
.vipDetail__vipCardContent__1nLu0 h2[data-v-f2b5b3f8] {
  line-height: 0.48rem;
  font-size: 0.48rem;
  color: #fff;
  font-style: italic;
}
.vipDetail__vipCardContent__1nLu0 p[data-v-f2b5b3f8] {
  margin-top: 0.5rem;
  line-height: 0.2rem;
  font-size: 0.2rem;
  color: #fff;
}
.vipDetail__vipCardContent__1nLu0 p .vipDetail__left140__2Og6f[data-v-f2b5b3f8] {
  margin-left: 1.64rem;
}
.vipDetail__vipCardContent__1nLu0 p .vipDetail__left70__F34zv[data-v-f2b5b3f8] {
  margin-left: 0.54rem;
}
.vipDetail__vipCardContent__1nLu0 .vipDetail__margin__1TgMq[data-v-f2b5b3f8],
.vipDetail__vipCardContent__1nLu0 p[data-v-f2b5b3f8]:last-child {
  margin-top: 0.25rem;
}
.vipDetail__vipCardContent__1nLu0 > div[data-v-f2b5b3f8] {
  margin-bottom: 0.2rem;
  font-size: 0.24rem;
  color: #583f2d;
}
.vipDetail__vipCardContent__1nLu0 > div .vipDetail__left140__2Og6f[data-v-f2b5b3f8] {
  margin-left: 2.1rem;
}
.vipDetail__vipCardContent__1nLu0 > div .vipDetail__left70__F34zv[data-v-f2b5b3f8] {
  margin-left: 1rem;
}
.vipDetail__selectVenue__cYKIU[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  border-radius: 0.12rem;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  background: rgba(107, 183, 238, 0.04);
  padding: 0.34rem 0.5rem;
  font-size: 0.24rem;
  margin-top: 0.12rem;
}
.vipDetail__selectVenue__cYKIU uni-input[data-v-f2b5b3f8] {
  border: 0.01rem solid #ccc;
  width: 1.88rem;
  display: inline-block;
  height: 0.48rem;
  line-height: 0.48rem;
  font-size: 0.2rem;
  margin-right: 0.9rem;
  text-align: center;
  border-radius: 0.06rem;
}
.vipDetail__selectVenue__cYKIU .vipDetail__seleced__1NSJR[data-v-f2b5b3f8] {
  height: 0.48rem;
  line-height: 0.48rem;
  color: rgba(0, 0, 0, 0.85);
  font-size: 0.24rem;
  font-weight: 400;
}
.vipDetail__selectVenue__cYKIU .vipDetail__receive__3G4hq[data-v-f2b5b3f8] {
  width: 1.34rem;
  line-height: 0.48rem;
  height: 0.48rem;
  display: inline-block;
  background-color: #ede9e7;
  color: #fff;
  font-size: 0.2rem;
  text-align: center;
  border-radius: 0.04rem;
}
.vipDetail__selectVenue__cYKIU uni-button[data-v-f2b5b3f8] {
  width: 1.8rem;
  height: 0.54rem;
  border: none;
  outline: none;
  border-radius: 0.4rem;
  cursor: pointer;
  font-size: 0.24rem;
  color: #4993de;
  -webkit-box-shadow: 0.02rem 0.05rem 0.1rem 0 #eee;
  box-shadow: 0.02rem 0.05rem 0.1rem 0 #eee;
}
.vipDetail__lishsUl__1UJf3[data-v-f2b5b3f8] {
  background: #fff;
  border-radius: 0.16rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__vip__2iQQc[data-v-f2b5b3f8] {
  position: relative;
  width: 6.3rem;
  height: 3.1rem;
  margin: 0.12rem auto 0;
  border-radius: 0.16rem;
  -webkit-box-shadow: 0.01rem 0.06rem 0.12rem #ccc;
  box-shadow: 0.01rem 0.06rem 0.12rem #ccc;
  overflow: hidden;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__vip__2iQQc .vipDetail__curr__1Q5tt[data-v-f2b5b3f8] {
  position: absolute;
  top: 0;
  left: 0;
  padding: 0.09rem;
  text-align: center;
  background-color: #ffe9b7;
  border-radius: 0.16rem 0 0.16rem 0;
  color: #3c4b69;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__vip__2iQQc > img[data-v-f2b5b3f8] {
  height: 0.44rem;
  width: 0.72rem;
  border: none;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz[data-v-f2b5b3f8] {
  padding: 0 0.4rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku > div[data-v-f2b5b3f8] {
  border-radius: 0.12rem;
  height: 1.32rem;
  width: 3.28rem;
  margin-top: 0.32rem;
  padding-top: 0.24rem;
  padding-left: 0.38rem;
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  font-size: 0.24rem;
  text-align: left;
  background: rgba(107, 183, 238, 0.04);
  -webkit-box-sizing: content-box;
  box-sizing: initial;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku > div .vipDetail__monthGift__38GuX[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding-right: 0.4rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku > div > span[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: rgba(0, 0, 0, 0.85);
  line-height: 0.33rem;
  width: 100%;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku > div > span > span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku > div div[data-v-f2b5b3f8] {
  font-size: 0.2rem;
  margin-top: 0.16rem;
  color: #9aa7b5;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__upgrade_money__1f2Ku > div[data-v-f2b5b3f8]:first-child {
  margin-right: 0.1rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__birthday__1f75g[data-v-f2b5b3f8] {
  margin-top: 0.12rem;
  width: 6.7rem;
  height: 0.98rem;
  padding: 0 0.48rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  background: rgba(107, 183, 238, 0.04);
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__birthday__1f75g span[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  font-size: 0.28rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__birthday__1f75g .vipDetail__birthleft__1Ymxg[data-v-f2b5b3f8] {
  display: inline-block;
  line-height: 0.98rem;
  font-weight: 700;
  font-size: 0.24rem;
  color: rgba(0, 0, 0, 0.85);
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__birthday__1f75g .vipDetail__birthleft__1Ymxg span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__birthday__1f75g .vipDetail__birthRight__jBCnk[data-v-f2b5b3f8] {
  margin-top: 0.26rem;
  display: inline-block;
  width: 1.34rem;
  height: 0.48rem;
  line-height: 0.48rem;
  text-align: center;
  border-radius: 0.04rem;
  background-color: #ede9e7;
  color: #fff;
  font-size: 0.2rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5[data-v-f2b5b3f8] {
  margin-top: 0.12rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div[data-v-f2b5b3f8] {
  height: 3.14rem;
  padding-left: 0.34rem;
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-align: start;
  -webkit-align-items: flex-start;
  -ms-flex-align: start;
  align-items: flex-start;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  background: rgba(107, 183, 238, 0.04);
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div[data-v-f2b5b3f8]:first-child {
  margin-right: 0.14rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div > div > span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div h3[data-v-f2b5b3f8] {
  white-space: nowrap;
  display: inline-block;
  font-size: 0.24rem;
  line-height: 0.28rem;
  color: rgba(0, 0, 0, 0.85);
  font-weight: 400;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div p[data-v-f2b5b3f8] {
  margin-top: 0.16rem;
  color: #9aa7b5;
  font-size: 0.2rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div > div[data-v-f2b5b3f8] {
  width: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding-right: 0.4rem;
  margin-bottom: 0.24rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div > div[data-v-f2b5b3f8]:first-child {
  margin-top: 0.16rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__offerDescription__2IzN3 > div > div span[data-v-f2b5b3f8] {
  text-align: right;
  width: 1.2rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__apply_money__2Fp3I[data-v-f2b5b3f8] {
  height: 1.64rem;
  border-radius: 0.05rem;
  padding-left: 0.5rem;
  padding-top: 0.32rem;
  color: #98a5b3;
  font-size: 0.24rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__apply_money__2Fp3I div p[data-v-f2b5b3f8] {
  height: 0.4rem;
  line-height: 0.4rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__apply_money__2Fp3I div p[data-v-f2b5b3f8]:last-child {
  margin-top: 0.2rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__apply_money__2Fp3I div p span[data-v-f2b5b3f8] {
  font-weight: 700;
  display: inline-block;
  color: #ede9e7;
  font-size: 0.28rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__trans__25Le5 .vipDetail__apply_money__2Fp3I .vipDetail__second__3HTpP[data-v-f2b5b3f8] {
  margin-left: 0.9rem;
  font-size: 0.24rem;
  color: #98a5b3;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__gifts__3byrE[data-v-f2b5b3f8] {
  background: rgba(107, 183, 238, 0.04);
  padding: 0.32rem 0;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__gifts__3byrE h3[data-v-f2b5b3f8] {
  padding-left: 0.48rem;
  margin-bottom: 0.2rem;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__gifts__3byrE .vipDetail__giftsLeft__2sEhT[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  padding: 0 0.48rem;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__gifts__3byrE .vipDetail__giftsLeft__2sEhT p[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  line-height: 0.24rem;
  color: #98a5b3;
  width: 3.4rem;
  word-wrap: auto;
}
.vipDetail__lishsUl__1UJf3 .vipDetail__right__1BZgz .vipDetail__vip1__25_sT .vipDetail__gifts__3byrE .vipDetail__giftsRight__1NATH[data-v-f2b5b3f8] {
  margin-top: 0.02rem;
  margin-left: 1rem;
  width: 1.34rem;
  height: 0.48rem;
  line-height: 0.48rem;
  text-align: center;
  background-color: #ede9e7;
  color: #fff;
  font-size: 0.2rem;
  border-radius: 0.04rem;
}
.vipDetail__multiLine__1YGWc[data-v-f2b5b3f8],
.vipDetail__multiLine__1YGWc div[data-v-f2b5b3f8] {
  line-height: 0.3rem !important;
  padding-top: 0;
}
.vipDetail__multiLine__1YGWc div[data-v-f2b5b3f8] {
  background: none !important;
  border-right: none !important;
}
.vipDetail__tab_s__3K9Go[data-v-f2b5b3f8] {
  font-size: 0.18rem;
  background-color: #fff;
  border-radius: 0.1rem;
}
.vipDetail__tab_s__3K9Go .vipDetail__multiLineTitle__3dwsW[data-v-f2b5b3f8] {
  width: 100%;
  height: 0.98rem;
  background-size: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  background: -webkit-gradient(linear, left top, right top, from(rgba(7, 226, 255, 0)), to(rgba(3, 193, 253, 0.09)));
  background: -webkit-linear-gradient(left, rgba(7, 226, 255, 0), rgba(3, 193, 253, 0.09));
  background: linear-gradient(90deg, rgba(7, 226, 255, 0), rgba(3, 193, 253, 0.09));
}
.vipDetail__tab_s__3K9Go .vipDetail__multiLineTitle__3dwsW img[data-v-f2b5b3f8] {
  height: 0.34rem;
  width: 0.4rem;
  margin-right: 0.16rem;
}
.vipDetail__tab_s__3K9Go .vipDetail__multiLineTitle__3dwsW span[data-v-f2b5b3f8] {
  font-size: 0.28rem;
  color: rgba(0, 0, 0, 0.85);
  line-height: 0.28rem;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8[data-v-f2b5b3f8] {
  margin: 0 auto;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  width: 100%;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 ul[data-v-f2b5b3f8] {
  overflow: hidden;
  width: 1.25rem;
  border-right: 1px solid #f4f2f5;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 ul li[data-v-f2b5b3f8] {
  font-size: 0.2rem;
  height: 0.9rem;
  line-height: 0.9rem;
  text-align: center;
  border-top: 1px solid #f4f2f5;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 ul[data-v-f2b5b3f8]:last-child {
  border-right: none;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 .vipDetail__uldivlevel__2nN4q li[data-v-f2b5b3f8] {
  color: #c1ab77;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 li[data-v-f2b5b3f8]:last-child {
  border-right: none;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 li[data-v-f2b5b3f8]:first-child {
  width: 100%;
}
.vipDetail__tab_s__3K9Go .vipDetail__uldiv__2UIY8 li:first-child div[data-v-f2b5b3f8] {
  color: #333;
  line-height: 0.64rem;
  height: 0.64rem;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u[data-v-f2b5b3f8] {
  margin: 0 auto;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  width: 100%;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u ul[data-v-f2b5b3f8] {
  width: 2.5rem;
  text-align: center;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u ul li[data-v-f2b5b3f8] {
  height: 0.9rem;
  line-height: 0.9rem;
  font-size: 0.2rem;
  text-align: center;
  border-top: 1px solid #f4f2f5;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u .vipDetail__ulwidthdivlevel__3pwe9 li[data-v-f2b5b3f8] {
  color: #c1ab77;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u li[data-v-f2b5b3f8]:last-child {
  border-right: none;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u li[data-v-f2b5b3f8]:first-child {
  width: 100%;
}
.vipDetail__tab_s__3K9Go .vipDetail__ulwithdiv__1qN5u li:first-child div[data-v-f2b5b3f8] {
  color: #333;
  line-height: 0.64rem;
  height: 0.64rem;
}
.vipDetail__rule_number__3UT8X[data-v-f2b5b3f8] {
  width: 0.32rem;
  height: 0.32rem;
  border-radius: 50%;
}
.vipDetail__xian_tu__3NgQr[data-v-f2b5b3f8] {
  margin-top: 0.32rem;
  background-color: #fff;
  padding-bottom: 0.32rem;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_title__1WG9H[data-v-f2b5b3f8] {
  height: 0.9rem;
  border-bottom: 1px solid #f4f2f5;
  text-align: center;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_title__1WG9H h3[data-v-f2b5b3f8] {
  display: inline-block;
  height: 100%;
  line-height: 0.9rem;
  font-size: 0.32rem;
  color: #ede9e7;
  position: relative;
  font-weight: 400;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_title__1WG9H h3[data-v-f2b5b3f8]:after {
  content: '';
  width: 1.28px;
  width: 0.08rem;
  height: 4.48px;
  height: 0.28rem;
  background: #ede9e7;
  position: absolute;
  left: -3.84px;
  left: -0.24rem;
  top: 50%;
  margin-top: -0.11rem;
  -webkit-transform: skewY(-20deg);
  transform: skewY(-20deg);
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_content__PpqHg[data-v-f2b5b3f8] {
  padding: 0.24rem 0.32rem 0;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_content__PpqHg p[data-v-f2b5b3f8] {
  line-height: 0.4rem;
  margin-bottom: 0.2rem;
  font-size: 0.24rem;
  color: #7e7e7e;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_content__PpqHg p h1[data-v-f2b5b3f8] {
  color: rgba(51, 51, 51, 0.8);
  font-size: 0.28rem;
  margin-bottom: 0.1rem;
  line-height: 0.32rem;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_content__PpqHg p h1 span[data-v-f2b5b3f8] {
  display: inline-block;
  border-radius: 50%;
  width: 0.3rem;
  height: 0.3rem;
  margin-right: 0.2rem;
  line-height: 0.3rem;
  text-align: center;
  font-size: 0.24rem;
  font-weight: 100;
  color: #fff;
  background: #ede9e7;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_content__PpqHg p > p[data-v-f2b5b3f8] {
  padding-left: 0.45rem;
  color: #98a5b3;
}
.vipDetail__xian_tu__3NgQr .vipDetail__dib__3JzoJ .vipDetail__rule_content__PpqHg p .vipDetail__remark__1xCLE[data-v-f2b5b3f8] {
  color: #356dfc;
  padding-left: 0.45rem;
}
.vipDetail__backtop__3bvjS[data-v-f2b5b3f8] {
  height: 0.8rem;
  width: 2.1rem;
  text-align: center;
  margin: 0.36rem auto 0;
}
.vipDetail__backtop__3bvjS span[data-v-f2b5b3f8] {
  display: block;
  line-height: 0.8rem;
  font-size: 0.28rem;
  color: #98a7b5;
}
.vipDetail__vip_back_btn__2xXpy[data-v-f2b5b3f8] {
  width: 100%;
  text-align: center;
}
.vipDetail__vip_back_arrow__2PFaZ[data-v-f2b5b3f8] {
  display: inline-block;
  width: 1.8rem;
  height: 1.5rem;
  background-position: 50%;
  background-repeat: no-repeat;
  background-size: cover;
  margin: 0 auto;
}
.vipDetail__tab_list__2UI1q[data-v-f2b5b3f8] {
  width: 100%;
  border-radius: 0.08rem;
  margin-top: 0.32rem;
  background: #fff;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-justify-content: space-around;
  -ms-flex-pack: distribute;
  justify-content: space-around;
}
.vipDetail__tab_list__2UI1q span[data-v-f2b5b3f8] {
  width: 1.78rem;
  height: 0.8rem;
  background: none;
  line-height: 0.8rem;
  text-align: center;
  font-size: 0.28rem;
  color: #697b8c;
  background-color: #fff;
}
.vipDetail__tab_list__2UI1q span[data-v-f2b5b3f8]:first-child {
  margin-right: 0.3rem;
}
.vipDetail__tab_list__2UI1q .vipDetail__aactive__1Fise[data-v-f2b5b3f8] {
  border-bottom: 2px solid #ede9e7;
  color: #ede9e7;
}
.vipDetail__cheeck__1Ye83[data-v-f2b5b3f8] {
  display: inline-block;
  width: 2.46rem;
  background: #fff;
  color: #333;
  font-size: 0.24rem;
  border-radius: 0.05rem;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  border: 1px solid #f4f2f5;
  text-indent: 0.2rem;
  padding: 0.12rem 0;
  position: relative;
}
.vipDetail__cheeck__1Ye83 i[data-v-f2b5b3f8] {
  position: absolute;
  border: 0.12rem solid transparent;
  border-top-color: #e1bf8b;
  top: 0.15rem;
  right: 0.1rem;
}
.vipDetail__foot__2grsE[data-v-f2b5b3f8] {
  height: 0.8rem;
  width: 2.1rem;
  margin: 0 auto;
  text-align: center;
  color: #98a7b5;
  border-radius: 0.47rem;
  -webkit-box-shadow: 0.02rem 0.04rem 0.18rem #f3f3f3;
  box-shadow: 0.02rem 0.04rem 0.18rem #f3f3f3;
}
.vipDetail__foot__2grsE span[data-v-f2b5b3f8] {
  text-align: center;
}
.vipDetail__foot__2grsE span img[data-v-f2b5b3f8] {
  height: 0.32rem;
  width: 0.32rem;
  margin-top: 0.02rem;
}
.vipDetail__foot__2grsE p[data-v-f2b5b3f8] {
  font-size: 0.28rem;
  height: 0.4rem;
  color: #98a7b5;
}
.vipDetail__detail__XbnPM[data-v-f2b5b3f8] {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  overflow-y: auto;
}
.vipDetail__rules__aXDXM[data-v-f2b5b3f8] {
  display: inline-block;
  margin-top: 0.16rem;
  padding: 0.32rem;
  font-size: 0.28rem;
  line-height: 0.44rem;
  text-align: justify;
  background: #fff;
  color: #263440;
}
.vipDetail__rules__aXDXM.vipDetail__noTop__2EO2T[data-v-f2b5b3f8] {
  margin-top: 0;
}
.vipDetail__rules__aXDXM div[data-v-f2b5b3f8],
.vipDetail__rules__aXDXM p[data-v-f2b5b3f8] {
  margin-bottom: 0.24rem;
}
.vipDetail__rules__aXDXM img[data-v-f2b5b3f8] {
  max-width: 100%;
  height: -webkit-fit-content;
  height: -moz-fit-content;
  height: fit-content;
}
.vipDetail__empty_container__18inz[data-v-f2b5b3f8] {
  text-align: center;
}
.vipDetail__empty_container__18inz .vipDetail__empty_icon__2IhnK[data-v-f2b5b3f8] {
  width: 3.6rem;
  height: 3.6rem;
  margin: 2rem auto 0.2rem;
}
.vipDetail__empty_container__18inz .vipDetail__empty_desc__2FyhX[data-v-f2b5b3f8] {
  font-size: 0.26rem;
  color: #a5a9b3;
}
.advance__advance_container__1ft87[data-v-f2b5b3f8] {
  width: 100%;
  padding-top: 0.32rem;
  margin-top: 0.2rem;
  background: #fff;
}
.advance__advance_container__1ft87 .advance__title__2lc6Y img[data-v-f2b5b3f8] {
  margin-left: 0.28rem;
  width: 0.06rem;
  height: 0.32rem;
}
.advance__advance_container__1ft87 .advance__title__2lc6Y span[data-v-f2b5b3f8] {
  line-height: 0.32rem;
  font-size: 0.38rem;
  height: 0.32rem;
  margin-left: 0.18rem;
  font-family: PingFangSC-Medium, sans-serif;
}
.advance__advance_container__1ft87 .advance__list__Nu5EE[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: nowrap;
  -ms-flex-wrap: nowrap;
  flex-wrap: nowrap;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  margin-top: 0.4rem;
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__common_item__2upOm[data-v-f2b5b3f8],
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__mid_item__12WEP[data-v-f2b5b3f8] {
  text-align: center;
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__common_item__2upOm div[data-v-f2b5b3f8]:last-child,
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__mid_item__12WEP div[data-v-f2b5b3f8]:last-child {
  margin-top: 0.7rem;
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__common_item__2upOm .advance__num__nFAw-[data-v-f2b5b3f8],
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__mid_item__12WEP .advance__num__nFAw-[data-v-f2b5b3f8] {
  margin-bottom: 0.2rem;
  font-family: DINPro-Medium;
  line-height: 0.48rem;
  font-size: 0.32rem;
  color: rgba(51, 51, 51, 0.85);
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__common_item__2upOm .advance__desc__1H0zJ[data-v-f2b5b3f8],
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__mid_item__12WEP .advance__desc__1H0zJ[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: rgba(65, 70, 85, 0.5);
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__common_item__2upOm[data-v-f2b5b3f8] {
  width: 2.4rem;
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__mid_item__12WEP[data-v-f2b5b3f8] {
  width: 2.6rem;
}
.advance__advance_container__1ft87 .advance__list__Nu5EE .advance__list_line__2BdMt[data-v-f2b5b3f8] {
  width: 0.02rem;
  height: 2rem;
  background: #efeff4;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri[data-v-f2b5b3f8] {
  padding: 1rem 0.6rem;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__bonus_area__xXguc[data-v-f2b5b3f8] {
  padding-bottom: 0.64rem;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__bonus_area__xXguc[data-v-f2b5b3f8],
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__gift_area__p_mCC[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__gift_area__p_mCC[data-v-f2b5b3f8] {
  padding-top: 0.6rem;
  border-top: 0.01rem solid #efeff4;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__gift_area__p_mCC .advance__gift_pic__O3lls[data-v-f2b5b3f8] {
  width: 3.32rem;
  height: 3.2rem;
  padding: 0.2rem;
  border-radius: 0.04rem;
  background: #f4f6f9;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__gift_area__p_mCC .advance__gift_pic__O3lls img[data-v-f2b5b3f8] {
  width: 100%;
  height: 100%;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__gift_area__p_mCC .advance__gift_name__XvnCm[data-v-f2b5b3f8] {
  width: 2.52rem;
  padding-bottom: 0.4rem;
  line-height: 1.4;
  font-size: 0.28rem;
  word-break: break-all;
  color: #666;
  text-align: center;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg[data-v-f2b5b3f8] {
  margin-top: 0.4rem;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80[data-v-f2b5b3f8] {
  margin-bottom: 0.2rem;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__percent_container__R1ksa[data-v-f2b5b3f8] {
  position: relative;
  margin-left: 1.5rem;
  height: 0.36rem;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__percent_container__R1ksa .advance__percent_num__3ofpb[data-v-f2b5b3f8] {
  position: absolute;
  bottom: 0;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  font-family: DIN !important;
  font-weight: 500;
  font-size: 0.28rem;
  color: #ede9e7;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__percent_wrap__3FCRi[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  margin: 0.16rem 0;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__percent_wrap__3FCRi p[data-v-f2b5b3f8] {
  width: 1.5rem;
  font-size: 0.28rem;
  color: #666;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__percent_wrap__3FCRi .advance__all_percent__rwMgr[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  position: relative;
  height: 0.24rem;
  border-radius: 0.12rem;
  background: hsla(0, 0%, 60%, 0.2);
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__percent_wrap__3FCRi .advance__all_percent__rwMgr .advance__current_percent__3REu8[data-v-f2b5b3f8] {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  border-radius: 0.12rem;
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__money_wrap__JTtSd[data-v-f2b5b3f8] {
  margin-left: 1.5rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  line-height: 1.4;
  color: #666;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__money_wrap__JTtSd .advance__money_title__1fzmi[data-v-f2b5b3f8] {
  font-size: 0.2rem;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__money_wrap__JTtSd .advance__current_money__3D4IU[data-v-f2b5b3f8] {
  font-family: DIN !important;
  font-size: 0.24rem;
  color: #ede9e7;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__money_wrap__JTtSd .advance__total_money__gQLlR[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  font-family: DIN !important;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__progress_container__1Ro80 .advance__money_wrap__JTtSd > div:first-child .advance__money_title__1fzmi[data-v-f2b5b3f8] {
  color: #6c6e82;
}
.advance__advance_container__1ft87 .advance__inner_box__2hOri .advance__progress_area__3lOLg .advance__static_tips__234Rr[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: #999;
}
.advance__advance_container__1ft87 .advance__kok_liner_btn__E2cr9[data-v-f2b5b3f8] {
  border-radius: 0.4rem;
  -webkit-box-shadow: 0 0.2rem 0.4rem 0 rgba(0, 136, 239, 0.2);
  box-shadow: 0 0.2rem 0.4rem 0 rgba(0, 136, 239, 0.2);
}
.advance__advance_container__1ft87 .advance__kok_liner_btn__E2cr9[data-v-f2b5b3f8],
.advance__advance_container__1ft87 .advance__liner_btn__2hWfW[data-v-f2b5b3f8] {
  width: 2.52rem;
  height: 0.8rem;
  line-height: 0.8rem;
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
  text-align: center;
  font-size: 0.32rem;
  color: #fff;
}
.advance__advance_container__1ft87 .advance__liner_btn__2hWfW[data-v-f2b5b3f8] {
  border-radius: 0.08rem;
}
.advance__advance_container__1ft87 .advance__step_text__2PSps[data-v-f2b5b3f8] {
  line-height: 0.36rem;
  font-size: 0.32rem;
  color: #666;
  text-align: center;
}
.advance__advance_container__1ft87 .advance__disable_btn__2xLVU[data-v-f2b5b3f8] {
  width: 2.52rem;
  height: 0.8rem;
  line-height: 0.8rem;
  border-radius: 0.08rem;
  text-align: center;
  font-size: 0.32rem;
  color: #fff;
  background: #818f9c;
}
.advance__advance_container__1ft87 .advance__kok_disable_btn__22mXZ[data-v-f2b5b3f8] {
  width: 2.52rem;
  height: 0.8rem;
  line-height: 0.8rem;
  border-radius: 0.4rem;
  background: #818f9c;
  text-align: center;
  font-size: 0.32rem;
  color: #fff;
}
.advance__advance_container__1ft87 .advance__veneue_area__3FSBE[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  width: 3.68rem;
  height: 0.8rem;
  padding: 0 0.24rem;
  border: 0.02rem solid #999;
  border-radius: 0.08rem;
  color: #999;
}
.advance__advance_container__1ft87 .advance__veneue_area__3FSBE span[data-v-f2b5b3f8] {
  font-size: 0.32rem;
}
.advance__advance_container__1ft87 .advance__veneue_area__3FSBE .advance__triangle__201GS[data-v-f2b5b3f8] {
  margin-top: 0.2rem;
  width: 0;
  height: 0;
  border: 0.16rem solid transparent;
  border-top: 0.2rem solid #333;
}
.advance__advance_container__1ft87 .advance__veneue_area__3FSBE.advance__kok_veneue__3Y7s-[data-v-f2b5b3f8] {
  border-radius: 0.4rem;
}
.advance__flow_common_container__FU_9s[data-v-f2b5b3f8] {
  padding: 0.2rem 0;
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU[data-v-f2b5b3f8] {
  background-color: #fff;
  position: relative;
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU > span[data-v-f2b5b3f8] {
  position: absolute;
  bottom: 0.2rem;
  left: 50%;
  -webkit-transform: translate(-50%);
  transform: translate(-50%);
  background-color: #ededed;
  height: 0.36rem;
  line-height: 0.36rem;
  width: 1.2rem;
  text-align: center;
  color: #666;
  border-radius: 0.18rem;
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg[data-v-f2b5b3f8] {
  width: 4.8rem;
  margin: 0 auto;
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .advance__pic__3ERbH[data-v-f2b5b3f8] {
  width: 4.8rem;
  height: 4.8rem;
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .slick-slider .slick-arrow[data-v-f2b5b3f8] {
  width: 0.48rem;
  height: 0.48rem;
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .slick-slider .slick-arrow[data-v-f2b5b3f8]:before {
  content: '';
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .slick-slider .slick-arrow.slick-prev[data-v-f2b5b3f8] {
  left: -1.04rem;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAPFBMVEUAAACurq6pqKirqqqqqan///+qqamqqKipqKiqqamqqamqqampqampqKirqampqamtqamurq7///+pqKgaa3Z/AAAAE3RSTlMAF9ZHmwH51/fysOrE339WPiYCcKsBnAAAAGZJREFUSMft1MsKgDAMRNFpq619+Mz//6tQF24zIEIhs75nmeDd2XJxINZEJDEiCymKkMIlWvguvAkTJj4UBXrRH0O8tP08PVdO9stB9mHsPlpv/R89NrJHJXusZI89Sg1g5oLix934uxMjxfB7MgAAAABJRU5ErkJggg==);
  background-repeat: no-repeat;
  background-size: 100% 100%;
  background-position: 0 0;
  -webkit-transform: translateY(-50%) rotate(180deg);
  transform: translateY(-50%) rotate(180deg);
}
.webpa .advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .slick-slider .slick-arrow.slick-prev[data-v-f2b5b3f8] {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAPFBMVEUAAACurq6pqKirqqqqqan///+qqamqqKipqKiqqamqqamqqampqampqKirqampqamtqamurq7///+pqKgaa3Z/AAAAE3RSTlMAF9ZHmwH51/fysOrE339WPiYCcKsBnAAAAGZJREFUSMft1MsKgDAMRNFpq619+Mz//6tQF24zIEIhs75nmeDd2XJxINZEJDEiCymKkMIlWvguvAkTJj4UBXrRH0O8tP08PVdO9stB9mHsPlpv/R89NrJHJXusZI89Sg1g5oLix934uxMjxfB7MgAAAABJRU5ErkJggg==);
}
.advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .slick-slider .slick-arrow.slick-next[data-v-f2b5b3f8] {
  right: -1.04rem;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAPFBMVEUAAACurq6pqKirqqqqqan///+qqamqqKipqKiqqamqqamqqampqampqKirqampqamtqamurq7///+pqKgaa3Z/AAAAE3RSTlMAF9ZHmwH51/fysOrE339WPiYCcKsBnAAAAGZJREFUSMft1MsKgDAMRNFpq619+Mz//6tQF24zIEIhs75nmeDd2XJxINZEJDEiCymKkMIlWvguvAkTJj4UBXrRH0O8tP08PVdO9stB9mHsPlpv/R89NrJHJXusZI89Sg1g5oLix934uxMjxfB7MgAAAABJRU5ErkJggg==);
  background-repeat: no-repeat;
  background-size: 100% 100%;
  background-position: 0 0;
}
.webpa .advance__flow_common_container__FU_9s .advance__imgBox__3g3hU .advance__slider__3oJpg .slick-slider .slick-arrow.slick-next[data-v-f2b5b3f8] {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAPFBMVEUAAACurq6pqKirqqqqqan///+qqamqqKipqKiqqamqqamqqampqampqKirqampqamtqamurq7///+pqKgaa3Z/AAAAE3RSTlMAF9ZHmwH51/fysOrE339WPiYCcKsBnAAAAGZJREFUSMft1MsKgDAMRNFpq619+Mz//6tQF24zIEIhs75nmeDd2XJxINZEJDEiCymKkMIlWvguvAkTJj4UBXrRH0O8tP08PVdO9stB9mHsPlpv/R89NrJHJXusZI89Sg1g5oLix934uxMjxfB7MgAAAABJRU5ErkJggg==);
}
.advance__flow_common_container__FU_9s .advance__info_container__2o7Vx[data-v-f2b5b3f8] {
  padding: 0 0.3rem;
  background: #fff;
  overflow-x: hidden;
  overflow-y: auto;
}
.advance__flow_common_container__FU_9s .advance__info_container__2o7Vx[data-v-f2b5b3f8]::-webkit-scrollbar {
  display: block;
  width: 0.08rem;
}
.advance__flow_common_container__FU_9s .advance__info_container__2o7Vx[data-v-f2b5b3f8]::-webkit-scrollbar-thumb {
  border-radius: 0.08rem;
  background: #51525b;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-flex-wrap: nowrap;
  -ms-flex-wrap: nowrap;
  flex-wrap: nowrap;
  height: 1.2rem;
  border-bottom: 0.02rem solid #ebedf0;
  font-size: 0.32rem;
  color: #333;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R[data-v-f2b5b3f8]:last-child {
  border-bottom: none;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__label__1Qcqf[data-v-f2b5b3f8] {
  width: 1.68rem;
  display: inline-block;
  font-size: 0.32rem;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__venue_name__9k4J5[data-v-f2b5b3f8] {
  font-size: 0.32rem;
  font-family: PingFangSC-Medium sans-serif;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__apply_amount__NNd-I[data-v-f2b5b3f8],
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__center_money__1KAMR[data-v-f2b5b3f8] {
  font-size: 0.36rem;
  font-family: DINPro-Medium sans-serif;
  color: #ede9e7;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__apply_input__3Vw5q > div[data-v-f2b5b3f8] {
  padding-left: 0;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__apply_input__3Vw5q > div > section[data-v-f2b5b3f8]:before {
  display: none;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__apply_input__3Vw5q > div > section > div[data-v-f2b5b3f8] {
  padding-left: 0;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__apply_input__3Vw5q > div > section div[data-v-f2b5b3f8] {
  font-size: 0.32rem;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__recycle__3_kZ5[data-v-f2b5b3f8] {
  font-size: 0.28rem;
  color: #ede9e7;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R .advance__apply_num__1jBvw[data-v-f2b5b3f8] {
  font-size: 0.28rem;
}
.advance__flow_common_container__FU_9s .advance__info_item__2R22R p[data-v-f2b5b3f8] {
  font-size: 0.32rem;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q[data-v-f2b5b3f8] {
  padding: 0.4rem 0 0.2rem;
  border-bottom: 0.02rem solid #ebedf0;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q.advance__need_top_boder__DZ736[data-v-f2b5b3f8] {
  border-top: 0.02rem solid #ebedf0;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q[data-v-f2b5b3f8]:last-child {
  border-bottom: none;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q p[data-v-f2b5b3f8] {
  margin-bottom: 0.2rem;
  font-size: 0.32rem;
  color: #333;
  word-break: break-all;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__detail_addr__3ZwLJ[data-v-f2b5b3f8] {
  margin-left: 1.28rem;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  text-overflow: ellipsis;
  overflow: hidden;
  word-break: break-all;
  line-height: normal;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__confirm_attr__WqL5v[data-v-f2b5b3f8] {
  line-height: 0.32rem;
  font-size: 0.28rem;
  color: #666;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__attribute_container__R22GE[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__attribute__1plaC[data-v-f2b5b3f8] {
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  height: 0.64rem;
  line-height: 0.64rem;
  border: 0.02rem solid #e5e5e5;
  margin: 0 0.2rem 0.2rem 0;
  padding: 0 0.4rem;
  border-radius: 0.08rem;
  position: relative;
  text-align: center;
  color: #666;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__attribute__1plaC.advance__attribute_active__3Qmvv[data-v-f2b5b3f8] {
  border: 0.02rem solid #ede9e7;
  color: #ede9e7;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__attribute__1plaC span[data-v-f2b5b3f8] {
  margin-right: -0.02rem;
  font-size: 0.28rem;
  display: inline-block;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__attribute__1plaC .advance__corner_icon__1okqv[data-v-f2b5b3f8] {
  position: absolute;
  z-index: 2;
  right: -0.02rem;
  width: 0.4rem;
  height: 0.4rem;
  bottom: -0.02rem;
}
.advance__flow_common_container__FU_9s .advance__gift_item__3Db_Q .advance__attribute__1plaC .advance__corner_area__1FmE3[data-v-f2b5b3f8] {
  position: absolute;
  z-index: 1;
  right: -0.02rem;
  width: 0.1rem;
  height: 0.1rem;
  bottom: -0.02rem;
  background: #ede9e7;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp[data-v-f2b5b3f8] {
  padding-bottom: 1rem;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs[data-v-f2b5b3f8] {
  padding: 0.4rem 0;
  border-bottom: 0.02rem solid #ebedf0;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs p[data-v-f2b5b3f8] {
  margin-bottom: 0.6rem;
  font-size: 0.32rem;
  color: #333;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8] {
  width: 100%;
  font-size: 0.32rem;
  color: #333;
  border: none;
  outline: none;
  overflow: visible;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::-webkit-input-placeholder {
  font-size: 0.32rem;
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::-moz-placeholder {
  font-size: 0.32rem;
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::-ms-input-placeholder {
  font-size: 0.32rem;
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::placeholder {
  font-size: 0.32rem;
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve div[data-v-f2b5b3f8] {
  width: 100%;
  font-size: 0.32rem;
  border: none;
  outline: none;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve div.advance__item_value__CwPJ-[data-v-f2b5b3f8] {
  color: #333;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve div.advance__item_place__1uPl1[data-v-f2b5b3f8] {
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_triangle__2-s6p[data-v-f2b5b3f8] {
  width: 0;
  height: 0;
  margin: 0.1rem 0.2rem 0 0;
  border: 0.16rem solid transparent;
  border-top: 0.2rem solid #333;
}
.advance__flow_common_container__FU_9s .advance__address_tips__2o8P5[data-v-f2b5b3f8] {
  margin: 0.4rem 0;
  text-align: center;
  font-size: 0.24rem;
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__calc_result__3AnS-[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  margin-top: 0.2rem;
  padding: 0 0.3rem;
  color: #263440;
}
.advance__flow_common_container__FU_9s .advance__calc_result__3AnS- p[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
}
.advance__flow_common_container__FU_9s .advance__calc_result__3AnS- .advance__calc_num__213Ht[data-v-f2b5b3f8] {
  font-family: DINPro sans-serif;
  color: #ede9e7;
}
.advance__flow_common_container__FU_9s .advance__save_addr__1wzD8[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  margin: 0.52rem auto 0.3rem;
  text-align: center;
  font-size: 0.24rem;
  color: #999;
}
.advance__flow_common_container__FU_9s .advance__save_addr__1wzD8 > i[data-v-f2b5b3f8] {
  display: inline-block;
  width: 0.32rem;
  height: 0.32rem;
  margin-right: 0.2rem;
  border: 0.02rem solid #ede9e7;
}
.advance__flow_common_container__FU_9s .advance__save_addr__1wzD8 > i.advance__icon_checked__1cygT[data-v-f2b5b3f8] {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAMAAAAM7l6QAAAAV1BMVEUAAABOgP9VhP9Of/9OgP9Qgf9Sg/9Xg/9Pf/9PgP9Ogf9NgP9Pf/9Mgv9dhv9bgf9PgP9Of/9PgP9Pf/9NgP9PgP9Qf/9Qf/9Pf/9Pgv9Xgf9Nhf9Of/+5qgaxAAAAHHRSTlMAzhH2ZTYnF9i1loh0UwwJ6+jjxKmLeV9VSSAfRyKOJwAAAGxJREFUKM/lz1kOgCAMRdEqIM7z7Nv/OiUSoyHQDXB/T5rmUWSlUnOaoGJVZJziYFVy6j7WW/vX2blYkJ+fTp3DSmBQr+YpuTWAaKz2vkk1gDUzKhT5KmHbyV/xaE2B2sRoScH0iIKYLtlRLN0k9wam2Nv/aAAAAABJRU5ErkJggg==);
  background-size: 100% 100%;
}
.webpa .advance__flow_common_container__FU_9s .advance__save_addr__1wzD8 > i.advance__icon_checked__1cygT[data-v-f2b5b3f8] {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAMAAAAM7l6QAAAAV1BMVEUAAABOgP9VhP9Of/9OgP9Qgf9Sg/9Xg/9Pf/9PgP9Ogf9NgP9Pf/9Mgv9dhv9bgf9PgP9Of/9PgP9Pf/9NgP9PgP9Qf/9Qf/9Pf/9Pgv9Xgf9Nhf9Of/+5qgaxAAAAHHRSTlMAzhH2ZTYnF9i1loh0UwwJ6+jjxKmLeV9VSSAfRyKOJwAAAGxJREFUKM/lz1kOgCAMRdEqIM7z7Nv/OiUSoyHQDXB/T5rmUWSlUnOaoGJVZJziYFVy6j7WW/vX2blYkJ+fTp3DSmBQr+YpuTWAaKz2vkk1gDUzKhT5KmHbyV/xaE2B2sRoScH0iIKYLtlRLN0k9wam2Nv/aAAAAABJRU5ErkJggg==);
}
.advance__flow_common_container__FU_9s .advance__bottom_btn_wrapper__2dKe6[data-v-f2b5b3f8] {
  width: 100%;
  background: #fff;
  height: 1rem;
}
.advance__flow_common_container__FU_9s .advance__bottom_btn_wrapper__2dKe6 .advance__bonus_btn__2hG_x[data-v-f2b5b3f8] {
  margin: 0 auto 0.2rem;
}
.advance__flow_common_container__FU_9s .advance__bonus_btn__2hG_x[data-v-f2b5b3f8] {
  width: 6.94rem;
  height: 0.88rem;
  margin: 0.8rem auto 0.2rem;
  line-height: 0.88rem;
  border-radius: 0.08rem;
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
  text-align: center;
  font-size: 0.32rem;
  color: #fff;
}
.advance__flow_common_container__FU_9s .am-list-item[data-v-f2b5b3f8] {
  background: transparent;
}
.advance__flow_common_container__FU_9s .bonus_money_input section .am-list-item .am-input-control .fake-input-container .fake-input[data-v-f2b5b3f8] {
  font-size: 0.36rem;
  font-family: DINPro-Medium sans-serif;
  color: #333;
  font-weight: 400;
}
.advance__flow_common_container__FU_9s .bonus_money_input section .am-list-item .am-input-control .fake-input-container .fake-input-placeholder[data-v-f2b5b3f8] {
  color: #999;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs[data-v-f2b5b3f8] {
  padding: 0.24rem 0;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs p[data-v-f2b5b3f8] {
  margin-bottom: 0.1rem;
  font-size: 0.28rem;
  color: #dedede;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8] {
  font-size: 0.28rem;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::-webkit-input-placeholder {
  font-size: 0.28rem;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::-moz-placeholder {
  font-size: 0.28rem;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::-ms-input-placeholder {
  font-size: 0.28rem;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs uni-input[data-v-f2b5b3f8]::placeholder {
  font-size: 0.28rem;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve .advance__item_value__CwPJ-[data-v-f2b5b3f8] {
  font-size: 0.28rem;
  color: #dedede;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve .advance__select_triangle__2-s6p[data-v-f2b5b3f8] {
  margin: 0;
  border: none;
  width: 0.32rem;
  height: 0.32rem;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcBAMAAACAI8KnAAAALVBMVEUAAABtb4NsdINtb4Nvb4Rtb4JtboNsboNtb4Jsb4Ntb4Ntb4JuboRwcIdsboItqaZGAAAADnRSTlMAcyH0PuGi35LSw7ZRIGeyXfwAAAAxSURBVBjTY8AETAdQuFwPUbgsdgIofOfBK71vAjKP7akCMjcvaKAlOZ4roHBrGXAAAL86FSc0mp/VAAAAAElFTkSuQmCC);
  background-repeat: no-repeat;
  background-position: 50%;
  background-size: contain;
}
.webpa .advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve .advance__select_triangle__2-s6p[data-v-f2b5b3f8] {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcBAMAAACAI8KnAAAALVBMVEUAAABtb4NsdINtb4Nvb4Rtb4JtboNsboNtb4Jsb4Ntb4Ntb4JuboRwcIdsboItqaZGAAAADnRSTlMAcyH0PuGi35LSw7ZRIGeyXfwAAAAxSURBVBjTY8AETAdQuFwPUbgsdgIofOfBK71vAjKP7akCMjcvaKAlOZ4roHBrGXAAAL86FSc0mp/VAAAAAElFTkSuQmCC);
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__personal_info__2Zfwp .advance__pinfo_item__zmVXs .advance__select_area__4J7ve .advance__item_place__1uPl1[data-v-f2b5b3f8] {
  color: #6c6e82;
}
.advance__yibo_flow_common_container__10BDT .advance__info_container__2o7Vx .advance__address_tips__2o8P5[data-v-f2b5b3f8] {
  margin: 0.32rem 0 0.8rem;
  color: #6c6e82;
}
.advance__success_container__m2Hd-[data-v-f2b5b3f8] {
  width: 100%;
  padding: 1.8rem 0 1.6rem;
  text-align: center;
  color: #999;
}
.advance__success_container__m2Hd- p[data-v-f2b5b3f8] {
  line-height: 0.36rem;
}
.advance__success_container__m2Hd- .advance__my_gift__2gK_l[data-v-f2b5b3f8] {
  color: #999;
}
.advance__success_container__m2Hd- img[data-v-f2b5b3f8] {
  width: 1.6rem;
  height: 1.6rem;
  margin: 0 auto 0.4rem;
}
.advance__back_home_btn__3lwpY[data-v-f2b5b3f8],
.advance__finish_btn__2GvIk[data-v-f2b5b3f8] {
  width: 6.94rem;
  height: 0.88rem;
  line-height: 0.88rem;
  margin: 0 auto;
  border-radius: 0.44rem;
  text-align: center;
  font-size: 0.32rem;
}
.advance__finish_btn__2GvIk[data-v-f2b5b3f8] {
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
  color: #fff;
}
.advance__back_home_btn__3lwpY[data-v-f2b5b3f8] {
  margin-top: 0.28rem;
  background: transparent;
  border: 0.02rem solid #ede9e7;
  color: #ede9e7;
}
.advance__dialogBox__3GI5y[data-v-f2b5b3f8] {
  position: relative;
  width: 6.22rem;
  border-radius: 0.1rem;
  background-color: #fff;
}
.advance__dialogContent__2E5mj[data-v-f2b5b3f8] {
  padding: 0.4rem 0.4rem 0.1rem;
}
.advance__dialogContent__2E5mj h3[data-v-f2b5b3f8] {
  font-size: 0.32rem;
  line-height: 0.36rem;
  color: #263440;
  margin-bottom: 0.14rem;
}
.advance__dialogContent__2E5mj p[data-v-f2b5b3f8] {
  line-height: 0.44rem;
  font-size: 0.28rem;
  color: #999;
}
.advance__dialogContent__2E5mj .am-list-item[data-v-f2b5b3f8] {
  min-height: 0.68rem;
}
.advance__dialogContent__2E5mj .am-list-item .am-input-control .fake-input-container .fake-input[data-v-f2b5b3f8] {
  height: 80%;
  font-size: 0.28rem;
}
.advance__dialogContent__2E5mj .am-list-item:not(:last-child) .am-list-line[data-v-f2b5b3f8] {
  border-bottom: none;
}
.advance__dialogAmount__2r7kq[data-v-f2b5b3f8] {
  font-weight: 600;
  margin: 0.3rem;
  font-size: 0.28rem;
  text-align: center;
  color: #263440;
}
.advance__dialogAmountInput__3vyuq[data-v-f2b5b3f8] {
  width: 3.8rem;
  padding-left: 0.32rem;
  height: 0.68rem !important;
  border-radius: 0.1rem;
  border: 1px solid #eceef2;
}
.advance__activeLine__28caC[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  margin-bottom: 0.1rem;
}
.advance__dialogWran__3cR9k[data-v-f2b5b3f8] {
  text-align: center;
  font-size: 0.24rem;
  line-height: 0.44rem;
  color: #ff4634;
}
.advance__dialogFooter__17phi[data-v-f2b5b3f8] {
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  border-top: 0.02rem solid #eee;
  height: 1rem;
  color: #414655;
}
.advance__dialogFooter__17phi[data-v-f2b5b3f8],
.advance__dialogFooter__17phi > div[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
}
.advance__dialogFooter__17phi > div[data-v-f2b5b3f8] {
  cursor: pointer;
  font-family: PingFang SC, Helvetica Neue, Hiragino Sans GB, Helvetica, Microsoft YaHei, Arial;
  font-size: 0.36rem;
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
}
.advance__dialogFooter__17phi > div[data-v-f2b5b3f8]:not(:last-child) {
  border-right: 0.02rem solid #eee;
}
.advance__dialogPrimaryBtn__1SinD[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.advance__btn__2sAvm[data-v-f2b5b3f8] {
  height: 0.68rem;
  font-size: 0.28rem;
  text-align: center;
  line-height: 0.68rem;
  width: 1.4rem;
  color: #fff;
  background-color: #ede9e7;
  border-radius: 0.1rem;
}
.dialog__masker__3GFYu[data-v-f2b5b3f8] {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  width: 100%;
  height: 100%;
  margin: auto;
  background-color: rgba(0, 0, 0, 0.7);
  z-index: 15;
}
.dialog__dialog_container__1aVBT[data-v-f2b5b3f8] {
  position: relative;
  width: 4.2rem;
  text-align: center;
  background-color: #fff;
  border-radius: 2px;
}
.dialog__dialog_container__1aVBT img[data-v-f2b5b3f8] {
  max-width: 100%;
  height: -webkit-fit-content;
  height: -moz-fit-content;
  height: fit-content;
}
.dialog__dialog_title__1TmOR[data-v-f2b5b3f8] {
  padding: 0.6rem 0 0.24rem;
  font-weight: 700;
  font-size: 0.28rem;
}
.dialog__dialog_content__25Rwk[data-v-f2b5b3f8] {
  padding: 0 0.49rem;
  line-height: 1.5;
  font-size: 0.24rem;
  text-align: justify;
}
.dialog__dialog_footer__1Dg-u[data-v-f2b5b3f8] {
  margin: 0.4rem 0;
}
.dialog__dialog_footer__1Dg-u > uni-button[data-v-f2b5b3f8] {
  width: 1.43rem;
  height: 0.64rem;
  color: #ede9e7;
  background-color: #fff;
  border: 1px solid #ede9e7;
  border-radius: 0.4rem;
}
.dialog__dialog_footer__1Dg-u > uni-button[data-v-f2b5b3f8]:first-child {
  margin-right: 0.1rem;
}
.dialog__dialog_footer__1Dg-u > uni-button[data-v-f2b5b3f8]:last-child {
  margin-left: 0.1rem;
}
.dialog__dialog_footer__1Dg-u > uni-button.dialog__active_bg__1PErg[data-v-f2b5b3f8] {
  color: #fff;
  background-color: #ede9e7;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__slider__nVYOW[data-v-f2b5b3f8] {
  min-height: 3rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY[data-v-f2b5b3f8] {
  background: #0d1a26;
  background-size: cover;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1[data-v-f2b5b3f8] {
  z-index: 3333;
  width: 6.03rem;
  min-height: 2.97rem;
  overflow: hidden;
  margin: 0 auto;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: bottom;
  position: relative;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > img[data-v-f2b5b3f8] {
  width: 100%;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1[data-v-f2b5b3f8]:after {
  content: '';
  position: absolute;
  width: 3.84px;
  width: 0.24rem;
  height: 1.6px;
  height: 0.1rem;
  left: 50%;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  bottom: 0;
  display: block;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > span[data-v-f2b5b3f8] {
  display: block;
  position: absolute;
  left: 0;
  top: 0;
  height: 0.44rem;
  width: 0.72rem;
  line-height: 0.44rem;
  text-align: center;
  background-color: #e0edf9;
  border-radius: 0.16rem 0 0.16rem 0;
  font-size: 0.24rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > span.vipLevelStyle__active__2qMBM[data-v-f2b5b3f8] {
  background-color: #ffe9b7;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div[data-v-f2b5b3f8] {
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  bottom: 0;
  padding: 0.32rem 0 0 0.72rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div h3[data-v-f2b5b3f8] {
  font-size: 0.52rem;
  color: #fff;
  font-weight: 700;
  font-style: italic;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > p[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: hsla(0, 0%, 100%, 0.6);
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  white-space: nowrap;
  margin-left: -0.6rem;
  line-height: 0.42rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > p > span[data-v-f2b5b3f8] {
  font-size: 0.32rem;
  color: #fff;
  position: relative;
  top: 0.02rem;
  display: inline-block;
  margin: 0 0.05rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > p.vipLevelStyle__last__1BytD[data-v-f2b5b3f8] {
  position: relative;
  top: -0.15rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > p.vipLevelStyle__last__1BytD > span[data-v-f2b5b3f8] {
  margin-left: 0;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > div[data-v-f2b5b3f8] {
  position: absolute;
  left: 0.2rem;
  color: hsla(0, 0%, 100%, 0.6);
  font-size: 0.24rem;
  bottom: 0.09rem;
  line-height: 0.4rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > div > p[data-v-f2b5b3f8] {
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  white-space: nowrap;
  font-size: 0.24rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > div > p span[data-v-f2b5b3f8] {
  display: inline-block;
  font-size: 0.28rem;
  color: #fff;
  margin: 0 0.05rem;
  position: relative;
  top: 0.01rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__list__3uiPY .vipLevelStyle__box__DGqq1 > div > div > div > p[data-v-f2b5b3f8]:last-child {
  position: relative;
  top: -0.14rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0[data-v-f2b5b3f8] {
  height: 4.8rem;
  padding: 0.32rem 0.32rem 0;
  background-color: #fff;
  height: 5.3rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 .vipLevelStyle__everyMonthGift__Zr5-K span[data-v-f2b5b3f8] {
  font-size: 0.4rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 h3[data-v-f2b5b3f8] {
  font-size: 0.32rem;
  color: #000;
  padding-left: 0.16rem;
  position: relative;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 h3 img[data-v-f2b5b3f8] {
  position: absolute;
  left: 0;
  top: 0.05rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  margin-top: 0.246rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8] {
  height: 1.3rem;
  width: 50%;
  position: relative;
  padding: 0.22rem 0 0 1.12rem;
  color: #555;
  font-size: 0.4rem;
  display: block;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:nth-child(3),
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:nth-child(4),
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:nth-child(5),
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:nth-child(6) {
  height: 1.5rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:after {
  content: '';
  display: block;
  width: 44.8px;
  width: 2.8rem;
  height: 1px;
  background: #ebedf0;
  position: absolute;
  left: 1.6px;
  left: 0.1rem;
  bottom: 0;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:nth-child(odd):before {
  content: '';
  display: block;
  width: 1px;
  height: 10.4px;
  height: 0.65rem;
  background: #ebedf0;
  position: absolute;
  right: 3.2px;
  right: 0.2rem;
  top: 50%;
  -webkit-transform: translateY(-50%);
  transform: translateY(-50%);
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:last-of-type:after,
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li[data-v-f2b5b3f8]:last-of-type:before {
  display: none;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li:last-child span[data-v-f2b5b3f8] {
  overflow: hidden;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li img.vipLevelStyle__icon__3xwaY[data-v-f2b5b3f8] {
  position: absolute;
  left: 0.2rem;
  width: 0.8rem;
  top: 0.256rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li > span[data-v-f2b5b3f8] {
  display: block;
  font-size: 0.24rem;
  color: #697b8c;
  width: 2.4rem;
  line-height: 0.34rem;
  white-space: pre-wrap;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  display: -webkit-box;
  overflow: hidden;
  word-break: break-all;
  width: 2rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li > b[data-v-f2b5b3f8] {
  font-size: 0.28rem;
  position: absolute;
  top: 0.18rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li > p[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: #697b8c;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  white-space: nowrap;
  position: relative;
  left: -0.25rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__privilege__3DLJ0 ul li > p span[data-v-f2b5b3f8] {
  color: #000;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN[data-v-f2b5b3f8] {
  margin: 0.16rem 0 0;
  background-color: #fff;
  padding: 0.32rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN .vipLevelStyle__notes__26_Vb[data-v-f2b5b3f8] {
  font-size: 0.18rem;
  color: #697b8c;
  text-align: center;
  font-weight: 400;
  padding: 0.1rem 0;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN h3[data-v-f2b5b3f8] {
  font-size: 0.32rem;
  color: #000;
  padding-left: 0.16rem;
  line-height: 0.32rem;
  position: relative;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN h3 img[data-v-f2b5b3f8] {
  position: absolute;
  left: 0;
  top: 0.05rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  margin-top: 0.246rem;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8] {
  height: 1.25rem;
  width: 33.3%;
  position: relative;
  padding: 0.22rem 0 0;
  color: #555;
  display: block;
  font-size: 0.24rem;
  text-align: center;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:before {
  content: '';
  display: block;
  width: 1px;
  height: 10.4px;
  height: 0.65rem;
  background: #ebedf0;
  position: absolute;
  right: 0;
  top: 50%;
  -webkit-transform: translateY(-50%);
  transform: translateY(-50%);
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:after {
  content: '';
  display: block;
  width: 80%;
  height: 1px;
  background: #ebedf0;
  position: absolute;
  left: 50%;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  bottom: 0;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li.vipLevelStyle__no__337Rf[data-v-f2b5b3f8]:after,
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:nth-child(3):before,
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:nth-child(6):before,
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:nth-child(9):before,
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:nth-child(12):before,
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li[data-v-f2b5b3f8]:nth-child(15):before {
  display: none;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li > span[data-v-f2b5b3f8] {
  color: #be9e55;
  display: block;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li > span span[data-v-f2b5b3f8] {
  font-size: 0.4rem;
  font-weight: 400;
}
.vipLevelStyle__vipLevel__TJpIe .vipLevelStyle__myVip__2faiN ul li > p[data-v-f2b5b3f8] {
  display: block;
  font-size: 0.24rem;
  color: #697b8c;
  position: relative;
  font-weight: 400;
}
.progressStyle__progress__Pe7Gi[data-v-f2b5b3f8] {
  height: 2.46rem;
  position: relative;
}
.progressStyle__progress__Pe7Gi .progressStyle__v10__2CJBj[data-v-f2b5b3f8] {
  position: absolute;
  width: 1.4rem;
  left: 50%;
  top: 0.35rem;
  z-index: 0;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
}
.progressStyle__progress__Pe7Gi .progressStyle__img1__1sWd0[data-v-f2b5b3f8] {
  width: 3.82rem;
  position: absolute;
  left: 50%;
  top: 0.1rem;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
}
.progressStyle__progress__Pe7Gi .progressStyle__img2__2hjYC[data-v-f2b5b3f8] {
  bottom: 0.6rem;
}
.progressStyle__progress__Pe7Gi .progressStyle__img2__2hjYC[data-v-f2b5b3f8],
.progressStyle__progress__Pe7Gi > p[data-v-f2b5b3f8] {
  width: 3.4rem;
  position: absolute;
  left: 50%;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
}
.progressStyle__progress__Pe7Gi > p[data-v-f2b5b3f8] {
  top: 0.34rem;
  height: 1.6rem;
}
.progressStyle__progress__Pe7Gi > p > img[data-v-f2b5b3f8] {
  width: 0.34rem;
  position: absolute;
  z-index: 2;
}
.progressStyle__progress__Pe7Gi > p > span[data-v-f2b5b3f8] {
  display: block;
  overflow: hidden;
  position: absolute;
  bottom: 0.1rem;
  left: 0;
  background-image: url(/static/img/xian-jindu@2x.cd057734d860bb96238847a370be6b7f.cd057734.png);
  background-repeat: no-repeat;
  background-position: 0 100%;
  background-size: 3.4rem auto;
}
.webpa .progressStyle__progress__Pe7Gi > p > span[data-v-f2b5b3f8] {
  background-image: url(/static/img/xian-jindu@2x.cd057734d860bb96238847a370be6b7f.cd057734.png);
}
.progressStyle__progress__Pe7Gi > p > b[data-v-f2b5b3f8] {
  display: block;
  overflow: hidden;
  position: absolute;
  width: 100%;
  height: 1.25rem;
  top: -0.03rem;
  left: 0;
  background-image: url(/static/img/xian-jindu@2x.cd057734d860bb96238847a370be6b7f.cd057734.png);
  background-repeat: no-repeat;
  background-position: 0 0;
  background-size: 3.4rem auto;
}
.webpa .progressStyle__progress__Pe7Gi > p > b[data-v-f2b5b3f8] {
  background-image: url(/static/img/xian-jindu@2x.cd057734d860bb96238847a370be6b7f.cd057734.png);
}
.progressStyle__progress__Pe7Gi > span[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: #e7d3a5;
  position: absolute;
  left: 50%;
  bottom: 0.56rem;
}
.progressStyle__progress__Pe7Gi > span.progressStyle__l__p6Xbv[data-v-f2b5b3f8] {
  margin-left: -2.3rem;
}
.progressStyle__progress__Pe7Gi > span.progressStyle__r__YM2p5[data-v-f2b5b3f8] {
  margin-left: 1.8rem;
}
.progressStyle__progress__Pe7Gi > div[data-v-f2b5b3f8] {
  padding-top: 0.77rem;
}
.progressStyle__progress__Pe7Gi > div > span[data-v-f2b5b3f8] {
  height: 0.63rem;
  width: 0.63rem;
  background: #fff;
  border-radius: 0.63rem;
  border: 1px solid #fff;
  overflow: hidden;
  display: block;
  margin: 0 auto;
  z-index: 1;
  position: relative;
}
.progressStyle__progress__Pe7Gi > div > span img[data-v-f2b5b3f8] {
  width: 100%;
  height: 100%;
  border-radius: 50%;
}
.progressStyle__progress__Pe7Gi > div > img[data-v-f2b5b3f8] {
  display: block;
  margin: 0.1rem auto 0.07rem;
  width: 0.6rem;
}
.progressStyle__progress__Pe7Gi > div > p[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: #ede9e7;
  text-align: center;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__banner__1wayq[data-v-f2b5b3f8] {
  width: 100%;
  height: 4.56rem;
  background-repeat: no-repeat;
  background-position: 50%;
  background-size: cover;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__waterDesc__3uYu5[data-v-f2b5b3f8] {
  padding: 0.24rem 0.26rem;
  color: #a5aeb9;
  font-size: 0.24rem;
  background: #fff;
  line-height: 0.44rem;
  margin-bottom: 0.4rem;
}
.vipDetailStyle__vipDetail__2q9TP > ul[data-v-f2b5b3f8] {
  padding: 0.2rem 0.2rem 0;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  height: 1.96rem;
}
.vipDetailStyle__vipDetail__2q9TP > ul li[data-v-f2b5b3f8] {
  width: 1.36rem;
  height: 0.7rem;
  line-height: 0.7rem;
  background: #fff;
  border-radius: 0.3rem;
  margin-bottom: 0.16rem;
  position: relative;
  padding-left: 0.05rem;
}
.vipDetailStyle__vipDetail__2q9TP > ul li img[data-v-f2b5b3f8] {
  width: 0.7rem;
  display: block;
  border: none;
}
.vipDetailStyle__vipDetail__2q9TP > ul li span[data-v-f2b5b3f8] {
  display: block;
  font-size: 0.24rem;
  color: #9aa7b5;
  position: absolute;
  top: 0;
  left: 0.65rem;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
}
.vipDetailStyle__vipDetail__2q9TP > ul li span.vipDetailStyle__active__3-3yq[data-v-f2b5b3f8] {
  color: #0d1a26;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT[data-v-f2b5b3f8] {
  padding: 0.36rem 0.4rem 0.24rem;
  background-color: #fff;
  margin-bottom: 0.32rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT[data-v-f2b5b3f8]:last-of-type {
  margin-bottom: 0;
  padding: 0 0 0.24rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV[data-v-f2b5b3f8] {
  width: 6.03rem;
  margin: 0 auto 0.2rem;
  position: relative;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > img[data-v-f2b5b3f8] {
  width: 100%;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > span[data-v-f2b5b3f8] {
  display: block;
  position: absolute;
  left: 0;
  top: 0;
  height: 0.44rem;
  padding: 0 0.12rem;
  line-height: 0.44rem;
  text-align: center;
  background-color: #e0edf9;
  border-radius: 0.14rem 0 0.14rem 0;
  font-size: 0.24rem;
  color: #3c4b69;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > span.vipDetailStyle__active__3-3yq[data-v-f2b5b3f8] {
  background-color: #ffe9b7;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > div[data-v-f2b5b3f8] {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  padding: 0.6rem 0 0 0.72rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > div h3[data-v-f2b5b3f8] {
  font-size: 0.52rem;
  color: #fff;
  font-weight: 700;
  font-style: italic;
  margin-bottom: 0.1rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > div > p[data-v-f2b5b3f8] {
  position: relative;
  padding-left: 2.36rem;
  font-size: 0.24rem;
  color: hsla(0, 0%, 100%, 0.8);
  margin-bottom: 0.1rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__card__3aRNV > div > p span[data-v-f2b5b3f8] {
  color: hsla(0, 0%, 100%, 0.5);
  position: absolute;
  left: -0.1rem;
  top: 0.01rem;
  display: block;
  max-width: 2.8rem;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  -webkit-transform-origin: left center;
  transform-origin: left center;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div[data-v-f2b5b3f8] {
  margin-bottom: 0.12rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div uni-button[data-v-f2b5b3f8] {
  border: 0;
  height: 0.48rem;
  line-height: 0.48rem;
  text-align: center;
  width: 1.34rem;
  color: #fff;
  background-color: #ede9e7;
  font-size: 0.24rem;
  border-radius: 0.04rem;
  position: absolute;
  top: 0.24rem;
  right: 0.48rem;
  padding: 0;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div uni-button > span[data-v-f2b5b3f8] {
  display: block;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div > div[data-v-f2b5b3f8] {
  padding: 0.22rem 0 0 0.46rem;
  background: rgba(107, 183, 238, 0.04);
  border-radius: 0.12rem;
  position: relative;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div > div > p[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: rgba(0, 0, 0, 0.85);
  margin-bottom: 0.08rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div > div > p > span[data-v-f2b5b3f8] {
  color: #ede9e7;
  font-weight: 700;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div > div > p > span.vipDetailStyle__no__3JjM8[data-v-f2b5b3f8] {
  color: rgba(0, 0, 0, 0.85);
  font-weight: 400;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div > div[data-v-f2b5b3f8]:nth-child(2) {
  padding-left: 0.36rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div > div > span[data-v-f2b5b3f8] {
  display: block;
  color: #9aa7b5;
  font-size: 0.24rem;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  -webkit-transform-origin: left center;
  transform-origin: left center;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  max-width: 2.8rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__info1__2kXvH[data-v-f2b5b3f8] {
  height: 1.44rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__info1__2kXvH > div[data-v-f2b5b3f8] {
  width: 49%;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__info1__2kXvH > div:last-child p[data-v-f2b5b3f8]:nth-child(2) {
  margin-bottom: 0.04rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__birthday__2V0Ku > div[data-v-f2b5b3f8] {
  width: 100%;
  height: 0.98rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__birthday__2V0Ku > div > p[data-v-f2b5b3f8] {
  padding-top: 0.1rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__advance__v1zId[data-v-f2b5b3f8] {
  height: 3.14rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__advance__v1zId > div[data-v-f2b5b3f8] {
  width: 49%;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__advance__v1zId > div[data-v-f2b5b3f8]:first-child {
  padding-top: 1.2rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__advance__v1zId > div:last-child p[data-v-f2b5b3f8] {
  margin-bottom: 0.125rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__vanuePromo__1wktT > div[data-v-f2b5b3f8] {
  width: 100%;
  height: 0.98rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__vanuePromo__1wktT > div > p[data-v-f2b5b3f8] {
  padding-top: 0.1rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__vanuePromo__1wktT > div > p > b[data-v-f2b5b3f8] {
  display: block;
  position: absolute;
  height: 0.48rem;
  line-height: 0.48rem;
  text-align: center;
  width: 1.88rem;
  background: #fff;
  border: 1px solid hsla(0, 0%, 80%, 0.5);
  font-size: 0.24rem;
  font-weight: 400;
  left: 1.7rem;
  top: 0.26rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__vanuePromo__1wktT > div > p > b > span[data-v-f2b5b3f8] {
  display: block;
  color: #9aa7b5;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__gif__1otJE > div[data-v-f2b5b3f8] {
  height: 1.2rem;
  width: 100%;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__gif__1otJE > div > span[data-v-f2b5b3f8] {
  line-height: 0.32rem;
  margin-left: -0.7rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > div.vipDetailStyle__gif__1otJE uni-button[data-v-f2b5b3f8] {
  top: 0.36rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > p[data-v-f2b5b3f8] {
  padding: 0.32rem 0 0.2rem 0.46rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  height: 1.65rem;
  font-size: 0.24rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > p > span[data-v-f2b5b3f8] {
  display: block;
  width: 50%;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > p > span > span[data-v-f2b5b3f8] {
  color: #ede9e7;
  font-weight: 700;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > p > span.vipDetailStyle__y__2MUZu[data-v-f2b5b3f8] {
  text-align: right;
  padding-right: 0.48rem;
  white-space: nowrap;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP > p > span.vipDetailStyle__y__2MUZu > span[data-v-f2b5b3f8] {
  color: #be9e55;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP .vipDetailStyle__birthTip__2wEuy[data-v-f2b5b3f8] {
  display: block;
  padding: 0.3rem 0;
  background-color: rgba(107, 183, 238, 0.04);
  text-align: center;
  border-radius: 0.12rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP .vipDetailStyle__birthTip__2wEuy .vipDetailStyle__money__3SNQm[data-v-f2b5b3f8] {
  margin-bottom: 0.1rem;
  font-size: 0.24rem;
  color: rgba(0, 0, 0, 0.85);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP .vipDetailStyle__birthTip__2wEuy .vipDetailStyle__money__3SNQm span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP .vipDetailStyle__birthTip__2wEuy .vipDetailStyle__money2__UuJH0[data-v-f2b5b3f8] {
  margin-bottom: 0.2rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP .vipDetailStyle__birthTip__2wEuy .vipDetailStyle__tip__20JCO[data-v-f2b5b3f8] {
  color: #9aa7b5;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__vipInfo__3IZIT .vipDetailStyle__info__1NaSP .vipDetailStyle__birthTip__2wEuy .vipDetailStyle__tip__20JCO .vipDetailStyle__btn__10i8z[data-v-f2b5b3f8] {
  display: inline-block;
  width: 1.68rem;
  height: 0.56rem;
  border-radius: 0.3rem;
  color: #ede9e7;
  border: 0.5px solid #ede9e7;
  text-align: center;
  line-height: 0.56rem;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__tab__2T-IZ[data-v-f2b5b3f8] {
  height: 0.92rem;
  line-height: 0.92rem;
  background-color: #fff;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  font-size: 0.28rem;
  color: #697b8c;
  border-bottom: 1px solid hsla(0, 0%, 86.7%, 0.4);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__tab__2T-IZ > span[data-v-f2b5b3f8] {
  display: block;
  width: 50%;
  position: relative;
  text-align: center;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__tab__2T-IZ > span.vipDetailStyle__active__3-3yq[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__tab__2T-IZ > span.vipDetailStyle__active__3-3yq[data-v-f2b5b3f8]:after {
  content: '';
  display: block;
  width: 31.68px;
  width: 1.98rem;
  height: 2px;
  background: #ede9e7;
  position: absolute;
  left: 50%;
  bottom: 0;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__note__2kqnK p[data-v-f2b5b3f8] {
  padding: 0 0.34rem 0 0.84rem;
  line-height: 1.5;
  text-align: justify;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__title__d_Riu[data-v-f2b5b3f8] {
  height: 0.94rem;
  line-height: 0.94rem;
  text-align: center;
  color: rgba(0, 0, 0, 0.85);
  background: -webkit-gradient(linear, left top, right top, from(rgba(7, 226, 255, 0)), to(rgba(3, 193, 253, 0.09)));
  background: -webkit-linear-gradient(left, rgba(7, 226, 255, 0), rgba(3, 193, 253, 0.09));
  background: linear-gradient(90deg, rgba(7, 226, 255, 0), rgba(3, 193, 253, 0.09));
  border-bottom: 1px solid hsla(0, 0%, 86.7%, 0.4);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__title__d_Riu span[data-v-f2b5b3f8] {
  display: inline-block;
  text-align: center;
  padding-left: 0.55rem;
  position: relative;
  font-size: 0.28rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__title__d_Riu span img[data-v-f2b5b3f8] {
  height: 0.5rem;
  margin-right: 0.16rem;
  position: absolute;
  left: 0;
  top: 0.18rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__more_rule_detail__3qH-C[data-v-f2b5b3f8] {
  padding: 0.12rem 0.12rem 0;
  margin: auto;
  color: #c2ac79;
  display: block;
  text-decoration: underline;
  background-color: #fff;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc[data-v-f2b5b3f8] {
  background-color: #fff;
  overflow-x: auto;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  text-align: center;
  -webkit-flex-wrap: nowrap;
  -ms-flex-wrap: nowrap;
  flex-wrap: nowrap;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc > ul[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc > ul:first-child > li[data-v-f2b5b3f8] {
  color: #c1ab77 !important;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc > ul > li[data-v-f2b5b3f8] {
  height: 0.9rem;
  line-height: 0.9rem;
  font-size: 0.24rem;
  color: #98a5b3;
  display: block;
  border: 1px solid hsla(0, 0%, 86.7%, 0.4);
  border-left: 0;
  border-top: 0;
  text-align: center;
  white-space: nowrap;
  padding: 0 5px;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc > ul > li[data-v-f2b5b3f8]:first-child {
  color: rgba(51, 51, 51, 0.8);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc > ul > li[data-v-f2b5b3f8]:nth-of-type(2n) {
  background: rgba(251, 251, 252, 0.53);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__table__2KDLc > ul > li span[data-v-f2b5b3f8] {
  display: block;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  white-space: nowrap;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz[data-v-f2b5b3f8] {
  margin: 0 0 0.22rem;
  background-color: #fff;
  padding-bottom: 0.3rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz h2[data-v-f2b5b3f8] {
  height: 0.9rem;
  line-height: 0.9rem;
  border-bottom: 1px solid #f8fafa;
  margin-bottom: 0.1rem;
  text-align: center;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz h2 span[data-v-f2b5b3f8] {
  display: inline-block;
  height: 100%;
  line-height: 0.9rem;
  font-size: 0.32rem;
  color: #cf866b;
  position: relative;
  font-weight: 400;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz h2 span[data-v-f2b5b3f8]:after {
  content: '';
  width: 1.28px;
  width: 0.08rem;
  height: 4.48px;
  height: 0.28rem;
  background: #1890ff;
  position: absolute;
  left: -3.84px;
  left: -0.24rem;
  top: 50%;
  margin-top: -0.11rem;
  -webkit-transform: skewY(-20deg);
  transform: skewY(-20deg);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > h4[data-v-f2b5b3f8] {
  height: 0.4rem;
  line-height: 0.4rem;
  padding-left: 0.84rem;
  font-size: 0.28rem;
  position: relative;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > h4 > span[data-v-f2b5b3f8] {
  position: absolute;
  left: 0.34rem;
  top: 0.05rem;
  width: 0.32rem;
  height: 0.32rem;
  line-height: 0.32rem;
  border-radius: 50%;
  background-color: #ede9e7;
  text-align: center;
  color: #fff;
  font-size: 0.28rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > p[data-v-f2b5b3f8] {
  padding: 0 0.34rem 0 0.84rem;
  line-height: 0.4rem;
  font-size: 0.24rem;
  color: #98a5b3;
  margin-bottom: 0.32rem;
  text-align: justify;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > div[data-v-f2b5b3f8] {
  text-align: center;
  color: rgba(51, 51, 51, 0.8);
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > div > p[data-v-f2b5b3f8] {
  color: #356dfc;
  margin: 0.1rem 0 0.3rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > span[data-v-f2b5b3f8] {
  display: block;
  margin: 0.2rem auto;
  width: 2.1rem;
  height: 0.8rem;
  position: relative;
  font-size: 0.28rem;
  text-align: center;
  color: #98a7b5;
  padding-top: 0.3rem;
  -webkit-box-shadow: 0.02rem 0.04rem 0.18rem #f3f3f3;
  box-shadow: 0.02rem 0.04rem 0.18rem #f3f3f3;
  border-radius: 0.8rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > span img[data-v-f2b5b3f8] {
  position: absolute;
  left: 50%;
  top: 0.05rem;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  height: 0.3rem;
}
.style__monthBac__10SgC[data-v-f2b5b3f8] {
  padding: 0 0.32rem;
  margin: 0 auto;
  background-color: #f0f6fe;
  width: 6.94rem;
  border-radius: 0.2rem;
}
.style__monthBac__10SgC .style__veneue_area__2Laxq[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  border-radius: 0.08rem;
  width: 3.6rem;
  height: 0.64rem;
  padding: 0 0.2rem;
  border: 0.02rem solid #999;
  color: #999;
  margin-bottom: 0.16rem;
  background: #fff;
}
.style__monthBac__10SgC .style__veneue_area__2Laxq span[data-v-f2b5b3f8] {
  font-size: 0.24rem;
}
.style__monthBac__10SgC .style__veneue_area__2Laxq .style__triangle__CzGed[data-v-f2b5b3f8] {
  width: 0;
  height: 0;
  margin-top: 0.2rem;
  border: 0.12rem solid transparent;
  border-top-color: #333;
}
.style__monthBac__10SgC .style__veneue_area__2Laxq.style__kok_veneue__1EuYH[data-v-f2b5b3f8] {
  border-radius: 0.4rem;
}
.style__monthBac__10SgC .style__currentMonth__1scXn[data-v-f2b5b3f8] {
  height: 2.6rem;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.style__monthBac__10SgC .style__currentMonth__1scXn[data-v-f2b5b3f8],
.style__monthBac__10SgC .style__currentMonth__1scXn .style__rightSide__3s76W[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__rightSide__3s76W .style__birthdayCake__1rSus[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__rightSide__3s76W span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__rightSide__3s76W span[data-v-f2b5b3f8]:first-of-type {
  color: #fff;
  margin-bottom: 0.08rem;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__immediatelyGet__36-aI[data-v-f2b5b3f8] {
  text-align: center;
  display: inline-block;
  width: 1.88rem;
  height: 0.64rem;
  line-height: 0.64rem;
  border-radius: 0.32rem;
  font-size: 0.24rem;
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
  color: #fff;
  margin-right: 0.1rem;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__immediatelyGet__36-aI.style__disable__2xjk3[data-v-f2b5b3f8] {
  opacity: 0.4;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk p[data-v-f2b5b3f8] {
  font-family: PingFangSC-Medium, sans-serif;
  font-size: 0.28rem;
  color: #515151;
  margin-bottom: 0;
  font-weight: 400;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk p[data-v-f2b5b3f8]:first-child {
  margin-bottom: 0.2rem;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk p span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk .style__birthdayText__1KIvm[data-v-f2b5b3f8] {
  margin-left: 0.1rem;
  font-size: 0.28rem;
  color: #ede9e7;
  font-weight: 700;
}
.style__monthBac__10SgC .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk .style__birthdayCash__1MX6P[data-v-f2b5b3f8] {
  display: inline-block;
  font-size: 0.24rem;
  color: #3fa9ff;
}
.style__monthBac1__2VmlU[data-v-f2b5b3f8] {
  width: 6.94rem;
  margin: 0 auto;
  background-color: #f0f6fe;
  border-radius: 0.84rem;
  padding: 0 0.32rem;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  height: 1.68rem;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__rightSide__3s76W[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__rightSide__3s76W span[data-v-f2b5b3f8] {
  color: #ede9e7;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__rightSide__3s76W span[data-v-f2b5b3f8]:first-of-type {
  color: #fff;
  margin-bottom: 0.08rem;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__birthdayCake__1rSus[data-v-f2b5b3f8] {
  width: 1.8rem;
  height: 1.2rem;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__immediatelyGet__36-aI[data-v-f2b5b3f8] {
  display: inline-block;
  margin-left: 0.5rem;
  width: 1.36rem;
  height: 0.48rem;
  line-height: 0.48rem;
  border-radius: 0.24rem;
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
  text-align: center;
  font-size: 0.24rem;
  color: #fff;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__immediatelyGet__36-aI.style__disable__2xjk3[data-v-f2b5b3f8] {
  opacity: 0.4;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk p[data-v-f2b5b3f8] {
  font-family: PingFangSC-Medium, sans-serif;
  font-size: 0.28rem;
  color: #ede9e7;
  margin-bottom: 0;
  font-weight: 400;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk p[data-v-f2b5b3f8]:first-child {
  margin-bottom: 0.18rem;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk .style__birthdayText__1KIvm[data-v-f2b5b3f8] {
  font-size: 0.32rem;
  color: #ede9e7;
}
.style__monthBac1__2VmlU .style__currentMonth__1scXn .style__birthdayInfoArea__3SbEk .style__birthdayCash__1MX6P[data-v-f2b5b3f8] {
  display: inline-block;
  font-size: 0.24rem;
  color: #3fa9ff;
}
.month__mask__1nD8X[data-v-f2b5b3f8] {
  width: 100%;
  height: 100%;
  position: fixed;
  left: 0;
  top: 0;
  background: rgba(0, 0, 0, 0.5);
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  z-index: 100;
}
.month__mask__1nD8X .month__modalTable__2thPB[data-v-f2b5b3f8] {
  width: 6.9rem;
  height: 10rem;
  background: #fff;
  border-radius: 0.2rem;
  padding: 0.24rem 0.3rem 0;
  position: relative;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__close__2UZ5u[data-v-f2b5b3f8] {
  position: absolute;
  right: -0.06rem;
  top: -0.12rem;
  width: 0.5rem;
  height: 0.5rem;
  text-align: center;
  line-height: 0.5rem;
  font-size: 0.26rem;
  color: #fff;
  background: #333;
  border-radius: 0.5rem;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__title__3AaM0[data-v-f2b5b3f8] {
  font-size: 0.36rem;
  width: 100%;
  text-align: center;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  margin-top: 5px;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__firstLine__1RUu1 span[data-v-f2b5b3f8] {
  width: 1.26rem;
  height: 0.7rem;
  text-align: center;
  line-height: 0.7rem;
  border: 1px solid #ececf0;
  border-right: none;
  border-bottom: none;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__firstLine__1RUu1 span[data-v-f2b5b3f8]:last-of-type {
  border-bottom: 1px solid #ececf0;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__modal1__enowK span[data-v-f2b5b3f8],
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__modal2__2cTPm span[data-v-f2b5b3f8] {
  width: 2.52rem;
  height: 0.7rem;
  text-align: center;
  line-height: 0.7rem;
  border: 1px solid #ececf0;
  border-bottom: none;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__modal1__enowK span[data-v-f2b5b3f8]:last-of-type,
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__modal2__2cTPm span[data-v-f2b5b3f8]:last-of-type {
  border-bottom: 1px solid #ececf0;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u .month__modal1__enowK span[data-v-f2b5b3f8] {
  border-right: none;
}
.month__mask__1nD8X .month__modalTable__2thPB .month__modalContent__25k-u div[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
}
.month__tableHead__2sAFJ[data-v-f2b5b3f8] {
  width: 7.2rem;
  height: 0.8rem;
  text-align: center;
  line-height: 0.8rem;
  background: -webkit-gradient(linear, left top, left bottom, from(#d5ba9c), to(#cea472));
  background: -webkit-linear-gradient(#d5ba9c, #cea472);
  background: linear-gradient(#d5ba9c, #cea472);
  margin: 0 auto;
  color: #fff;
  font-size: 0.36rem;
}
.month__table__vcSJ7[data-v-f2b5b3f8] {
  width: 100%;
  background-color: #fff;
  overflow: hidden;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  text-align: center;
  -webkit-flex-wrap: nowrap;
  -ms-flex-wrap: nowrap;
  flex-wrap: nowrap;
}
.month__table__vcSJ7 > ul[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
}
.month__table__vcSJ7 > ul:first-child > li[data-v-f2b5b3f8] {
  color: #c1ab77 !important;
}
.month__table__vcSJ7 > ul > li[data-v-f2b5b3f8] {
  height: 0.9rem;
  line-height: 0.9rem;
  font-size: 0.24rem;
  color: #98a5b3;
  display: block;
  border: 1px solid hsla(0, 0%, 86.7%, 0.4);
  border-left: 0;
  border-top: 0;
  text-align: center;
  white-space: nowrap;
  padding: 0 5px;
}
.month__table__vcSJ7 > ul > li[data-v-f2b5b3f8]:first-child {
  color: rgba(51, 51, 51, 0.8);
}
.month__table__vcSJ7 > ul > li[data-v-f2b5b3f8]:nth-of-type(2n) {
  background: rgba(251, 251, 252, 0.53);
}
.month__table__vcSJ7 > ul > li span[data-v-f2b5b3f8] {
  display: block;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  white-space: nowrap;
}
.month__table__vcSJ7 .month__tableContent__q7JRn[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  overflow-x: auto;
}
.month__table__vcSJ7 .month__tableContent__q7JRn > ul[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
}
.month__table__vcSJ7 .month__tableContent__q7JRn > ul > li[data-v-f2b5b3f8] {
  height: 0.9rem;
  line-height: 0.9rem;
  font-size: 0.24rem;
  color: #98a5b3;
  display: block;
  border: 1px solid hsla(0, 0%, 86.7%, 0.4);
  border-left: 0;
  border-top: 0;
  text-align: center;
  white-space: nowrap;
  padding: 0 5px;
}
.month__table__vcSJ7 .month__tableContent__q7JRn > ul > li[data-v-f2b5b3f8]:first-child {
  color: rgba(51, 51, 51, 0.8);
}
.month__table__vcSJ7 .month__tableContent__q7JRn > ul > li[data-v-f2b5b3f8]:nth-of-type(2n) {
  background: rgba(251, 251, 252, 0.53);
}
.month__table__vcSJ7 .month__tableContent__q7JRn > ul > li span[data-v-f2b5b3f8] {
  display: block;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  white-space: nowrap;
}
.ProgressSVG[data-v-f2b5b3f8] {
  position: absolute;
  width: 80vw;
  left: 17vw;
}
#vip_month_pack .hint[data-v-f2b5b3f8] {
  color: #a5a9b3;
  text-align: left;
}
#vip_month_pack .normalList p[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  padding-left: 0.1rem;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
p[data-v-f2b5b3f8] {
  margin-bottom: 0;
}
.obAdvance__advance_container__1Pzeg[data-v-f2b5b3f8] {
  width: 100%;
  background: #fff;
  padding: 0.12rem 0 0;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4 .obAdvance__award_name__2M9lQ[data-v-f2b5b3f8] {
  width: 3.28rem;
  height: 3.14rem;
  background: rgba(107, 183, 238, 0.04);
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: start;
  -webkit-align-items: flex-start;
  -ms-flex-align: start;
  align-items: flex-start;
  padding-left: 0.34rem;
  border-radius: 0.12rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4 .obAdvance__award_name__2M9lQ p[data-v-f2b5b3f8]:first-child {
  margin-bottom: 0.16rem;
  padding-left: 0.12rem;
  font-size: 0.24rem;
  color: rgba(0, 0, 0, 0.85);
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4 .obAdvance__award_name__2M9lQ p[data-v-f2b5b3f8]:last-child {
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
  margin-left: -0.16rem;
  font-size: 0.24rem;
  color: #9aa7b5;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4 .obAdvance__award_desc__3-Sg8[data-v-f2b5b3f8] {
  width: 3.28rem;
  height: 3.14rem;
  padding: 0 0.48rem 0 0.32rem;
  background: rgba(107, 183, 238, 0.04);
  border-radius: 0.12rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4 .obAdvance__award_desc__3-Sg8 .obAdvance__item__13bBu[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_desc_container__15gN4 .obAdvance__award_desc__3-Sg8 .obAdvance__blue_text__2HddN[data-v-f2b5b3f8] {
  font-family: DIN-Bold sans-serif;
  font-weight: 700;
  font-size: 0.24rem;
  color: #ede9e7;
}
.obAdvance__advance_container__1Pzeg .obAdvance__disable_btn__20mY5[data-v-f2b5b3f8],
.obAdvance__advance_container__1Pzeg .obAdvance__liner_btn__3Sdin[data-v-f2b5b3f8] {
  width: 1.34rem;
  height: 0.48rem;
  line-height: 0.48rem;
  text-align: center;
  border-radius: 0.04rem;
  color: #fff;
  font-size: 0.2rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__disable_btn__20mY5 span[data-v-f2b5b3f8],
.obAdvance__advance_container__1Pzeg .obAdvance__liner_btn__3Sdin span[data-v-f2b5b3f8] {
  display: inline-block;
  -webkit-transform: scale(0.8);
  transform: scale(0.8);
}
.obAdvance__advance_container__1Pzeg .obAdvance__liner_btn__3Sdin[data-v-f2b5b3f8] {
  background: #ede9e7;
}
.obAdvance__advance_container__1Pzeg .obAdvance__disable_btn__20mY5[data-v-f2b5b3f8] {
  background: rgba(89, 126, 247, 0.5);
}
.obAdvance__advance_container__1Pzeg .obAdvance__step_text__1BNa-[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: #ede9e7;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_operate_container__166Vo[data-v-f2b5b3f8] {
  height: 0.98rem;
  margin: 0.12rem 0;
  padding: 0 0.48rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  background: rgba(107, 183, 238, 0.04);
  border-radius: 0.12rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_operate_container__166Vo .obAdvance__veneue_container__3pZnG[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  font-size: 0.24rem;
  color: rgba(0, 0, 0, 0.85);
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_operate_container__166Vo .obAdvance__veneue_area__1dxdi[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  width: 2.2rem;
  height: 0.48rem;
  padding: 0 0.16rem;
  border: 0.02rem solid #ccc;
  border-radius: 0.04rem;
  color: #9aa7b5;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_operate_container__166Vo .obAdvance__veneue_area__1dxdi span[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.obAdvance__advance_container__1Pzeg .obAdvance__award_operate_container__166Vo .obAdvance__veneue_area__1dxdi .obAdvance__triangle__3vaHP[data-v-f2b5b3f8] {
  width: 0.24rem;
  height: 0.24rem;
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYBAMAAAASWSDLAAAAJFBMVEUAAADNzc3Nzc3Pz8/V1dXOzs7MzMzNzc3Nzc3Nzc3Nzc3Nzc1yH+mMAAAAC3RSTlMAr+9KDCTUwHt6x01IPKkAAABOSURBVBjTYyAeMC5F4rBvMkBwWKR3Iklx7BZB4hltC0BwWLOVkaQcd09A4nVvdEBwmLULcHBmA5UhDGhANxrTUk4k57BI70D3AsJzJAEAi/MRX8cx5ogAAAAASUVORK5CYII=);
  background-repeat: no-repeat;
  background-size: 100% 100%;
}
.webpa .obAdvance__advance_container__1Pzeg .obAdvance__award_operate_container__166Vo .obAdvance__veneue_area__1dxdi .obAdvance__triangle__3vaHP[data-v-f2b5b3f8] {
  background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYBAMAAAASWSDLAAAAJFBMVEUAAADNzc3Nzc3Pz8/V1dXOzs7MzMzNzc3Nzc3Nzc3Nzc3Nzc1yH+mMAAAAC3RSTlMAr+9KDCTUwHt6x01IPKkAAABOSURBVBjTYyAeMC5F4rBvMkBwWKR3Iklx7BZB4hltC0BwWLOVkaQcd09A4nVvdEBwmLULcHBmA5UhDGhANxrTUk4k57BI70D3AsJzJAEAi/MRX8cx5ogAAAAASUVORK5CYII=);
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9[data-v-f2b5b3f8] {
  padding: 0.16rem 0 0.16rem 0.16rem;
  width: 100%;
  border-radius: 0.12rem;
  background: rgba(107, 183, 238, 0.04);
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt[data-v-f2b5b3f8] {
  height: 1.44rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: start;
  -webkit-align-items: flex-start;
  -ms-flex-align: start;
  align-items: flex-start;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt .obAdvance__gift_pic__3etQj[data-v-f2b5b3f8] {
  width: 1.48rem;
  height: 1.44rem;
  padding: 0.04rem;
  border-radius: 0.018rem;
  background: #f4f6f9;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt .obAdvance__gift_pic__3etQj img[data-v-f2b5b3f8] {
  width: 100%;
  height: 100%;
  background-color: #fff;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt .obAdvance__gift_desc__ip0Cp[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  margin-left: 0.16rem;
  height: 100%;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt .obAdvance__gift_desc__ip0Cp .obAdvance__gift_title__3tATc[data-v-f2b5b3f8] {
  margin-bottom: 0.08rem;
  font-size: 0.28rem;
  color: rgba(0, 0, 0, 0.85);
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt .obAdvance__gift_desc__ip0Cp .obAdvance__gift_name__2MIgU[data-v-f2b5b3f8] {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  line-height: normal;
  height: 0.94rem;
  overflow: hidden;
  word-break: break-all;
  text-overflow: ellipsis;
  line-height: 0.32rem;
  font-size: 0.24rem;
  color: #9aa7b5;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__gift_area__3eVbt .obAdvance__operate_area__19gaG[data-v-f2b5b3f8] {
  width: 2.28rem;
  height: 100%;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
  -ms-flex-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B[data-v-f2b5b3f8] {
  margin-top: 0.2rem;
  padding-right: 0.32rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2[data-v-f2b5b3f8] {
  margin-bottom: 0.2rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__percent_container__1rdk7[data-v-f2b5b3f8] {
  position: relative;
  margin-left: 1.5rem;
  height: 0.36rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__percent_container__1rdk7 .obAdvance__percent_num__33A8H[data-v-f2b5b3f8] {
  position: absolute;
  bottom: 0;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  font-family: DINPro-Bold sans-serif;
  font-weight: 500;
  font-size: 0.28rem;
  color: #ede9e7;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__percent_wrap__204Lx[data-v-f2b5b3f8] {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  -webkit-box-align: center;
  -webkit-align-items: center;
  -ms-flex-align: center;
  align-items: center;
  margin: 0.16rem 0;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__percent_wrap__204Lx p[data-v-f2b5b3f8] {
  width: 1.5rem;
  font-size: 0.28rem;
  color: #666;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__percent_wrap__204Lx .obAdvance__all_percent__DLdts[data-v-f2b5b3f8] {
  -webkit-box-flex: 1;
  -webkit-flex: 1;
  -ms-flex: 1;
  flex: 1;
  position: relative;
  height: 0.24rem;
  border-radius: 0.12rem;
  background: hsla(0, 0%, 60%, 0.2);
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__percent_wrap__204Lx .obAdvance__all_percent__DLdts .obAdvance__current_percent__2lIu4[data-v-f2b5b3f8] {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  border-radius: 0.12rem;
  background: -webkit-gradient(linear, left top, right top, from(#70b4ff), to(#3674ff));
  background: -webkit-linear-gradient(left, #70b4ff, #3674ff);
  background: linear-gradient(90deg, #70b4ff, #3674ff);
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__money_wrap__2b9E1[data-v-f2b5b3f8] {
  margin-left: 1.5rem;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -webkit-justify-content: space-between;
  -ms-flex-pack: justify;
  justify-content: space-between;
  line-height: 1.4;
  color: #666;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__money_wrap__2b9E1 .obAdvance__money_title__2UUab[data-v-f2b5b3f8] {
  font-size: 0.2rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__money_wrap__2b9E1 .obAdvance__current_money__2FHMH[data-v-f2b5b3f8] {
  font-family: DINPro-Medium sans-serif;
  font-size: 0.24rem;
  color: #ede9e7;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__progress_container__BcnY2 .obAdvance__money_wrap__2b9E1 .obAdvance__total_money__22aSM[data-v-f2b5b3f8] {
  font-size: 0.24rem;
}
.obAdvance__advance_container__1Pzeg .obAdvance__gift_operate_container__1x_M9 .obAdvance__progress_area__BKT3B .obAdvance__static_tips__10aeA[data-v-f2b5b3f8] {
  font-size: 0.24rem;
  color: #999;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz > h4 > span[data-v-f2b5b3f8][data-v-920a0086] {
  position: absolute;
  left: 11px;
  top: -1px;
  width: 16px;
  height: 16px;
  line-height: 16px;
  border-radius: 50%;
  background-color: #1890ff;
  text-align: center;
  color: #fff;
  font-size: 0.28rem;
}
.vipDetailStyle__vipDetail__2q9TP .vipDetailStyle__rule__2s4Rz h2 span[data-v-f2b5b3f8][data-v-920a0086] {
  display: inline-block;
  height: 30px;
  line-height: 30px;
  font-size: 16px;
  color: #cf866b;
  position: relative;
  font-weight: 400;
}
</style>
