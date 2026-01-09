<template>
  <div class="mine-container">
    <!-- title -->
    <div style="padding: 0 18px">
      <div class="mineTop">
        我的
        <div class="kefus" @click="$parent.openKefu()"><img src="/static/image/iconKefu.da12a10e52caa3da81e8cbe383247f67.png" /><span>客服</span></div>
      </div>
      <!-- user -->
      <div class="usersg">
        <div class="lesf">
          <img :src="$store.state.userInfo.avatar ? $store.state.userInfo.avatar : '/static/image/imageAvatar02@3x.png'" alt="" />
          <input class="inputsw" type="file" @change="onchangemd" single accept="image/gif,image/png" />
        </div>
        <div class="center" @click="$parent.goNav('/userInfo')">
          <div class="name">
            {{ $store.state.userInfo.username }}
            <img :src="`/static/style/vip${$store.state.userInfo.vip}.png`" alt="" />
          </div>
          <div class="dat">加入{{ $store.state.appInfo.title }}第{{ $store.state.userInfo.joinday }}天</div>
        </div>
        <img class="rig" @click="$parent.goNav('/userInfo')" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
      <div class="meys">
        <div class="lefs">
          <img src="/static/image/wallet.png" alt="" />
          <span>中心钱包</span>
          <div class="imsg" @click="$parent.getUserInfoShowLoding()">
            <img src="/static/style/refresh_2.98852cef4dfc05494e3f32a99e17d124.png" />
          </div>
        </div>
        <div class="meysf">
          <span>￥</span>
          {{ $store.state.userInfo.balance }}
        </div>
      </div>
      <div class="vipousf">
        <div class="box" @click="$parent.goNav('/vip')">
          <img src="/static/image/vipnew.png" alt="" />
          <div>
            <div class="nsgs">VIP特权</div>
            <div class="nsgsss">VIP PRIVILEGE</div>
          </div>
        </div>
        <div class="shu"></div>
        <div class="box" @click="$parent.goNav('/activity')">
          <img src="/static/image/giftnew.png" alt="" />
          <div>
            <div class="nsgs">豪礼赠送</div>
            <div class="nsgsss">SPREE ACTIVITY</div>
          </div>
        </div>
      </div>
    </div>
    <div class="uslis">
      <div style="box-sizing: border-box; padding: 0 12px">
        <div style="height: 24px"></div>
        <div class="thbs">
          <div class="lis" @click="$parent.goNav('/money')">
            <img src="/static/image/walletnew.png" alt="" />
            <span>我的钱包</span>
          </div>
          <div class="lis" @click="$parent.goNav('/transRecord')">
            <img src="/static/image/transactionnew.png" alt="" />
            <span>交易记录</span>
          </div>
          <div class="lis" @click="$parent.goNav('/betRecord')">
            <img src="/static/image/betnew.png" alt="" />
            <span>投注记录</span>
          </div>
          <div class="lis" @click="$parent.goNav('/userCent')">
            <img src="/static/image/settingnew.png" alt="" />
            <span>账户设置</span>
          </div>
        </div>
        <div class="bosfs">
          <div class="hgsw" @click="$parent.goNav('/message')">
            <img class="firsimg" src="/static/image/ob@2x.png" alt="" />
            <span class="tit">消息中心</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>

          <div class="hgsw" @click="$parent.goNav('/fanshui')">
            <img class="firsimg" src="/static/image/haoyou.svg" alt="" />
            <span class="tit">返水中心</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
          <div class="hgsw" @click="$parent.goNav('/activityRecord')">
            <img class="firsimg" src="/static/image/help.png" alt="" />
            <span class="tit">活动记录</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
          <div class="hgsw" @click="$parent.goNav('/welfare')">
            <img class="firsimg" src="/static/image/haoyou.svg" alt="" />
            <span class="tit">福利中心</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
          <div class="hgsw" v-if="$store.state.userInfo.isagent == 0" @click="$parent.goNav('/applyagent')">
            <img class="firsimg" src="/static/image/join.png" alt="" />
            <span class="tit">合营计划</span>
            <span class="tisf">加入我们，共赢财富</span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
          <div class="hgsw" v-if="$store.state.userInfo.isagent == 1" @click="$parent.goNav('/gamePage?dailiD=1')">
            <img class="firsimg" src="/static/image/join.png" alt="" />
            <span class="tit">代理登录</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
          <div class="hgsw" @click="$parent.goNav('/app')">
            <img class="firsimg" src="/static/image/appxiazaus.png" alt="" />
            <span class="tit">app下载</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
          <div class="hgsw" @click="$parent.goNav('/boutBallBet')">
            <img class="firsimg" src="/static/image/help.png" alt="" />
            <span class="tit">帮助中心</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>

          <div class="hgsw" style="border: none" @click="$parent.outLogin">
            <img class="firsimg" style="width: 18px; margin-left: 3px; margin-right: 3px" src="/static/image/close151.png" alt="" />
            <span class="tit">退出系统</span>
            <span class="tisf"></span>
            <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'mine',
  data() {
    return {
      activitylistList: [],
      bfNum: 0,
      vipLis: [],
    };
  },
  created() {
    let that = this;
    that.activitylist();
    that.uservip();
  },
  methods: {
    onchangemd(e) {
      let that = this;

      console.log(e.target.files); //这个就是选中文件信息
      let formdata = new FormData();
      Array.from(e.target.files).map(item => {
        console.log(item);
        formdata.append('file', item); //将每一个文件图片都加进formdata
      });
      //  axios.post("接口地址", formdata).then(res => { console.log(res) })
      that.$parent.showLoading();

      that.$apiFun.post('/api/uploadimg', formdata).then(res => {
        that.$parent.hideLoading();

        that.$parent.getUserInfoShowLoding();
      });
    },
    uservip() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/uservip', {})
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.vipLis = res.data;
            that.getbfNum();
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
      console.log(111);
    },
    transall() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/transall', {})
        .then(res => {
          that.$parent.showTost(0, res.message);
          that.$parent.getUserInfoShowLoding();
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
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
.mine-container {
  width: 100%;
  min-height: 100vh;
  box-sizing: border-box;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
}
// @import '../../../static/css/chunk-4deac3d0.11461b5f.css';
.mineTop {
  height: 60px;
  line-height: 60px;
  box-sizing: border-box;
  font-size: 16px;
  color: #5d75a2;
  text-align: center;
  position: relative;
  .kefus {
    position: absolute;
    top: 0;
    right: 0px;
    display: flex;
    height: 56px;
    align-items: center;
    font-size: 12px;

    img {
      width: 20px;
    }
    span {
      padding-top: 6px;
    }
  }
}

.usersg {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #5d75a2;
  .lesf {
    width: 60px;
    position: relative;
    margin-right: 20px;
    img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
    }
  }
  .center {
    flex: 1;
    .name {
      font-size: 16px;
      font-weight: 700;
      img {
        width: 30px;
        margin-left: 10px;
      }
    }
    .dat {
      font-size: 12px;
      margin-top: 8px;
      color: #a2aec8;
    }
  }
  .rig {
    width: 10px;
  }
}

.meys {
  display: flex;

  align-items: center;
  justify-content: space-between;
  margin: 0 auto;
  margin-top: 10px;
  padding: 0 12px;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  width: 94%;
  height: 36px;
  background-image: url(/static/style/walletbg.27f1624304433214845615bf9bc121e3.png);
  background-repeat: no-repeat;
  background-position: top;
  background-size: 100% 100%;
  color: #fff;
  .lefs {
    display: flex;
    align-items: center;
    font-size: 12px;
    img {
      width: 18px;
    }
    span {
      padding: 0 6px;
    }
    .imsg {
      background: url(/static/image/refresh_1.png) no-repeat;
      background-size: 100% 100%;
      width: 14px;
      height: 14px;
      display: flex;
      justify-content: center;
      align-items: center;
      img {
        width: 6px;
      }
    }
  }
  .meysf {
    font-size: 16px;
    font-weight: 700;
    span {
      font-size: 8px;
      font-weight: 100;
    }
  }
}

.vipousf {
  position: relative;
  display: flex;
  justify-content: space-between;
  background: url(/static/image/itembg01.9f4ff0731e793011f46663dbfdbfd903.9f4ff073.png) no-repeat;
  background-size: 100% 100%;
  margin: 0 auto;
  border-radius: 4px;
  width: 100%;
  height: 80px;
  -webkit-box-shadow: 0 0.04rem 0.2rem 0 rgb(93 114 162 / 11%);
  box-shadow: 0 0.04rem 0.2rem 0 rgb(93 114 162 / 11%);
  box-sizing: border-box;
  padding-top: 5px;
  padding-bottom: 10px;
  .box {
    display: flex;
    flex: 1;
    justify-content: center;
    align-items: center;
    img {
      width: 38px;
      margin-right: 10px;
    }
    .nsgs {
      font-weight: 600;
      font-size: 14px;
      color: #5d75a2;
    }
    .nsgsss {
      color: #a5a9b3;
      font-size: 8px;
      margin-top: 6px;
    }
  }
  .shu {
    width: 1px;
    display: block;
    background: rgba(170, 184, 209, 0.2);
    height: 100%;
  }
}
.uslis {
  background: rgba(0, 0, 0, 0.5);
  width: 100%;
  margin-top: -20px;
  z-index: 200;
  position: relative;
  padding-bottom: 98px;
  .thbs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-sizing: border-box;
    padding: 0 16px;
    .lis {
      img {
        display: block;
        margin: 0 auto;
        width: 44px;
      }
      span {
        display: block;
        text-align: center;
        font-size: 12px;
        color: #5d75a2;
      }
    }
  }
  .bosfs {
    margin-top: 15px;
    border-radius: 15px;
    padding: 4px 12px;
    box-sizing: border-box;
    width: 100%;
    background: rgba(0, 0, 0, 0.45);
    -webkit-box-shadow: 0 0.04rem 0.2rem 0 rgb(93 114 162 / 11%);
    box-shadow: 0 0.04rem 0.2rem 0 rgb(93 114 162 / 11%);
      .hgsw {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 40px;
        box-sizing: border-box;
        border-bottom: 1px solid rgba(24, 144, 255, 0.3);
      .firsimg {
        width: 24px;
      }
      .tit {
        color: rgba(255, 255, 255, 0.9);
        margin-left: 10px;
      }
      .tisf {
        flex: 1;
        margin: 0 12px;
        color: rgba(255, 255, 255, 0.7);
        text-align: right;
      }
      .rigiong {
        width: 6px;
      }
    }
  }
}
</style>
