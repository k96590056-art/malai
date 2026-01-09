<template>
  <div class="sdg" style="width: 100%; min-height: 100vh; background: rgb(237, 241, 255); padding-bottom: 50px" v-if="pay_way">
    <div style="width: 100%; background: #fff">
      <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="存款" left-arrow @click-left="$router.back()" />
      <div style="height: 46px"></div>
      <!-- 存款方式选择 -->
      <div class="typelis">
        <div :class="pay_way == 'bank' ? ' tyls atc' : 'tyls'" @click="changPayway('bank')" v-if="payWayList.card == 1"><img src="/static/image/icoOnlineTransfer2@3x.png" alt="" />网银转账</div>
        <div :class="pay_way == 'usdt' ? ' tyls atc' : 'tyls'" @click="changPayway('usdt')" v-if="payWayList.usdt == 1"><img src="/static/image/1595237922936176.png" alt="" />USDT</div>
        <div :class="pay_way == 'wechat' ? ' tyls atc' : 'tyls'" @click="changPayway('wechat')" v-if="payWayList.wechat == 1"><img src="/static/image/QuickWechat.png" alt="" />微信</div>
        <div :class="pay_way == 'alipay' ? ' tyls atc' : 'tyls'" @click="changPayway('alipay')" v-if="payWayList.alipay == 1"><img src="/static/image/icoAlipay2@3x.png" alt="" />支付宝</div>
      </div>
      <div v-if="pay_way == 'bank' && !(userbank.length == 0 && userUSD.length == 0)">
        <div class="usrse">
          <div class="bans" style="" v-for="(item, index) in cardLis" :key="index">
            <p>
              <span class="frists"> 收款账号 </span><span class="sdsw">{{ item.bank_no }}</span
              ><span class="copy" @click="doCopy(item.bank_no)"> 复制 </span>
            </p>
            <p>
              <span class="frists"> 银行户名 </span><span class="sdsw">{{ item.bank_owner }}</span
              ><span class="copy" @click="doCopy(item.bank_owner)"> 复制 </span>
            </p>
            <p>
              <span class="frists"> 开户行 </span><span class="sdsw">{{ item.bank_data.bank_name }}</span
              ><span class="copy" @click="doCopy(item.bank_data.bank_name)"> 复制 </span>
            </p>
            <p>
              <span class="frists"> 银行地址 </span><span class="sdsw">{{ item.bank_address }}</span
              ><span class="copy" @click="doCopy(item.bank_address)"> 复制 </span>
            </p>
          </div>
          <div class="hgs" @click="changShow">
            <div class="nams">开户银行</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field v-model="bankBox.bank" type="text" placeholder="选择开户银行" disabled> </van-field>
              </van-cell-group>
            </div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
          <div class="hgs">
            <div class="nams">存款人姓名</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field v-model="bankBox.bank_owner" type="text" placeholder="请输入存款人姓名"> </van-field>
              </van-cell-group>
            </div>
            <div class="lasthg">为及时到账，请务必输入正确的存款人姓名</div>
          </div>

          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
          <div class="hgs">
            <div class="nams">银行卡号</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field v-model="bankBox.bank_no" type="text" placeholder="请输入银行卡号"> </van-field>
              </van-cell-group>
            </div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
          <div class="hgs">
            <div class="nams">开户行地址</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field v-model="bankBox.bank_address" type="text" placeholder="请输入开户行地址"> </van-field>
              </van-cell-group>
            </div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
          <div class="hgs">
            <div class="nams">存款金额</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field label="￥" v-model="amount" type="text" :placeholder="`请输入取款金额 ${min_price} - ${max_price}`">
                  <template #button> <span style="color: #000"> 元</span> </template>
                </van-field>
              </van-cell-group>
            </div>
            <div class="lasthg"></div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
        </div>
      </div>
      <div v-if="pay_way == 'usdt'">
        <div class="tipsh">
          <div class="tops">USDT价格稳定 流通性高 不受监管 <span @click="$parent.goNav('/usdtmore')">了解更多 ></span></div>
          <div class="tsg">
            <div class="tsgs">绑定协议地址</div>
            <div class="tsgs">交易所划转</div>
            <div class="tsgs">完成取款</div>
          </div>
        </div>
        <div class="usrse">
          <div class="hgs">
            <div class="nams sc">
              钱包协议
              <div :class="meyXi == 'TRC20' ? ' ssa acti' : 'ssa'" @click="changXiyi('TRC20')">TRC20</div>
              <div :class="meyXi == 'ERC20' ? ' ssa acti' : 'ssa'" style="margin-left: 0.5rem" @click="changXiyi('ERC20')">ERC20</div>
            </div>
            <div style="border-bottom: 1px solid #f2f2f2"></div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
          <!-- <div class="hgs">
            <div class="nams">USDT地址</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <div data-v-a12ec382="" class="van-cell-group van-hairline--top-bottom">
                <div data-v-a12ec382="" class="van-cell van-field">
                  <div class="van-cell__value van-cell__value--alone van-field__value">
                    <div class="van-field__body"><input type="text" readonly onfocus="this.removeAttribute('readonly');" auto-complete="off" onblur="this.setAttribute('readonly',true);" placeholder="请输入USDT地址" class="van-field__control" /></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div> -->
          <div class="hgs">
            <div class="nams">存款金额</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field label="￥" v-model="amount" type="text" :placeholder="`请输入取款金额 ${min_price} - ${max_price}`">
                  <template #button> <span style="color: #000"> 元</span> </template>
                </van-field>
              </van-cell-group>
            </div>
            <div class="lasthg"><span style="color: red; font-size: 0.43rem; margin-right: 10px">≈ </span> {{ amount ? Math.floor((amount / $store.state.userInfo.usdtrate) * 100) / 100 : '0.00' }}USDT ; 参考汇率：{{ $store.state.userInfo.usdtrate }}</div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
          <div class="hgs">
            <div class="nams">温馨提示</div>
            <div class="lasthg" style="border-top: 1px solid #eee; margin-top: 10px">请选择正确的USDT协议付款，若您选择错误的协议付款，平台将无法收到您的付款，为此我们不承担任何负责！</div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
        </div>
      </div>
      <div v-if="pay_way == 'wechat' || pay_way == 'alipay'">
        <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>

        <div class="usrse">
          <div class="hgs">
            <div class="nams">存款金额</div>
            <div style="border-bottom: 1px solid #f2f2f2">
              <van-cell-group>
                <van-field label="￥" v-model="amount" type="text" :placeholder="`请输入取款金额 ${min_price} - ${max_price}`">
                  <template #button> <span style="color: #000"> 元</span> </template>
                </van-field>
              </van-cell-group>
            </div>
            <div class="lasthg"></div>
          </div>
          <div style="height: 0.2rem; background: #f8f8f8; width: 100wh"></div>
        </div>
      </div>
      <div style="margin: 0 auto; width: 86%">
        <van-button type="info" style="margin-top: 20px; width: 100%" @click="payTest">立即存款</van-button>
        <div class="textcns" style="text-align: center; color: #999; padding: 10px 0">存款遇到问题？联系 <span @click="$parent.openKefu" style="color: #cf866b; display: inline-block; margin: 0 6px">人工客服</span> 解决</div>
      </div>
    </div>
    <div v-if="show" style="position: fixed; width: 100%; height: 100%; top: 0; z-index: 999; background: rgba(0, 0, 0, 0.39)">
      <van-picker style="position: absolute; bottom: 0; left: 0; width: 100%" title="银行类型" show-toolbar :columns="banklist" @confirm="onConfirm" @cancel="onCancel" @change="onChange" value-key="bank_name" />
    </div>
    <!-- 禁止   -->
    <div class="domainModal_domainView__FWCzg" v-if="userbank.length == 0 && userUSD.length == 0">
      <div class="domainModal_mask__24Y2m domainModal_fadeIn__1I3AS false" @click="$router.back()"></div>
      <div class="domainModal_content__1nBgc" style="width: 80%">
        <div id="domain" class="domainModal_contentTop__2C4jc">
          <img src="/static/image/hongbaocolse.png" @click="$router.back()" style="position: absolute; top: 5px; right: 13px; width: 0.6rem" alt="" />

          <div class="domainModal_top__1omYS">温馨提示</div>
          <div class="domainModal_middle__3gQPm" style="padding: 30px">您还为绑定任何钱包卡片，请前往绑定！</div>

          <div style="height: 30px; text-align: center; line-height: 30px; color: #fff" @click="$parent.goNav('/wallet')">前往绑定</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'recharge',
  data() {
    return {
      pay_way: '',
      bankBox: {},
      payInfo: {},
      amount: null,
      cardLis: [],
      banklist: [],
      bankBox: {},
      meyXi: 'TRC20',
      payWayList: {},
      show: false,
      userbank: [],
      userUSD: [1],
      min_price: 100,
      max_price: 10000,
    };
  },
  created() {
    let that = this;
    that.getPayWay();
    that.getBanklist();
    that.getcard();
  },
  methods: {
    getPayRange() {
      let that = this;
      let type = null;

      if (that.pay_way == 'bank') {
        type = 'bank';
      }
      if (that.pay_way == 'wechat') {
        type = 'wechat';
      }
      if (that.pay_way == 'alipay') {
        type = 'alipay';
      }
      if (that.pay_way == 'alipay') {
        type = 'alipay';
      }
      if (that.pay_way == 'usdt') {
        if (that.meyXi == 'ERC20') {
          type = 'usdt-erc20';
        }
        if (that.meyXi == 'TRC20') {
          type = 'usdt-trc20';
        }
      }
      // bank/wechat/alipay/usdt-erc20/usdt-trc20/ebpay    min_price max_price
      that.showLoading();

      that.$apiFun
        .post('/api/getPayRange', { type })
        .then(res => {
          if (res.code == 200) {
            that.min_price = res.data.min_price;
            that.max_price = res.data.max_price;
          }
          that.hideLoading();
        })
        .catch(res => {
          that.hideLoading();
        });
    },
    changShow() {
      this.show = !this.show;
    },
    onConfirm(value, index) {
      this.bankBox.bank = value.bank_name;
      this.show = false;
    },
    onChange(picker, value, index) {},
    onCancel() {
      this.show = false;
    },
    changXiyi(val) {
      if (this.meyXi == val) {
        return;
      }
      this.meyXi = val;
      this.getPayRange();
    },
    getPayWay() {
      let that = this;
      that.showLoading();

      that.$apiFun
        .get('/api/get_pay_way', {})
        .then(res => {
          if (res.code == 200) {
            that.payWayList = res.data;
            that.payWayList.rengong = 1;
            let obj = that.payWayList;
            for (let i in obj) {
              if (obj[i] == 1) {
                that.pay_way = i == 'card' ? 'bank' : i;
                that.hideLoading();
                that.getPayRange();

                return;
              }
            }
          }
          that.hideLoading();
        })
        .catch(res => {
          that.hideLoading();
        });
    },
    payTest() {
      let that = this;
      let info = {};

      // bank 情况下  bank  bank_address  bank_no  bank_owner
      if (that.pay_way == 'bank') {
        info = {
          paytype: that.pay_way,
          amount: that.amount * 1,
          bank: that.bankBox.bank,
          bank_address: that.bankBox.bank_address,
          bank_no: that.bankBox.bank_no,
          bank_owner: that.bankBox.bank_owner,
        };
        console.log(info);
        // 银行卡信息内容的限制
        if (!info.bank_owner) {
          that.showTost(0, '请输入存款人姓名');
          return;
        }
        if (!info.bank) {
          that.showTost(0, '请输入银行类型');
          return;
        }

        if (!info.bank_no) {
          that.showTost(0, '请输入银行卡号');
          return;
        }
        if (!info.bank_address) {
          that.showTost(0, '请输入银行开户行地址');
          return;
        }
      } else {
        //暂时
        info = {
          paytype: that.pay_way,
          amount: that.amount * 1,
        };
      }
      if (that.pay_way == 'usdt') {
        info.catepay = that.meyXi;
      }
      // usdt 情况下  catepay 必填

      // 支付的金额判断
      if (info.amount < that.min_price || info.amount > that.max_price) {
        that.showTost(0, `请输入金额在${that.min_price}-${that.max_price}之间！`);
        return;
      }
      that.showLoading();
      info.paytype = info.paytype == 'wechat' ? 'wxpay' : info.paytype;

      that.$apiFun
        .post('/api/recharge', info)
        .then(res => {
          console.log(res);
          if (res.code != 200) {
            that.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.amount = null;
            if (that.pay_way == 'bank') {
              that.showTost(1, '提交成功，等待后台审核');
              that.bankBox = {};
              that.amount = null;
              that.hideLoading();
              that.$router.push({ path: '/transRecord' });
              return;
            }
            that.bankBox = {};
            that.amount = null;
            that.$router.push({ path: `/payInfo?deposit_no=${res.message}` });
          }
          that.hideLoading();
        })
        .catch(res => {
          that.hideLoading();
        });
    },
    changMey(val) {
      this.amount = val * 1;
    },
    getBanklist() {
      let that = this;
      that.$apiFun
        .post('/api/banklist', {})
        .then(res => {
          if (res.code != 200) {
            that.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.banklist = res.data;
          }
          that.hideLoading();
        })
        .catch(res => {
          that.hideLoading();
        });
    },
    getcard() {
      let that = this;
      that.showLoading();
      that.$apiFun
        .post('/api/getpaybank', {})
        .then(res => {
          if (res.code != 200) {
            that.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.cardLis = res.data;

            that.hideLoading();
          }
        })
        .catch(res => {
          that.hideLoading();
        });
    },
    changPayway(val) {
      let that = this;
      if (val == that.pay_way) {
        return;
      }
      that.pay_way = val;
      that.bankBox = {};
      that.payInfo = {};
      that.amount = null;
      that.getPayRange();
    },
    goNav(url) {
      let that = this;
      that.$parent.goNav(url);
    },
    doCopy(msg) {
      let cInput = document.createElement('input');
      cInput.style.opacity = '0';
      cInput.value = msg;
      document.body.appendChild(cInput);
      // 选取文本框内容
      cInput.select();

      // 执行浏览器复制命令
      // 复制命令会将当前选中的内容复制到剪切板中（这里就是创建的input标签）
      // Input要在正常的编辑状态下原生复制方法才会生效
      document.execCommand('copy');

      // 复制成功后再将构造的标签 移除
      this.showTost(1, '复制成功！');
    },
    getUserInfo() {
      let that = this;
      that.$parent.getUserInfo();
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
    getuseCardlist() {
      let that = this;
      that.$apiFun.post('/api/getcard', { type: 1 }).then(res => {
        if (res.code == 200) {
          that.userbank = res.data;
        }
      });
      that.$apiFun.post('/api/getcard', { type: 2 }).then(res => {
        if (res.code == 200) {
          that.userUSD = res.data;
        }
      });
    },
  },
  mounted() {
    let that = this;
  },
  updated() {
    let that = this;
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      console.log(vm);
      let that = this;
      vm.getuseCardlist();
    });
  },
};
</script>

