<template>
  <div>
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed rounded-corners nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$parent.goNav('/')">
              <div class="home-top-login"><img src="/static/image/uacPoGJlb02AMGnUAAAYLvRuglw960.png" alt="" class="logo" /></div>
            </div>
            <div class="van-nav-bar__title van-ellipsis"></div>
            <!-- 已登录 -->
            <div v-if="$store.state.token" class="van-nav-bar__right">
              <div class="header-style-home">
                <div class="header-style-login">
                  <div class="home-money">
                    <div class="amount">{{ $store.state.userInfo.balance }}</div>

                    <svg @click="getBalances" style="width: 0.56rem; height: 0.56rem" aria-hidden="true" class="account-amount-refresh svg-icon svg-icon--mini-small svg-icon--refresh svg-icon--icon"><use xlink:href="#icon-refresh"></use></svg>
                  </div>
                  <div @click="transall" class="home-recharge startTheme-green" style="background: url('/static/image/uacPlmEAaMyAHbLrAAA6unmbQqs626.png'); min-width: 2rem; background-size: 100% 100% !important; height: 0.66rem; line-height: 0.66rem">一键回收</div>
                </div>
              </div>
            </div>
            <!-- 未登陆 -->
            <div v-else class="van-nav-bar__right">
              <div class="header-style-home">
                <div class="header-style-noLogin">
                  <div class="home-login" @click="$parent.goNav('/login')" style="background: url('/static/image/uacPlmEAaMyAOg0kAAAgsrNqG9M230.png')">登录</div>
                  <div class="home-register" @click="$parent.goNav('/login?type=1')" style="background: url('/static/image/uacPoGJhV7OAGmb4AAA6unmbQqs623.png')">注册</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div data-v-64c2bbbd="" class="bet-top-tab" style="margin-top: 40px">
      <div data-v-64c2bbbd="" class="van-tabs van-tabs--card">
        <div class="van-tabs__wrap">
          <div role="tablist" class="van-tabs__nav van-tabs__nav--card" style="border-color: rgb(255, 255, 255)">
            <div role="tab" aria-selected="true" class="van-tab van-tab--active" style="border-color: rgb(255, 255, 255); background-color: rgb(255, 255, 255)"><span class="van-tab__text van-tab__text--ellipsis">游戏平台</span></div>
          </div>
        </div>
        <div class="suwsf" style="position: absolute; top: 23px; left: 40%; display: flex; align-items: center">
          <span style="color: #fff; margin-right: 20px; font-size: 16px; font-weight: 700">免转模式</span>
          <van-switch @change="changeTasfer()" :active-value="1" :inactive-value="0" v-model="$store.state.userInfo.transferstatus" size="24px" />
        </div>
        <div class="van-tabs__content van-tabs__content--animated">
          <div class="van-tabs__track" style="transform: translate3d(0%, 0px, 0px); transition-duration: 0.3s">
            <div data-v-64c2bbbd="" role="tabpanel" class="van-tab__pane-wrapper"><div class="van-tab__pane"></div></div>
          </div>
        </div>
      </div>
      <div data-v-64c2bbbd="" class="customer-img"><img data-v-64c2bbbd="" src="/static/image/customer.9bf50982.png" /></div>
    </div>
    <div data-v-64c2bbbd="" class="main__content mp-60" style="box-sizing: border-box; padding: 0 29px">
      <div data-v-64c2bbbd="" class="main__list" v-for="(item, index) in balancelist" :key="index" @click="changVal(item)" v-if="item.platname != 'userbalance'" style="border-radius: 0px !important; background: var(--commonBg)">
        <div data-v-64c2bbbd="" class="list-item">
          <img data-v-64c2bbbd="" :src="`/static/image/transfer/${item.platname}.png`" class="game-img" />
          <div data-v-64c2bbbd="" class="item-info">
            <div data-v-64c2bbbd="" class="item-info-top">
              <div data-v-64c2bbbd="" class="info-name">
                <span data-v-64c2bbbd="">{{ item.name }}</span>
              </div>
              <div data-v-64c2bbbd="" class="info-amount">
                <span data-v-64c2bbbd="" class="">{{ item.balance }}</span>
              </div>
            </div>
            <div data-v-64c2bbbd="" class="item-info-bottom">
              <div data-v-64c2bbbd="" class="info-betMoney"><span data-v-64c2bbbd=""></span><span data-v-64c2bbbd=""></span></div>
              <div data-v-64c2bbbd="" class="info-betOrder"><span data-v-64c2bbbd=""></span> 额度转换</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 额度转换 -->
    <van-action-sheet v-model="openShow" title="额度转换">
      <div class="content">
        <van-form>
          <van-field v-model="$store.state.userInfo.balance" disabled label="平台账户余额" placeholder="平台账户余额" />
          <van-field v-model="openInfo.balance" disabled label="游戏账户余额" placeholder="游戏账户余额" />
          <van-field v-model="amount" name="操作金额" label="操作金额" placeholder="填写操作金额" />
          <van-field name="radio" label="转账方式">
            <template #input>
              <van-radio-group v-model="payType" direction="horizontal">
                <van-radio :name="0">转入</van-radio>
                <van-radio :name="1">转出</van-radio>
              </van-radio-group>
            </template>
          </van-field>
          <div style="margin: 16px">
            <van-button @click="isOk" round block type="info" native-type="submit">提交</van-button>
          </div>
        </van-form>
      </div>
    </van-action-sheet>
  </div>
