<template>
  <div data-v-f531b812="" class="app app-ti_green metransRecord">
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed fixed-top rounded-corners nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">充值信息</div>
            <!-- <div class="van-nav-bar__right" @click="$parent.openKefu">
              <div class="header-style-icon"><img src="/static/image/kefuIcon.9bf50982.png" /></div>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  <div style="height:40px"></div>
    <div data-v-334775a8="" data-v-f531b812="" class="wrapper green-wrapper">
      <div data-v-334775a8="">
        <div data-v-334775a8="" class="PayInfoTime green-PayInfoTime">
          <div class="PayAmount">
            <span class="Amount">{{ payInfo.info.amount }}</span
            ><span> 元 </span>
          </div>
          <div class="Countdown">
            <div>
              请在<span place="time">{{ m >= 10 ? m : `0${m}` }}：{{ s >= 10 ? s : `0${s}` }}</span
              >内完成支付
            </div>
          </div>
          <div>成功付款后，将自动到账！</div>
          <div>如有问题，请<span place="thing" class="contact-customer" @click="$parent.openKefu"> 联系客服 </span>确认</div>
        </div>
      </div>

      <div data-v-334775a8="" class="transaction-detail bg">
        <p data-v-334775a8="" v-if="type == 'usdtpay'">
          <span data-v-334775a8=""> 收款地址 </span
          ><span data-v-334775a8="" style="word-break: break-word; max-width: 240px"
            ><span data-v-334775a8="" class="transNum paddingSty">{{ payInfo.cardlist.mch_id }}</span
            ><span @click="doCopy(payInfo.cardlist.mch_id)" data-v-334775a8="" class="copy"> 复制 </span></span
          >
        </p>

        <p data-v-334775a8="">
          <span data-v-334775a8=""> 订单号 </span
          ><span data-v-334775a8=""
            ><span data-v-334775a8="" class="transNum paddingSty">{{ payInfo.deposit_no }}</span
            ><span v-if="false" data-v-334775a8="" class="copy"> 复制 </span></span
          >
        </p>
        <p data-v-334775a8="">交易时间 <span data-v-334775a8="" class="tran-time"> {{ payInfo.info.created_at }} </span></p>
        <p data-v-334775a8="">
          充值方式<span data-v-334775a8="" class="tran-type">{{ payInfo.info.paytype }}</span>
        </p>
            <p data-v-334775a8="" v-if="type =='usdtpay'">
          钱包协议<span data-v-334775a8="" class="tran-type">{{ payInfo.cardlist.content }}</span>
        </p>
        <div v-if="payInfo.cardlist.id == 4">
            <div style="margin-bottom: 15px;"><input style="width: 100%;height: 0.8rem;text-indent: 5px;" v-model="payname" placeholder="请输入您的姓名"></div>
            <div style="margin-bottom: 15px;"><input style="width: 100%;height: 0.8rem;text-indent: 5px;" v-model="paycode" placeholder="请输入您的支付宝红包口令"></div>
            <div style="text-align: center;"><button @click="setpaycode()" style="background: rgb(200, 160, 112);color: white;padding: 5px 15px;border: none; letter-spacing: 2px;border-radius: 5px;">提交红包口令</button></div>
        </div>
        <div v-else>
            <img :src="payInfo.cardlist.payimg" style="width:80%;display:block;margin:10px auto" alt="" />
        </div>
      </div>
      <div data-v-334775a8="" class="footer">
        <div data-v-334775a8="" class="goback-button" @click="$parent.goNav('/transRecord')">
          <img
            data-v-334775a8=""
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAWCAMAAADpVnyHAAAAPFBMVEVHcEwLqoUPqIUPqIYIp4cAr4AOqIYMp4cOp4UNp4UPqIYPqIYPqIYOp4YOqIUPqIYOqIUPp4UNqYYPqIanHmPRAAAAE3RSTlMAMO/wIBBwQIBg0MDfoLCvkOBQvn0j8QAAAJFJREFUGBl1wYkBwiAQBMAlPHdAXrf/XgVJFA3OoOMi/ogTBUM7yYABn1ko7szMSnETJ74ofu08beLQ8xs7c/C4mMRvs6AxE28ULwcHIirLkYgicmRyKMRyIKNyiQOCyi88LSJRV1YZTWCjqJxakg6NWFaKxiQy4OQSC8XJr1xx8QvJgIs/Mj4CafAmREce6MgTIHASgfcpjWAAAAAASUVORK5CYII="
            class="icon"
          /><span data-v-334775a8="">充值完成</span>
        </div>
        <div data-v-334775a8="" class="seedetail-button" @click="$parent.goNav('/transRecord')">
          <img
            data-v-334775a8=""
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAcCAMAAACnDzTfAAAAOVBMVEUMqIcPqIYOqIUOqIYPqIUAr4ALqoVHcEwPp4YOqYcNp4UPqIYPp4UOqIYOqIUIp4cNqYYOqIUPqIbGVSZiAAAAEnRSTlM/0JBw8BAwAMB/YN/goG8gULADspVqAAAAtklEQVQoz42S2xbEEAxFg0hCbzP5/48d2lqL0mnPm2xHboDZL5NeZCwnAW+oA5mdfXX+wEURNWSmCtwpqiRGqj1iyFGAJxZDo1gzaYuUmllpZN/mo7Y9epvvHxvrmZ1v5oBfZZLgR4yOXSLxpYfkMooWHCr6Lp87HH5W2zFRe94xAxarmdGNT7o/UfKd/2VRXKHUyXudAXcD5LKLcn/llPy5JydYzcWl5YYt+QluZplem+8Ye6EfKyMbAQTPPjYAAAAASUVORK5CYII="
            class="icon"
          /><span data-v-334775a8="">资金明细</span>
        </div>
      </div>
      <!---->
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
  name: 'payInfo',
  data() {
    return {
        payname:"",
        paycode:"",
        payno:"",
      payInfo: {},
      type: null,
      daoTime: null,
      m: 0,
      s: 0,
    };
  },
  created() {
    let that = this;

    var query = that.$route.query;
    if (query.deposit_no) {
        that.payno = query.deposit_no;
      that.getpayinfo(query.deposit_no);
    }
  },
  methods: {
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
      this.$parent.showTost(1, '复制成功！');
    },
    setpaycode() {
      let that = this;
      if(!that.payname) return that.$parent.showTost(1, '请输入您的姓名');
      if(!that.paycode) return that.$parent.showTost(1, '请输入红包口令');
      if(!that.payno) return that.$parent.showTost(1, '支付参数错误');
      that.$parent.showLoading();
      that.$apiFun.post('/api/paycode', { deposit_no:that.payno,pay_code:that.paycode,pay_name:that.payname }).then(res => {
        that.$parent.showTost(0, res.message);
        if (res.code == 200) {
          that.$parent.goNav('/transRecord')
        }
        that.$parent.hideLoading();
      });
    },
    getpayinfo(deposit_no) {
      let that = this;
      this.$parent.showLoading();

      that.$apiFun.post('/api/payinfo', { deposit_no }).then(res => {
        console.log(res);
        if (res.code != 200) {
          that.$parent.showTost(0, res.message);
        }
        if (res.code == 200) {
          that.payInfo = res.data;
          that.type = res.message;
          that.countTime();
        }
        this.$parent.hideLoading();
      });
    },
    countTime() {
      //获取当前时间
      let that = this;
      var date = new Date();
      var now = date.getTime(); //当前时间
      //   创建时间
      let created_at = that.payInfo.info.created_at;
      let createdTime = new Date(created_at).getTime();
      //设置截止时间
      var end = createdTime + 1000 * 60 * 60;
      //时间差
      var leftTime = end - now;

      //定义变量 d,h,m,s保存倒计时的时间
      if (leftTime >= 0) {
        // that.h = Math.floor((leftTime / 1000 / 60 / 60) % 24);
        that.m = Math.floor((leftTime / 1000 / 60) % 60);
        that.s = Math.floor((leftTime / 1000) % 60);
      } else {
        // $('.time').html('00:00');
        clearInterval(that.countTime);
        that.countTime = null;

        return;
      }
      let m = that.m >= 10 ? that.m : `0${that.m}`;
      let s = that.s >= 10 ? that.s : `0${that.s}`;
      // $('.time').html(`${m}:${s}`);
      //递归每秒调用countTime方法，显示动态时间效果
      setTimeout(this.countTime, 1000);
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
    if (that.countTime) {
      clearInterval(that.countTime);
    }
    that.countTime = null;
  },
};
</script>
<style lang="scss" scoped>
@import '../../static/css/fund-recharge-info.dba269b9.css';
</style>