<style lang="scss" scoped>
@import '../../static/css/2d87bbdbffeb4734e5c7.css';

.tipsh {
  width: 95%;
  margin: 6px auto;
  border-radius: 10px;
  background: #f8f8f8;
  box-sizing: border-box;
  padding: 6px;

  .tops {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.4rem;
    font-weight: 700;
    color: #333;
    height: 1rem;
    span {
      font-size: 0.29rem;
      font-weight: 400;
    }
  }
  .tsg {
    display: flex;
    align-items: center;
    justify-content: space-between;
    .tsgs {
      height: 0.56rem;
      line-height: 0.56rem;
      color: #a5a9b3;
      font-size: 0.2rem;
      text-align: center;
      padding: 4px 8px;
      flex: 1;
      background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAAA0CAYAAADPCHf8AAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAALCSURBVHgB7d0hcNtAEIXhtWh4y8vDixseXHPz4ibYKW2Kw1UsHt7g8vCWB7u7mYsnss7S3elmam3+b8bjxM6Evaz1tFJWu91uKyLnAixbt1qt7qSyRh+3Aizfpf6xv5TKGk3dX31uBVi+jYak6qehJjx3+ngSYPmuNCTvpZLngOgUsXBU//wG/Adn+thqSM6kgpcJYiG516ffAizfO31cSQXNwfc/BfDhXKfIRmbqBUSniE2QewF8sGZrLTM0kdfsWIQDdnix1pB8lEKDgIQD9k4AP75oSD5IgdgEsZDYeZE/AvhgjVZR/duMvPdDAD+s2fqaW/8eDUg4YKf2hSf2MSur2Wom3mdPC95c5DRbowFhTwtOWbN1kfKDUxPEsKcFjzYpzdZkQNjTglNJzVbKBGFPC15NNltJAQnY04JHo81WckDY04JjR5utnAli2NOCV+vYJbtZAWFPC84Nmq3cCWIsIOxpwavt62YrOyBhirCnBa96l+yWTBD2tODd/pLdlRTShNkv4QQiPOuKJgjwRjzNCcisa32BE/dgFw4WBSRsQiZtQwILZFvs3+2L0gnyWQCfLBzXoa3ND0g4JV/t1o7AibnRcOzP82UFJDRX1e+gDZyIOw3H4+sXcieITY8q9zwFTkyr4RisUSUHJEwPDszh0UO41dVAzgTZCuDPvrGKSQpIqHU5MIc3vcYqZjIg4aMVtS68sVD0GquYlAlirRXTA960h41VzGhAqHXhVLSxipmaIOxbwZvuWGMVczQg7FvBocfc/6U+NkE4MIcn1lh9k0zRgLBvBWessbqeaqxiBgEJB+afBPDjtiQcJjZBmB7wxBqrX1KoFxD2reBMVmMVczhB2LeCF9mNVcw+IOxbwZGixirmOSDsW8GR4sYq5mWCMD3gxU2tcJgmTA9WSuBBG+76WY1NEMIBD9q5jVXMPzyG0oj5jr9QAAAAAElFTkSuQmCC);
      background-size: cover;
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
[class*='van-hairline']:after {
  border: none;
}
.sc {
  display: flex;
  align-items: center;
  padding-bottom: 20px;
  .ssa {
    border-radius: 5px;
    border: 1px solid #f1f1f1;
    width: 2rem;
    height: 1rem;
    line-height: 1rem;
    font-size: 0.4rem;
    font-weight: 700;
    text-align: center;
    margin-left: 1rem;
  }
  .acti {
    color: #cf866b !important;
    border: 1px solid #cf866b !important;
  }
}
.typelis {
  width: 95%;
  margin: 6px auto;
  border-radius: 10px;
  box-sizing: border-box;
  padding: 6px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  .tyls {
    border-radius: 10px;
    border: 1px solid #eee;
    text-align: center;
    width: calc(25% - 0.2rem);
    padding: 0.2rem 0;
    font-size: 0.3rem;
    img {
      width: 0.7rem;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.2rem;
    }
  }
  .tyls.atc {
    border: 1px solid #cf866b;
    color: #cf866b;
  }
}
.lasthg {
  display: flex;
  align-items: center;
  font-size: 0.33rem;
  color: #a5a9b3;
  padding: 0.2rem 0;
}

.bans {
  background-image: linear-gradient(180deg, #fff, #f9fcff);
  width: 90%;
  margin: 0 auto;
  padding: 20px;
  border-radius: 20px;
  box-sizing: border-box;
  margin-bottom: 20px;
  p {
    display: flex;
    justify-content: space-between;
    align-items: center;
    .frists {
      width: 60px;
    }
    .sdsw {
      flex: 1;
    }
    .copy {
      color: #069b71;
    }
  }
}
</style>
