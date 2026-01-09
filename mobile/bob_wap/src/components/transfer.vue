<template>
  <div class="sdg sdgg stddss" style="width: 100%; min-height: 100vh; background-color: #f1f1f1; padding-bottom: 50px">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7; z-index: 222" title="转账" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
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
        <div class="lis" v-for="(item, index) in balancelist" v-if="index < showLis && index != 0" :key="index">
          <div class="name">{{ item.name }}</div>
          <div class="num">{{ item.balance }}</div>
        </div>
        <div class="lis" v-if="showLis != 4" @click="changShowLis(4)">
          <div class="name">收起</div>
          <div class="num"><img src="/static/image/xiangshang.png" alt="" /></div>
        </div>
        <div class="lis" v-if="showLis == 4" @click="changShowLis(balancelist.length )">
          <div class="name">展开</div>
          <div class="num"><img src="/static/image/xiangxia.png" alt="" /></div>
        </div>
      </div>
    </div>
    <div class="mianzhuan">
      <div class="lfs">自动免转</div>
      <div class="ces">开启后余额自动转入游戏场馆</div>
      <van-switch @change="changeTasfer()" :active-value="1" :inactive-value="0" v-model="$store.state.userInfo.transferstatus" size="24px" />
    </div>
    <!-- 转账 -->
    <div class="zhuanzang" v-if="$store.state.userInfo.transferstatus == 0">
      <div class="tit">
        <div class="lesg" @click="changShow('sourcetype')">{{ sourcetype.name }} <span>></span></div>
        <img src="/static/image/iconTransfer.png" alt="" />
        <div class="lesg" @click="changShow('targettype')">{{ targettype.name }} <span>></span></div>
      </div>
      <div style="padding: 0.2rem 0; font-size: 0.24rem; color: #a5a9b3">场馆内钱包不支持互转</div>
      <van-cell-group>
        <van-field label="￥" v-model="amount" type="text" placeholder="请输入转账金额">
          <template #button>
            <van-button @click="bigMey" size="mini" type="info">最大金额</van-button>
          </template>
        </van-field>
      </van-cell-group>
    </div>
    <div style="margin: 0 auto; width: 86%">
      <van-button type="info" style="margin-top: 20px; width: 100%" @click="btnOk">立即转账</van-button>
      <div class="textcns" style="text-align: center; color: #999; padding: 10px 0">转账遇到问题？联系 <span @click="$parent.openKefu" style="color: ##158bf4; display: inline-block; margin: 0 6px">人工客服</span> 解决</div>
    </div>
    <van-popup v-model="show" position="bottom" v-if="type == 'sourcetype' && show" :style="{ height: '70%', background: '#f8f8f8' }" class="card">
      <div class="poptit">选择钱包</div>
      <div style="height: 0.2rem"></div>
      <div style="background: #fff; width: 100%; margin: 0 auto; padding: 0 20px; box-sizing: border-box">
        <div class="lis" v-for="(item, index) in balancelist" :key="index" @click="changApiType('sourcetype', item)">
          <div>{{ item.name }}</div>
          <img v-if="sourcetype.name == item.name" src="/static/image/icon_chose.28d0a1732f077f8062a64082a086ebf2.png" alt="" />
        </div>
      </div>
    </van-popup>
    <van-popup v-model="show" position="bottom" v-if="type == 'targettype' && show" :style="{ height: '70%', background: '#f8f8f8' }" class="card">
      <div class="poptit">选择钱包</div>
      <div style="height: 0.2rem"></div>
      <div style="background: #fff; width: 100%; margin: 0 auto; padding: 0 20px; box-sizing: border-box">
        <div class="lis" v-for="(item, index) in balancelist" :key="index" @click="changApiType('targettype', item)">
          <div>{{ item.name }}</div>
          <img v-if="targettype.name == item.name" src="/static/image/icon_chose.28d0a1732f077f8062a64082a086ebf2.png" alt="" />
        </div>
      </div>
    </van-popup>
  </div>
</template>
<script>
export default {
  name: 'transfer',
  data() {
    return { selecttype:"",
        nshow: true, balancelist: [], openInfo: {}, amount: null, payType: 0, openShow: false, daoTime: null, showLis: 4, show: false, type: 'sourcetype', sourcetype: { platname: 'userbalance', name: '平台钱包' }, targettype: {} };
  },
  created() {
    let that = this;
    if(this.$route.query.code)
        that.selecttype = this.$route.query.code;
    that.getbalancelist();
    that.daoTime = setInterval(() => {
      that.getbalancelistNoLoding();
    }, 1500);
  },
  methods: {
    bigMey(){
      let that = this;
      if(that.sourcetype.platname == 'userbalance'){
        that.amount = that.$store.state.userInfo.balance
      }else{
        that.amount = that.sourcetype.balance*1

      }
    },
    changShow(name) {
      this.type = name;
      this.show = true;
   
    },
    changApiType(name, val) {
      let that = this;
      that[name] = val;
      that.show = false;
      if(name == 'sourcetype'){
        that.amount = null;

      }
    },
    changShowLis(val) {
      this.showLis = val;
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
    btnOk() {
      let that = this;
      if (that.amount == null || that.amount == '') {
        that.showTost(0, '请输入操作金额！');
        return;
      }
      if(that.sourcetype.platname != 'userbalance' && that.targettype.platname !='userbalance'){
        that.showTost(0, '场馆内钱包不支持互转');

        return
      }
      let info = { amount: that.amount, sourcetype: that.sourcetype.platname, targettype: that.targettype.platname };

      that.showLoading();
      that.$apiFun.post('/api/transfer', info).then(res => {
        that.showTost(1, res.message);

        if (res.code === 200) {
          that.refreshusermoney();
          that.getbalancelist();
        } else {
          that.hideLoading();
        }
      }) .catch(res => {
          that.$parent.hideLoading();
        });
    },
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
            that.targettype = balancelist[1];
            balancelist.forEach(function(v,i){
                if(that.selecttype && that.selecttype == v.platname)
                    that.targettype = v;
            })
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
    color: #158bf4;
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
    background: #158bf4;
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
  padding-bottom: 10px;

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
    width: 100%;
    margin: 0 auto;
    box-sizing: border-box;
    height: 1.2rem;
    border-bottom: 1px solid #f8f8f8;
    img {
      width: 0.4rem;
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
  color: #158bf4;
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

.mianzhuan {
  display: flex;
  height: 2rem;
  align-items: center;
  margin: 14px auto;
  box-sizing: border-box;
  padding: 0 20px;
  background: #fff;

  .lfs {
    flex: 1;
    font-weight: 700;
    font-size: 0.4rem;
  }

  .ces {
    color: #a5a9b3;
    padding-right: 10px;
    font-size: 0.3rem;
  }
}

.zhuanzang {
  background: #fff;
  box-sizing: border-box;
  padding: 0 20px;
  .tit {
    display: flex;
    align-items: center;
    height: 1.2rem;
    border-bottom: 1px solid #f8f8f8;
    img {
      width: 0.6rem;
    }
    .lesg {
      width: calc(50% - 0.6rem);
      display: flex;
      justify-content: center;
      font-size: 0.4rem;
      font-weight: 700;
      justify-content: center;
      span {
        color: #a5a9b3;
        font-size: 0.4rem;
        padding-left: 10px;
      }
    }
  }
}
</style>
