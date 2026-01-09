<template>
  <div style="background-color: #f8f8f8">
    <div style="min-height: 100vh; background: url('/static/image/bg_01.c00a1854e1446ef9fbd9f5b282da92f1.c00a1854.png') no-repeat; background-size: 100% auto; background-attachment: fixed">
      <img class="bancgs" style="opacity: 1" @click="$router.back()" src="/static/image/bank_020021515.png" alt="" />
      <div class="tit">我的钱包</div>
      <div class="mefs">
        <div class="conts" style="padding-top: 1.4rem">
          <div class="titsg">总资产（元）</div>
          <div class="mehs">
            <div class="lfs">￥</div>
            <div class="num">{{ $store.state.userInfo.balance * 1 + $store.state.userInfo.gameblance * 1 }}</div>
            <img @click="$parent.getUserInfoShowLoding()" class="shua" src="/static/image/icon_sx.88b45347bfcdb11586ef9a0872038bf9.png" alt="" />
          </div>
        </div>
      </div>
      <div class="bios">
        <div class="toptit">
          <div class="shu"></div>
          中心钱包（元）
        </div>
        <div class="mesg">
          <div class="bosgf">
            <div class="top"><img src="/static/image/qianbao123.png" alt="" />中心钱包</div>
            <div class="bots"><span>￥</span>{{ $store.state.userInfo.balance }}</div>
          </div>
          <div class="bosgf">
            <div class="top"><img src="/static/image/qianbao123.png" alt="" />游戏钱包</div>
            <div class="bots"><span>￥</span>{{ $store.state.userInfo.gameblance }}</div>
          </div>
        </div>
        <div class="typelist">
          <div class="lis" @click="$parent.goNav('/recharge')"><img src="/static/image/feature_moneydraw.ddbdd6cb1996bc0dccf6c8570d9e0183.ddbdd6cb.png" alt="" />存款</div>
          <div class="lis" @click="$parent.goNav('/transfer')"><img src="/static/image/feature_moneytransfer.5a83f20d17131faad2162df5435af5ca.5a83f20d.png" alt="" />转账</div>
          <div class="lis" @click="$parent.goNav('/withdrawal')"><img src="/static/image/feature_withdrawmoney.932feadcf30fa1646577e19f04412aaf.932feadc.png" alt="" />取款</div>
          <div class="lis" @click="$parent.goNav('/wallet')"><img src="/static/image/feature_bankcard.30833143844bfe739725bd4781495a2d.30833143.png" alt="" />卡片管理</div>
        </div>
        <div class="gamensg">
          <div class="titws">
            场馆余额
            <div class="btn" @click="transall">一键回收</div>
          </div>
          <div class="gameBox">
            <div class="lis" v-for="(item, index) in balancelist" :key="index">
              <div class="name">{{ item.name }}</div>
              <div class="nmey">{{ item.balance }}</div>
            </div>
          </div>
        </div>
        <div style="height: 1rem"></div>
      </div>
      <div style="height: 1rem"></div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'money',
  data() {
    return { daoTime: null, balancelist: [] };
  },
  created() {
    let that = this;
    that.getbalancelist();
    that.daoTime = setInterval(() => {
      that.getbalancelistNoLoding();
    }, 3500);
  },
  methods: {
    transall() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/transall', {})
        .then(res => {
          that.showTost(1, res.message);
          that.getbalancelist();
          that.refreshusermoney();
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    getbalancelist() {
      let that = this;
      that.$parent.showLoading();

      that.$apiFun
        .post('/api/balancelist', {})
        .then(res => {
          if (res.code !== 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code === 200) {
            that.balancelist = res.data;
            let balancelist = res.data;
            that.balancelist = balancelist;
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    getbalancelistNoLoding() {
      let that = this;

      that.$apiFun
        .post('/api/balancelist', {})
        .then(res => {
          if (res.code !== 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code === 200) {
            that.balancelist = res.data;
            let balancelist = res.data;
            that.balancelist = balancelist;
          }
        })
        .catch(res => {});
    },
    refreshusermoney() {
      let that = this;
      that.$apiFun.post('/api/refreshusermoney', {}).then(res => {
        that.$parent.hideLoading();
        if (res.code == 200) {
          localStorage.setItem('userInfo', JSON.stringify(res.data));
          that.$store.commit('changUserInfo');
        }
      });
    },
  },
  mounted() {
    let that = this;
  },
  updated() {},
  beforeDestroy() {
    let that = this;
    if (that.daoTime) {
      clearInterval(that.daoTime);
    }
    that.daoTime = null;
  },
};
</script>

<style lang="scss" scoped>
.tit {
  text-align: center;
  font-size: 0.5rem;
  font-weight: 700;
  height: 1.4rem;
  line-height: 1.4rem;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  color: #fff;
  background: url('/static/image/bg_01.c00a1854e1446ef9fbd9f5b282da92f1.c00a1854.png') no-repeat;
  background-size: 100% auto;
}
.mefs {
  color: #fff;
  height: 5.8rem;
  .conts {
    width: calc(100% - 80px);
    margin: 0 auto;
    padding-top: 0.6rem;
    .titsg {
      font-size: 0.5rem;
    }
    .mehs {
      display: flex;
      align-items: flex-end;
      height: 0.62rem;
      padding-top: 0.4rem;
      line-height: 0.62rem;

      .lfs {
        font-size: 0.5rem;
        padding-top: 6px;
        display: table-cell;
        vertical-align: bottom;
      }
      .num {
        font-size: 0.8rem;
        font-weight: 700;
        margin: 0 0.3rem 0 0.1rem;
      }
      .shua {
        width: 0.62rem;
      }
    }
  }
}
.bios {
  position: relative;
  width: calc(100% - 24px);
  margin: 0 auto;
  margin-top: -1.5rem;
  border-radius: 18px;
  background: #fff;
  overflow: hidden;
  .toptit {
    background: #f2f4fc;
    height: 1.2rem;
    display: flex;
    align-items: center;
    color: #383b43;
    font-size: 0.4rem;
    box-sizing: border-box;
    padding: 0 20px;
    .shu {
      margin-right: 15px;
      height: 0.5rem;
      width: 2px;
      background: #383b43;
    }
  }
}
.mesg {
  display: flex;
  align-items: center;
  height: 2rem;
  .bosgf {
    flex: 1;
    text-align: center;
    .top {
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 0.4rem;
      img {
        width: 0.5rem;
        margin-right: 0.1rem;
      }
    }
    .bots {
      margin-top: 0.1rem;
      font-size: 0.5rem;
      color: #597ef7;
      span {
        font-size: 0.23rem;
      }
    }
  }
}
.typelist {
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding-bottom: 0.5rem;
  .lis {
    width: 25%;
    font-size: 0.3rem;
    text-align: center;
    img {
      width: 70%;
      display: block;
      margin: 0 auto;
    }
  }
}

.gamensg {
  background: #fcfcff;
  box-sizing: border-box;
  width: calc(100% - 30px);
  margin: 0 auto;
  padding: 15px;
  .titws {
    display: flex;
    font-size: 0.4rem;
    .btn {
      border: 1px solid #4080ff;
      box-sizing: border-box;
      border-radius: 0.5rem;
      color: #4080ff;
      height: 0.5rem;
      font-size: 0.3rem;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 1.8rem;
      margin-left: 0.3rem;
    }
  }
  .gameBox {
    display: flex;
    flex-wrap: wrap;
    box-sizing: border-box;
    padding-top: 0.2rem;
    .lis {
      width: 25%;
      box-sizing: border-box;
      padding: 0.3rem 0;
      text-align: center;
      .name {
        color: #383b43;
        font-size: 0.3rem;
      }
      .nmey {
        color: #cbced8;
        font-size: 0.3rem;
        margin-top: 0.2rem;
      }
    }
  }
}
</style>
