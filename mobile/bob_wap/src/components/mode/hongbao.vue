<template>
  <div class="bisd" style="background: url(/static/image/bg123456.jpg) top center no-repeat !important; width: 100%">
    <input type="hidden" id="startDate" value="2022-06-18" /> <input type="hidden" id="endDate" value="2022-06-18" />
    <input type="hidden" id="startTime" value="14:00:00" /> <input type="hidden" id="endTime" value="15:59:59" />
    <input type="hidden" id="currentDateTime" value="2022-06-18 16:13:44" /> <input type="hidden" id="redPacketStatus"
      value="END" /> <input type="hidden" id="activityTimeId" value="" /> <input type="hidden" id="memberType"
      value="0" /> <input type="hidden" id="amount1" value="0" />
    <input type="hidden" id="amount2" value="00" /> <input type="hidden" id="isLocal" value="0" /> <input type="hidden"
      id="isWindow" value="0" /> <input type="hidden" id="windowHeight" value="" /> <input type="hidden"
      id="windowWidth" value="" />
    <div class="redEnveBody">
      <div class="redEnveBodyToo">
        <div class="redEnveBodyMain center">
          <div class="redEnveBodyTop">
            <div class="pen" style="z-index: 200;"><img src="/static/image/pen.png" /></div>
            <!-- <div class="hongbao"></div>
            <div class="jinbi"></div>
            <div class="jinbi2"></div>
            <div class="jinbi3"></div>
            <div class="caidai"></div>
            <div class="lcaidai"></div> -->
          </div>
          <div class="redEnveFont"><img alt="" src="/static/image/qianghb.png" /></div>
          <div class="toddyTotal"></div>
          <div class="redEnveTime" v-if="false">
            <div class="time">
              <span id="dd">00</span>: <span id="hh">00</span>
              <div class="redEnveClock redEnveClock2" v-if="false">
                <div id="ready" style="display: none">
                  <div data-wow-duration="0.35s" data-wow-delay="0.35s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.35s; animation-delay: 0.35s; animation-name: zoomInLeft">
                    始</div>
                  <div data-wow-duration="0.30s" data-wow-delay="0.30s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.3s; animation-delay: 0.3s; animation-name: zoomInLeft">
                    开</div>
                  <div data-wow-duration="0.25s" data-wow-delay="0.25s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.25s; animation-delay: 0.25s; animation-name: zoomInLeft">
                    包</div>
                  <div data-wow-duration="0.20s" data-wow-delay="0.20s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.2s; animation-delay: 0.2s; animation-name: zoomInLeft">
                    红</div>
                  <div data-wow-duration="0.15s" data-wow-delay="0.15s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.15s; animation-delay: 0.15s; animation-name: zoomInLeft">
                    离</div>
                  <div data-wow-duration="0.10s" data-wow-delay="0.10s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.1s; animation-delay: 0.1s; animation-name: zoomInLeft">
                    距</div>
                </div>
                <div id="starting" style="display: none">
                  <div data-wow-duration="0.35s" data-wow-delay="0.35s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.35s; animation-delay: 0.35s; animation-name: zoomInLeft">
                    束</div>
                  <div data-wow-duration="0.30s" data-wow-delay="0.30s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.3s; animation-delay: 0.3s; animation-name: zoomInLeft">
                    结</div>
                  <div data-wow-duration="0.25s" data-wow-delay="0.25s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.25s; animation-delay: 0.25s; animation-name: zoomInLeft">
                    包</div>
                  <div data-wow-duration="0.20s" data-wow-delay="0.20s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.2s; animation-delay: 0.2s; animation-name: zoomInLeft">
                    红</div>
                  <div data-wow-duration="0.15s" data-wow-delay="0.15s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.15s; animation-delay: 0.15s; animation-name: zoomInLeft">
                    离</div>
                  <div data-wow-duration="0.10s" data-wow-delay="0.10s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.1s; animation-delay: 0.1s; animation-name: zoomInLeft">
                    距</div>
                </div>
                <div id="red-packet-finish" style="display: none">
                  <div data-wow-duration="0.30s" data-wow-delay="0.30s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.3s; animation-delay: 0.3s; animation-name: zoomInLeft">
                    完</div>
                  <div data-wow-duration="0.25s" data-wow-delay="0.25s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.25s; animation-delay: 0.25s; animation-name: zoomInLeft">
                    抢</div>
                  <div data-wow-duration="0.20s" data-wow-delay="0.20s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.2s; animation-delay: 0.2s; animation-name: zoomInLeft">
                    已</div>
                  <div data-wow-duration="0.15s" data-wow-delay="0.15s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.15s; animation-delay: 0.15s; animation-name: zoomInLeft">
                    包</div>
                  <div data-wow-duration="0.10s" data-wow-delay="0.10s" class="wow zoomInLeft animated"
                    style="visibility: visible; animation-duration: 0.1s; animation-delay: 0.1s; animation-name: zoomInLeft">
                    红</div>
                </div>
                <div id="finish" style="display: block">
                  <div data-wow-duration="0.30s" data-wow-delay="0.30s" class="wow zoomInLeft"
                    style="visibility: visible; animation-duration: 0.3s; animation-delay: 0.3s; animation-name: zoomInLeft">
                    束</div>
                  <div data-wow-duration="0.25s" data-wow-delay="0.25s" class="wow zoomInLeft"
                    style="visibility: visible; animation-duration: 0.25s; animation-delay: 0.25s; animation-name: zoomInLeft">
                    结</div>
                  <div data-wow-duration="0.20s" data-wow-delay="0.20s" class="wow zoomInLeft"
                    style="visibility: visible; animation-duration: 0.2s; animation-delay: 0.2s; animation-name: zoomInLeft">
                    已</div>
                  <div data-wow-duration="0.15s" data-wow-delay="0.15s" class="wow zoomInLeft"
                    style="visibility: visible; animation-duration: 0.15s; animation-delay: 0.15s; animation-name: zoomInLeft">
                    包</div>
                  <div data-wow-duration="0.10s" data-wow-delay="0.10s" class="wow zoomInLeft"
                    style="visibility: visible; animation-duration: 0.1s; animation-delay: 0.1s; animation-name: zoomInLeft">
                    红</div>
                </div>
              </div>
              <span id="mm">00</span>: <span id="ss">00</span>
            </div>
            <div class="time2"><span>天</span> <span>时</span> <span>分</span> <span>秒</span></div>
          </div>
          <div class="receiveTimes">
            <p style="text-align: center">
              剩余领取次数&nbsp;<span id="remainNum" class="yellow">{{ userredpacket.sendnums < 0 ? 0 : userredpacket.sendnums
              }}</span>&nbsp;次，已领取&nbsp;<span id="currentNum" class="yellow">{{ userredpacket.acquirednum
}}</span>&nbsp;次
            </p>
          </div>
          <div class="currReceiveTimes">
            <p style="text-align: center">
              当前最多可领取&nbsp;<span class="yellow3">{{ userredpacket.max_times }}</span>&nbsp;次， <span id="maxMsg">
                快去满足条件吧！ </span>
            </p>
          </div>
          <div class="redEnveButtons" @click="changShow"></div>
          <div class="activityInfo">
            <div class="activityTop wow zoomIn" style="visibility: visible; animation-name: zoomIn"></div>
            <table id="activityTable" v-if="userredpacket.rules.length > 0" style="font-size: 8px !important">
              <tbody>
                <tr>
                  <td style="border-radius: 17px 0px 0px;">活动时间</td>
                  <td style="min-width:100px">累计充值金额</td>

                  <td style="border-radius: 0px 17px 0px 0px;min-width:60px" >红包次数</td>
                </tr>
                <tr v-for="(item, index) in userredpacket.rules" :key="index">
                  <td>{{ item.start_time }} ~ {{ item.end_time }}</td>
                  <td>{{ item.day_flow }}-{{ item.flow_money }}</td>

                  <td>{{ Math.floor(item.recharge) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="activityBot wow flipInX"
              style="visibility: visible; animation-name: flipInX;line-height: 1.5;    font-size: 10px!important;">
              <h3 class="yellow" style="font-size: 12px!important;">领取规则：</h3>
              <p>1.抢到红包后，系统自动派彩，<span class="yellow">秒到账</span>，达到流水倍数即可取款；</p>
              <p>2.领取红包条件：充值金额需要在规定的活动日期（美东时间）范围内，根据充值累计金额，获取抢红包次数，即可抢对应次数的红包；</p>
            </div>
          </div>
          <div class="h400"></div>
          <div class="activityDes">
            <div class="activityDesTop wow zoomIn" style="visibility: visible; animation-name: zoomIn"></div>
            <div class="activityDesMain">
              <p style="text-align: center"><strong>金沙集团官网</strong></p>
              <p>
                <strong>注意：<strong style="white-space: normal">抢红包</strong>北京时间为每天早上
                  10点到12点，存款计算为前一天12点到今天10点，谢谢~</strong><br />每日百万现金红包！存款越多，机会越多，红包享不停，惊喜抢不停，还等什么？快快叫上好友一起分享吧！！<br />
              </p>
              <p>1、会员必须在指定的活动日期（美东时间）范围内，根据充值累计金额，即可获得对应抢红包次数。若在规定的时间范围内没有达到存款金额范围，则不计算抢红包次数，逾期作废！</p>
              <p>2、所有的活动优惠特为玩家而设，如发现任何团体或个人，以不诚实的方式套取红利或任何威胁、滥用公司优惠等行为，公司保留冻结、取消该团体或个人账户及账户结余的权利。</p>
              <p>3、金沙集团官网保留所有解释权，在任何时候都可以更改、停止、取消优惠活动。</p>
              <p><br /></p>
            </div>
          </div>
        </div>
      </div>
      <div class="botLeft wow bounceInLeft" style="visibility: visible; animation-name: bounceInLeft"><img
          src="/static/image/botLeft.png" /></div>
      <div class="botright"><img src="/static/image/botRight.png" class="wow bounceInRight"
          style="visibility: visible; animation-name: bounceInRight" /></div>
    </div>
    <div id="box1" picfloat="right" class="rightFloat" style="position: fixed; z-index: 1000; top: 365px; right: 0.25px"
      v-if="false">
      <!-- myService -->
      <a href="javascript:;" class="myRedEnves" @click="$parent.goNav('/welfare')"></a> <a href="javascript:;"
        @click="$parent.openKefu" class="myService"></a>
      <a href="javascript:;" id="currenTimePeriod" class="currenTimePeriod">
        <span class="timePeriodTitle">当前时间段</span> <span class="currenTime">{{ userredpacket.datetime }}</span>
      </a>
    </div>


    <!-- 红包还未开始 已结束 -->
    <div class="weikaishi divIndex" v-if="weikaishi">
      <p style="margin-top:66px;font-size:16px">活动还没开始，请静待活动开始。</p>
      <a href="javascript:;" @click="closeAll" class="close"></a>
    </div>
    <div class="qiangwan divIndex" v-if="end">
      <p style="margin-top:66px;font-size:18px">请静待下次活动。</p>
      <a href="javascript:;" @click="closeAll" class="close"></a>
    </div>
    <!-- 中奖了 -->
    <div class="zhongjiang divIndex" v-if="zhongjiang">
      <p>恭喜您</p>
      <p>
        抢到<span id="redPacketAmount" class="yellow bigFont">{{ mey }}</span>元
      </p>
      <a href="javascript:;" @click="closeAll" class="close"></a>
    </div>
    <!-- henbaoqian  很抱歉 -->
    <div class="henbaoqian divIndex" v-if="henbaoqian">
      <p id="henbaoqian">{{ message }}</p>
      <div id="viewRules" class="rules" @click="$parent.goNav('/wallet?type=0')">立即充值</div>
      <a href="javascript:;" @click="closeAll" class="close"></a>
    </div>
  </div>
</template>
<script>
export default {
  name: 'hongbao',
  data() {
    return { show: false, redpacketList: [], page: 1, redpacketShowData: {}, userredpacket: { rules: [] }, weikaishi: false, end: false, mey: 0, zhongjiang: false, henbaoqian: false, message: '' };
  },
  created() {
    let that = this;

    that.getuserredpacket();
  },
  methods: {

    closeAll() {
      this.show = false;
      this.weikaishi = false;
      this.end = false;
      this.zhongjiang = false;
      this.henbaoqian = false;
      this.message = '';
    },
    changShow() {
      let that = this;

      // userredpacket.redPacketStatus  红包状态 READY-未开始 STARTING-进行中 END-结束
      // if (that.userredpacket.redPacketStatus == 'READY') {
      //   this.weikaishi = true;
      // }
      // if (that.userredpacket.redPacketStatus == 'END') {
      //   this.end = true;
      // }
      // if (that.userredpacket.redPacketStatus == 'STARTING') {
      //   that.getwelfare();
      // }
      if (that.userredpacket.sendnums <= 0) {
        that.henbaoqian = true;
        that.message = '您暂未达到领取条件，快去完成吧！';
        return;
      }
      that.getwelfare();
    },
    getwelfare() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/douserredpacket', {})
        .then(res => {
          console.log(res);
          if (res.code == 200) {
            that.mey = res.data.redpacketmoney;
            that.getuserredpacket();
            that.zhongjiang = true;
          } else {
            that.henbaoqian = true;
            that.message = res.message;
          }

          that.$parent.hideLoading();
        })
        .catch(() => {
          that.$parent.showTost(0, '服务器异常，请稍后再试');
          that.$parent.hideLoading();
        });
    },

    getuserredpacket() {
      let that = this;
      that.$parent.showLoading();

      that.$apiFun.get('/api/userredpacket', {}).then(res => {
        console.log(res);
        if (res.code != 200) {
          that.$parent.showTost(0, res.message);
        }
        if (res.code == 200) {
          that.userredpacket = res.data;
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
};
</script>

<style lang="scss" scoped>
@import '../../../static/style/layui.css';
@import '../../../static/style/animate.min.css';
@import '../../../static/style/style.css';

.activityInfo table td {

  line-height: 60px;
}

.bisd {
  .redEnveBodyToo {
    width: 100%;
  }

  .redEnveBodyTop .pen {
    height: 80px;
    overflow: hidden;
  }

  .redEnveBodyTop .pen img {
    width: 100%;
  }

  .redEnveFont {
    width: 146px;
    margin: 0 auto;
    height: 80px;
    overflow: hidden;
    margin-top: -28px;
  }

  .redEnveFont img {
    width: 200px;
    margin: 0 !important;
  }

  .receiveTimes {
    width: 100%;
    font-size: 14px;
  }

  .currReceiveTimes {
    width: 100%;
    font-size: 14px;
  }

  .activityInfo .activityTop {
    width: 200px;
    height: 45px;
    background-size: 100% 100%;
    top: -48px;
  }

  .redEnveButtons {
    width: 200px;
    height: 55px;
    background-size: 100% 100%;
  }

  .activityInfo {
    margin-top: 60px;
  }

  .activityInfo table {
    width: 100%;
    font-size: 10px;
  }

  .activityInfo .activityBot {
    width: 100%;
    box-sizing: border-box;
    padding: 0 15px;
  }

  .activityDes .activityDesTop {
    width: 200px;
    height: 45px;
    background-size: 100% 100%;
    top: -48px;
  }

  .activityDes .activityDesMain {
    width: 100%;
    box-sizing: border-box;
    margin: 0 auto;
    font-size: 10px;
    padding: 0 15px;
  }

  .activityDes .activityDesMain p {
    line-height: 1.5;
  }

  .botright img {
    width: 300px;
    height: 450px;
  }

  .botLeft img {
    position: absolute;
    width: 300px;
    height: 400px;
    left: 0px;
    bottom: 0px;
  }

  .h400 {
    height: 103px;
  }

  .redEnveBodyTop .caidai {
    background: url(/static/image/caidai.png);
    position: absolute;
    width: 65px;
    height: 35px;
    left: 29px;
    top: 133px;
    background-size: 100% 100%;
    -webkit-animation: caidai-data-v-00ba43ca 1s cubic-bezier(0.07, 0.94, 0.71, 1.07) 0s;
    animation: caidai-data-v-00ba43ca 1s cubic-bezier(0.07, 0.94, 0.71, 1.07) 0s;
    -webkit-transition: all;
    transition: all;
  }

  .redEnveBodyTop .jinbi3 {
    background: url(/static/image/jinbi3.png);
    position: absolute;
    background-size: 100% 100%;
    width: 45px;
    height: 45px;
    left: 5px;
    top: 79px;
    -webkit-animation: jinbi3-data-v-00ba43ca 1s ease-in-out 0s;
    animation: jinbi3-data-v-00ba43ca 1s ease-in-out 0s;
    -webkit-transition: all;
    transition: all;
  }
}



.henbaoqian {
  background: url(/static/image/henbaoqian.png) no-repeat;
    width: 160px;
    background-size: 100% 100%;
    height: 200px;
    top: 45%;
    left: 0;
    right: 0;
    margin: 10px auto;
    padding: 145px 20px 0;
}

.henbaoqian p {
  margin-top: 66px !important;
  font-size: 12px;
  line-height: 1.5;
}

.zhongjiang {
  width: 158px !important;

  padding: 280px 100px 100px 87px !important;
}
</style>
