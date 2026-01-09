<template>
  <div class="sdg sdgg" style="width: 100%; min-height: 100vh; background-color: #f1f1f1; padding-bottom: 50px">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7; z-index: 222" title="取款" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>

    <div class="tabVox">
      <div :class="activeName == 1 ? 'tab atc' : 'tab'" @click="changevT(1)">USDT取款</div>
      <div :class="activeName == 2 ? 'tab atc' : 'tab'" @click="changevT(2)">银行卡取款</div>
    </div>
    <div style="background: #fff; box-sizing: border-box; padding: 0 20px">
      <div class="qibao">
        <div class="fes">钱包金额</div>
        <div class="imgs"><img @click="$parent.getUserInfoShowLoding()" src="/static/image/iconRefresh.5b108ae65439270527aeee8ac17c2aca.png" alt="" /></div>
        <div class="btns" @click="transall">一键回收</div>
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

      <div class="gameBox" v-if="balancelist.length">
        <div class="lis" v-for="(item, index) in balancelist" v-if="index < showLis " :key="index">
          <div class="name">{{ item.name }}</div>
          <div class="num">{{ item.balance }}</div>
        </div>
        <div class="lis" v-if="showLis != 3" @click="changShowLis(3)">
          <div class="name">收起</div>
          <div class="num"><img src="/static/image/xiangshang.png" alt="" /></div>
        </div>
        <div class="lis" v-if="showLis == 3" @click="changShowLis(balancelist.length )">
          <div class="name">展开</div>
          <div class="num"><img src="/static/image/xiangxia.png" alt="" /></div>
        </div>
      </div>
    </div>
    <div class="usrse" v-if="activeName == 1">
      <div class="hgs" @click="$parent.goNav('/addUsdtCard')" v-if="usdssLis.length == 0">
        <div class="nams">选择USDT地址</div>
        <div style="color: #cf866b; height: 30px; line-height: 30px; text-align: center">+添加USDT地址</div>
      </div>
      <div class="hgs" v-else @click="changShow">
        <div class="nams">选择USDT地址</div>

        <div class="cardhgs" v-if="bankId">
          <img src="/static/image/1595237922936176.png" alt="" />
          <div>{{ hgInfo.bank_owner }} <span>****</span><span>****</span><span>****</span>{{ hgInfo.bank_no.substr(-4) }}</div>
        </div>
        <div v-else style="color: #cf866b; height: 30px; line-height: 30px; text-align: center">请选择USDT地址</div>
      </div>
         <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">打码量</div>
        <div>
          <van-cell-group>
            <van-field v-model="betAmount" type="text" disabled placeholder="打码量"> </van-field>
          </van-cell-group>
        </div>
      </div>
      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">取款金额</div>
        <div>
          <van-cell-group>
            <van-field v-model="amount" type="text" placeholder="请输入取款金额">
              <template #button> <van-button size="mini" @click="bigMey($store.state.userInfo.balance)" type="info">最大金额</van-button> </template>
            </van-field>
          </van-cell-group>
        </div>
      </div>

      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">支付密码</div>
        <div>
          <van-cell-group>
            <van-field v-model="password" type="password"  autocomplete="new-password"  placeholder="请输入支付密码"> </van-field>
          </van-cell-group>
        </div>
      </div>

      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs" v-if="chanmeyXi">
        <div class="nams">每笔手续费</div>
        <div>{{ chanmeyXi == 'ERC20' ? $store.state.userInfo.withdrawcashfee : $store.state.userInfo.withdrawfeeusdttrc }} USDT</div>
      </div>

      <div v-if="chanmeyXi" style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">折合USDT</div>
        <div style="padding-top: 5px">
          <span style="color: rgb(240, 80, 80)">≈ </span>{{ amount ? Math.floor((amount / $store.state.userInfo.withdrawusdtrate) * 100) / 100 : '0.00' }} SDT &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;参考汇率：{{ $store.state.userInfo.withdrawusdtrate }} 实时变化
        </div>
        <div class="lasthg" style="padding: 5px 0">实际到账：{{ amount ? Math.floor((amount / $store.state.userInfo.withdrawusdtrate) * 100) / 100 - (chanmeyXi == 'ERC20' ? $store.state.userInfo.withdrawcashfee * 1 : $store.state.userInfo.withdrawfeeusdttrc * 1) : '0.00' }}USDT</div>
      </div>

      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
    </div>
    <div class="usrse" v-if="activeName == 2">
      <div class="hgs" @click="$parent.goNav('/addBankCard')" v-if="usercardLis.length == 0">
        <div class="nams">选择银行卡</div>
        <div>
          <div style="color: #cf866b; height: 30px; line-height: 30px; text-align: center">+添加银行卡</div>
        </div>
      </div>
      <div class="hgs" v-else @click="changShow">
        <div class="nams">选择银行卡</div>
        <div class="cardhgs" v-if="bankId">
          <img :src="hgInfo.ico" alt="" />
          <div>{{ hgInfo.bank }} <span>{{ hgInfo.bank_owner }}</span><span>****</span>{{ hgInfo.bank_no.substr(-4) }}</div>
        </div>
        <div v-else style="color: #cf866b; height: 30px; line-height: 30px; text-align: center">请选择银行卡</div>

      </div>
         <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">打码量</div>
        <div>
          <van-cell-group>
            <van-field v-model="betAmount" type="text" disabled placeholder="打码量"> </van-field>
          </van-cell-group>
        </div>
      </div>
      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">取款金额</div>
        <div>
          <van-cell-group>
            <van-field v-model="amount" type="text" placeholder="请输入取款金额">
              <template #button> <van-button @click="bigMey($store.state.userInfo.balance)" size="mini" type="info">最大金额</van-button> </template>
            </van-field>
          </van-cell-group>
        </div>
      </div>

      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
      <div class="hgs">
        <div class="nams">支付密码</div>
        <div>
          <van-cell-group>
            <van-field v-model="password"  autocomplete="new-password"  type="password" placeholder="请输入支付密码"> </van-field>
          </van-cell-group>
        </div>
      </div>

      <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
    </div>
    <div style="margin: 0 auto; width: 86%" v-if="activeName == 2">
      <van-button type="info" style="margin-top: 20px; width: 100%" @click="withdraw">立即取款</van-button>
      <div class="textcns" style="text-align: center; color: #999; padding: 10px 0">取款遇到问题？联系 <span @click="$parent.openKefu" style="color: #cf866b; display: inline-block; margin: 0 6px">人工客服</span> 解决</div>
    </div>
    <div style="margin: 0 auto; width: 86%" v-if="activeName == 1">
      <van-button type="info" style="margin-top: 20px; width: 100%" @click="withdraw1">立即取款</van-button>
      <div class="textcns" style="text-align: center; color: #999; padding: 10px 0">取款遇到问题？联系 <span @click="$parent.openKefu" style="color: #cf866b; display: inline-block; margin: 0 6px">人工客服</span> 解决</div>
    </div>

    <!-- 弹出层  -->
    <van-popup v-model="show" position="bottom" v-if="activeName == 2 && show" :style="{ height: '70%', background: '#f8f8f8' }" class="card">
      <div class="poptit">请选择银行卡</div>

      <div class="lis" v-for="(item, index) in usercardLis" :key="index" @click="changApiType(item)">
        <img class="lefs" :src="item.ico" alt="" />
        <div class="cest">
          <div class="type">{{ item.bank }}</div>
          <div class="type">{{ item.bank_owner }}</div>
          <!-- <div class="type">{{ item.bank_address }}</div> -->
          <div class="num">
            <span>****</span><span>****</span><span>****</span><span>{{ item.bank_no.substr(-4) }}</span>
          </div>
        </div>
      </div>
    </van-popup>
    <van-popup v-model="show" position="bottom" v-if="activeName == 1 && show" :style="{ height: '70%', background: '#f8f8f8' }" class="card">
      <div class="poptit">请选择USDT地址</div>
      <div class="lis" v-for="(item, index) in usdssLis" :key="index" @click="changApiType(item)">
        <img class="lefs" src="/static/image/1595237922936176.png" alt="" />
        <div class="cest">
          <div class="type">{{ item.bank }}-{{ item.bank_owner }}</div>
          <div class="num">
            <span>****</span><span>****</span><span>****</span><span>{{ item.bank_no.substr(-4) }}</span>
          </div>
        </div>
      </div>
    </van-popup>
  </div>