</template>
<script>
export default {
  name: 'transfer',
  data() {
    return { nshow: true, balancelist: [], openInfo: {}, amount: null, payType: 0, openShow: false, daoTime: null };
  },
  created() {
    let that = this;
    that.getbalancelist();
    that.daoTime = setInterval(() => {
      that.getbalancelistNoLoding();
    }, 1500);
  },
  methods: {
    isOk() {
      let that = this;

      let sourcetype = '';
      let targettype = '';

      if (that.payType == 0) {
        //转入游戏
        sourcetype = 'userbalance';
        targettype = that.openInfo.platname;
      } else {
        //转入平台
        sourcetype = that.openInfo.platname;
        targettype = 'userbalance';
      }
      let info = { amount: that.amount, sourcetype: sourcetype, targettype: targettype };
      if (that.amount == null) {
        that.showTost(0, '请输入操作金额！');
        return;
      }
      that.closeCv();

      that.showLoading();
      that.$apiFun.post('/api/transfer', info).then(res => {
        that.showTost(1, res.message);

        if (res.code === 200) {
          that.refreshusermoney();
          that.getbalancelist();
        } else {
          that.hideLoading();
        }
      });
    },
    closeCv() {
      let that = this;
      that.openInfo = {};
      that.amount = null;
      that.openShow = false;
    },
    refreshusermoney() {
      let that = this;
      that.$apiFun.post('/api/refreshusermoney', {}).then(res => {
        that.hideLoading();
        if (res.code == 200) {
          localStorage.setItem('userInfo', JSON.stringify(res.data));
          that.$store.commit('changUserInfo');
        }
      });
    },
    getBalances() {
      let that = this;
      that.showLoading();
      that.getbalancelist();

      that.$apiFun
        .post('/api/balance', {})
        .then(res => {
          if (res.code == 200) {
            let userInfo = JSON.parse(localStorage.getItem('userInfo'));
            userInfo.balance = res.data.balance;
            localStorage.setItem('userInfo', JSON.stringify(userInfo));
            that.$store.commit('changUserInfo');
          }
          that.hideLoading();
        })
        .catch(res => {
          // console.log(res)
          that.hideLoading();
        });
    },
    changeTasfer() {
      let that = this;
      that.$parent.showLoading();
      let userInfo = JSON.parse(localStorage.getItem('userInfo'));
      let mianzhuan = userInfo.transferstatus ? 0 : 1;

      that.$apiFun
        .post('/api/uptransferstatus', { transferstatus: mianzhuan })
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            userInfo.transferstatus = mianzhuan;
            localStorage.setItem('userInfo', JSON.stringify(userInfo));
            that.$store.commit('changUserInfo');
            that.$parent.showTost(1, '操作成功！');
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    changVal(item) {
      let that = this;
      // $store.state.userInfo.transferstatus == 0  0  转账模式  1免转模式
      if (that.$store.state.userInfo.transferstatus == 1) {
        return;
      }
      that.openInfo = item;
      that.openShow = true;
      that.amount = null;
    },
    close() {
      that.openInfo = {};
      that.openShow = false;
      that.amount = null;
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
            balancelist.unshift({ platname: 'userbalance', name: '平台钱包' });
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
            balancelist.unshift({ platname: 'userbalance', name: '平台钱包' });
            that.balancelist = balancelist;
          }
        })
        .catch(res => {});
    },
    transall() {
      let that = this;
      that.showLoading();
      that.$apiFun
        .post('/api/transall', {})
        .then(res => {
          that.showTost(1, res.message);
          that.getbalancelist();
          that.refreshusermoney();
          that.hideLoading();
        })
        .catch(res => {
          that.hideLoading();
        });
    },
    showLoading() {
      this.$parent.showLoading();
    },
    hideLoading() {
      this.$parent.hideLoading();
    },
    openKefu() {
      this.$parent.openKefu();
    },
    showTost(type, title) {
      this.$parent.showTost(type, title);
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
@import '../../../static/css/chunk-0367d7af.20d89519.css';
</style>
