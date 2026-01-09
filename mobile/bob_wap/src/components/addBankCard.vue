<template>
  <div style="width: 100%; min-height: 100vh; background: rgb(237, 241, 255)">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="新增银行卡" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
    <div class="usrse">
      <div class="hgs">
        <div class="nams">持卡人姓名</div>
        <div>
          <div data-v-a12ec382="" class="van-cell-group van-hairline--top-bottom">
            <div data-v-a12ec382="" class="van-cell van-field">
              <div class="van-cell__value van-cell__value--alone van-field__value">
                <div class="van-field__body"><input type="text" readonly onfocus="this.removeAttribute('readonly');" auto-complete="off" onblur="this.setAttribute('readonly',true);" v-model="cardInfo.bank_owner" placeholder="请输入持卡人姓名" class="van-field__control" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="font-size: 0.24rem; color: #98a5b3; text-align: center; box-sizing: border-box; padding: 6px">为了您的资金能够迅速到账，请确保填写的姓名与银行卡的开户姓名一致</div>
    <div class="usrse">
      <div class="hgs" @click="changShow">
        <div class="nams">银行类型</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <div data-v-a12ec382="" class="van-cell-group van-hairline--top-bottom">
            <div data-v-a12ec382="" class="van-cell van-field">
              <div class="van-cell__value van-cell__value--alone van-field__value">
                <div class="van-field__body"><input type="text" readonly onfocus="this.removeAttribute('readonly');" auto-complete="off" onblur="this.setAttribute('readonly',true);" v-model="cardInfo.bank" disabled placeholder="请选择银行类型" class="van-field__control" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hgs">
        <div class="nams">银行卡号</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <div data-v-a12ec382="" class="van-cell-group van-hairline--top-bottom">
            <div data-v-a12ec382="" class="van-cell van-field">
              <div class="van-cell__value van-cell__value--alone van-field__value">
                <div class="van-field__body"><input type="text" readonly onfocus="this.removeAttribute('readonly');" auto-complete="off" onblur="this.setAttribute('readonly',true);" v-model="cardInfo.bank_no" placeholder="请输入银行卡号" class="van-field__control" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hgs">
        <div class="nams">开户行</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <div data-v-a12ec382="" class="van-cell-group van-hairline--top-bottom">
            <div data-v-a12ec382="" class="van-cell van-field">
              <div class="van-cell__value van-cell__value--alone van-field__value">
                <div class="van-field__body"><input type="text" readonly onfocus="this.removeAttribute('readonly');" auto-complete="off" onblur="this.setAttribute('readonly',true);" v-model="cardInfo.bank_address" placeholder="请输入开户行" class="van-field__control" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hgs">
        <div class="nams">支付密码</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="cardInfo.pay_pass" type="password" placeholder="请输入支付密码" />
          </van-cell-group>
        </div>
      </div>
      <van-button type="info" style="margin-top: 20px; width: 100%" @click="bindCard">确认添加</van-button>
      <div style="height: 60px"></div>
    </div>
    <div v-if="show" style="position: fixed; width: 100%; height: 100%; top: 0; z-index: 999; background: rgba(0, 0, 0, 0.39)">
      <van-picker style="position: absolute; bottom: 0; left: 0; width: 100%" title="银行类型" show-toolbar :columns="banklist" @confirm="onConfirm" @cancel="onCancel" @change="onChange" value-key="bank_name" />
    </div>
  </div>
</template>
<script>
export default {
  name: 'addBankCard',
  data() {
    return {
      cardInfo: {},
      banklist: [],
      show: false,
    };
  },
  created() {
    let that = this;
    that.getBanklist();
  },
  methods: {
    changShow() {
      this.show = !this.show;
    },
    onConfirm(value, index) {
      this.cardInfo.bank = value.bank_name;
      console.log(this.cardInfo.bank);
      this.show = false;
    },
    onChange(picker, value, index) {},
    onCancel() {
      this.show = false;
    },
    getBanklist() {
      let that = this;
      that.$parent.showLoading();

      that.$apiFun
        .post('/api/banklist', {})
        .then(res => {
          if (res.code != 200) {
            that.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.banklist = res.data;
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    bindCard() {
      let that = this;
      if (!that.cardInfo.bank_owner) {
        that.$parent.showTost(0, '请输入姓名');
        return;
      }
      if (!that.cardInfo.bank) {
        that.$parent.showTost(0, '请输入银行');
        return;
      }
      if (!that.cardInfo.bank_address) {
        that.$parent.showTost(0, '请输入开户行地址');
        return;
      }
      if (!that.cardInfo.bank_no) {
        that.$parent.showTost(0, '请输人银行卡号');
        return;
      }
      if (!that.cardInfo.pay_pass) {
        that.$parent.showTost(0, '请输人支付密码');
        return;
      }
      if (that.cardInfo.bank_no.length < 8) {
        that.$parent.showTost(0, '请输人正确的卡号长度');
        return;
      }
      if (that.cardInfo.pay_pass.length < 6 || that.cardInfo.pay_pass.length > 18) {
        that.$parent.showTost(0, '请输人支付密码长度');
        return;
      }
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/bindcard', that.cardInfo)
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.$parent.showTost(1, '绑定成功');
            that.$router.back();
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
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
};
</script>

<style lang="scss" scoped>
.usrse {
  background: #fff;
  box-sizing: border-box;
  padding: 6px 20px 0;
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
</style>