</template>
<script>
export default {
  name: 'withdrawal',
  data() {
    return {
      usercardLis: [],
      usdssLis: [],
      amount: null,
      bankId: null,
      chanmeyXi: null,
      password: null,
      activeName: 1,
      daoTime: null,
      balancelist: [],
      showLis: 3,
      show: false,
      hgInfo: {},
      betAmount: null,

    };
  },
  created() {
    let that = this;
    that.getBetAmount();

    that.getUsercard();
    that.getUsdssList();
    that.getbalancelist();
    that.daoTime = setInterval(() => {
      that.getbalancelistNoLoding();
    }, 3500);
  },
  methods: {
        getBetAmount() {
      let that = this;
      that.$apiFun
        .post('/api/getBetAmount', {})
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.betAmount = res.data.bet_amount;
          }
        })
        .catch(res => {});
    },
    bigMey(val) {
      this.amount = val * 1;
    },
    changShow() {
      this.show = !this.show;
    },
    changShowLis(val) {
      this.showLis = val;
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
    changApiType(e) {
      let that = this;
      that.hgInfo = e;
      console.log(e);
      if (that.qutype == 1) {
        let chanmeyXi = null;
        that.usdssLis.forEach(el => {
          if (el.id == e) {
            chanmeyXi = el.bank_owner;
            return;
          }
        });
        that.chanmeyXi = chanmeyXi;
      } else {
        that.chanmeyXi = null;
      }
      that.bankId = e.id;
      that.password = null;
      that.amount = null;
      that.show = false;
    },
    changevT(type) {
      let that = this;
      console.log(type);
      if (type == that.activeName) {
        return;
      }
      that.hgInfo = {};
      that.activeName = type;
      that.amount = null;
      this.bankId = null;
      this.chanmeyXi = null;
      this.password = null;
    },
    withdraw() {
      let that = this;
      // amount	是	float	金额
      // bank	是	int	提现银行卡id
      let bank = that.bankId;
      let amount = that.amount;
      let password = that.password;
      if (!bank) {
        that.$parent.showTost(0, '请选择您要提现到的银行卡');
        return;
      }
      if (amount < 100) {
        that.$parent.showTost(0, '单笔取款不能低于100元');
        return;
      }

      if (!password) {
        that.$parent.showTost(0, '请输入您的支付密码');
        return;
      }
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/withdraw', { amount, bank, password })
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.$parent.showTost(1, '提交成功，等待后台审核');

            that.changevT();
            setTimeout(() => {
              that.$router.push({ path: '/transRecord' });
            }, 1500);
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    withdraw1() {
      let that = this;
      let bank = that.bankId;
      let amount = that.amount;
      let password = that.password;
      if (!bank) {
        that.$parent.showTost(0, '请选择USDT地址');
        return;
      }
      if (amount < 100) {
        that.$parent.showTost(0, '单笔取款不能低于100元');
        return;
      }

      if (!password) {
        that.$parent.showTost(0, '请输入您的支付密码');
        return;
      }
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/withdraw', { amount, bank, password })
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.$parent.showTost(1, '提交成功，等待后台审核');
            that.changevT();
            setTimeout(() => {
              that.$router.push({ path: '/transRecord' });
            }, 1500);
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },

    getUsercard() {
      let that = this;
      this.$parent.showLoading();

      that.$apiFun.post('/api/getcard', { type: 1 }).then(res => {
        if (res.code == 200) {
          that.usercardLis = res.data;
        }
        this.$parent.hideLoading();
      });
    },
    getUsdssList() {
      let that = this;
      this.$parent.showLoading();

      that.$apiFun.post('/api/getcard', { type: 2 }).then(res => {
        if (res.code == 200) {
          that.usdssLis = res.data;
        }
        this.$parent.hideLoading();
      });
    },
  },
  mounted() {
    let that = this;
  },
  updated() {
    let that = this;
  },
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
.tabVox {
  background: #f1f1f1;
  display: flex;
  justify-content: space-between;
  height: 1.2rem;
  align-items: center;
  padding-top: 5px;
  .tab {
    width: 50%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.4rem;
  }
  .tab.atc {
    background: #fff;
    border-radius: 10px 10px 0 0;
    color: #cf866b;
    position: relative;
  }
  .atc::after {
    content: ' ';
    position: absolute;
    bottom: 2px;
    width: 30px;
    height: 3px;
    left: calc(50% - 15px);
    z-index: 200;
    background: #1890ff;
    border-radius: 4px;
  }
}

.qibao {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 1.4rem;
  box-sizing: border-box;
  border-bottom: 1px solid #f8f8f8;
  .fes {
    font-size: 0.4rem;
    font-weight: 700;
  }
  .imgs {
    flex: 1;
    img {
      width: 0.4rem;
      margin-left: 0.1rem;
      display: flex;
      align-items: center;
    }
  }
  .btns {
    color: #697b8c;
    font-size: 0.28rem;
  }
}

.mesg {
  display: flex;
  align-items: center;
  height: 2rem;
  border-bottom: 1px solid #f8f8f8;

  .bosgf {
    flex: 1;
    text-align: center;
    .top {
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 0.4rem;
      img {
        width: 0.4rem;
        margin-right: 0.1rem;
      }
    }
    .bots {
      margin-top: 0.1rem;
      font-size: 0.5rem;
      color: #158bf4;
      span {
        font-size: 0.23rem;
      }
    }
  }
}

.gameBox {
  display: flex;
  flex-wrap: wrap;
  .lis {
    width: 25%;
    text-align: center;
    box-sizing: border-box;
    padding-top: 0.4rem;
    .name {
      font-size: 0.23rem;
      color: #383b43;
      line-height: 1;
      overflow: hidden; //超出隐藏
      text-overflow: ellipsis; //显示省略号
      white-space: nowrap; //强制不换行
    }
    .num {
      font-size: 0.23rem;
      color: #cbced8;
      margin-top: 0.2rem;
    }
    img {
      width: 0.32rem;
    }
  }
}

.usrse {
  background: #fff;
  box-sizing: border-box;
  padding-top: 5px;
  .hgs {
    width: calc(100% - 40px);
    margin: 0 auto;
  }
  .nams {
    font-size: 0.38rem;
    color: #000;
    vertical-align: middle;
    margin-top: 10px;
    font-weight: 700;
  }
  .imgsa {
    position: relative;
    height: 2rem;
    border-bottom: 1px solid #f2f2f2;
    padding-bottom: 0.2rem;
    .bisn {
      width: 0.8rem;
      position: absolute;
      bottom: 0.3rem;
      left: 1.4rem;
    }
    img {
      width: 2rem;
      border-radius: 50%;
    }
  }
}

.card {
  .lis {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 95%;
    margin: 0 auto;
    margin-top: 20px;
    box-sizing: border-box;
    padding: 10px 25px;
    min-height: 80px;
    border-radius: 10px;
    position: relative;
    border-radius: 0.16rem;
    background-color: #f8f9ff;
    -webkit-box-shadow: 0 0.04rem 0.2rem rgb(199 212 255 / 42%);
    box-shadow: 0 0.04rem 0.2rem rgb(199 212 255 / 42%);
    border: 0.02rem solid #fff;
    .lefs {
      width: 1.5rem;
    }
    .cest {
      flex: 1;
      margin: 0 20px;
      .type {
        font-size: 0.4rem;
        font-weight: 700;
        color: #98a8c5;
        margin-top: 6px;
      }
      .num {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 6px;
        color: #98a8c5;
        span {
          font-size: 0.6rem;
        }
      }
    }
    .rigss {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 0.6rem;
    }
  }
}

.poptit {
  height: 1.4rem;
  font-size: 0.4rem;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #fff;
  position: sticky;
  top: 0;
  z-index: 22;
}

.cardhgs {
  display: flex;
  align-items: center;
  color: #cf866b;
  font-size: 0.23rem;
  height: 1.2rem;
  img {
    width: 0.8rem;
    margin: 0 1rem;
  }
  span {
    display: inline-block;
    margin: 0 4px;
    font-size: 0.23rem;
  }
}
</style>
