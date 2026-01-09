<template>
  <div class="bout-ball-bet-wrapper">
    <div class="header-placeholder"></div>
    <img class="bancgs" @click="$router.back()" src="/static/image/bank_020021515.png" alt="" />
    <div class="topsf">
      <div class="tois">
        <img src="/static/style/tx.0d38194b71b5a32ef1df50b7090ca7f5.png" alt="" />
        <div class="dwd">
          <div class="tisaa">Hi,尊敬的会员用户</div>
          <div class="newsa">早上好，欢迎来到帮助中心</div>
          <!-- <div class="newsa">若相关问题仍未解决，可咨询在线客服</div> -->
        </div>
      </div>
      <div style="color: #fff; text-align: center; margin-top: 20px">若相关问题仍未解决，可咨询在线客服</div>
    </div>
    <!-- 菜单列表 -->
    <!-- 
      // 1常见问题  2隐私政策  3免责说明  4联系我们  5代理加盟  7关于我们 8博彩责任

     -->
    <div class="bosfs">
      <div class="hgsw" @click="$parent.goNav('/boutBallBetInfo?type=7')">
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">关于我们</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
      <div class="hgsw" @click="$parent.goNav('/boutBallBetInfo?type=1')">
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">常见问题</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>

      <div class="hgsw" @click="$parent.goNav('/boutBallBetInfo?type=2')">
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">隐私政策</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
      <div class="hgsw" @click="$parent.goNav('/boutBallBetInfo?type=3')">
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">免责说明</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
      <div class="hgsw" @click="$parent.goNav('/boutBallBetInfo?type=4')">
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">联系我们</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
      <div class="hgsw" @click="$parent.goNav('/boutBallBetInfo?type=5')"> 
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">代理加盟</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
      <div class="hgsw" style="border: none" @click="$parent.goNav('/boutBallBetInfo?type=8')">
        <img class="firsimg" src="/static/image/1587555761884253.png" alt="" />
        <span class="tit">博彩责任</span>
        <span class="tisf"></span>
        <img class="rigiong" src="/static/style/icoArrowGrey@2x.abc43ab099390c4587d33290a76fc15e.png" alt="" />
      </div>
    </div>
    <div class="help-footer">没有找到解决办法？请联系<a @click="$parent.openKefu">人工客服</a>解决</div>
  </div>
</template>
<script>
export default {
  name: 'boutBallBet',
  data() {
    return {
      url: null,
    };
  },
  created() {
    let that = this;
  },
  methods: {},
  mounted() {
    let that = this;
  },
  updated() {
    let that = this;
  },
};
</script>

<style lang="scss" scoped>
// 页面容器 - 参考首页的深色主题
.bout-ball-bet-wrapper {
  width: 100%;
  min-height: 100vh;
  background: #000000;
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
  padding-bottom: 20px;
}

.header-placeholder {
  height: 180px;
}

.bancgs {
  position: fixed;
  top: 10px;
  left: 10px;
  width: 40px;
  height: 40px;
  z-index: 100;
  cursor: pointer;
  filter: brightness(1.5);
}

.topsf {
  background: url(/static/image/welcome-bg.812e6eebb547ed38a04db1056d489b08.812e6eeb.png) bottom no-repeat;
  background-size: 100% 100%;
  height: 180px;
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 10;
  
  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1;
  }
  
  .tois {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    margin: 0 auto;
    width: 90%;
    color: #fff;
    position: relative;
    z-index: 2;
    
    .tisaa {
      font-size: 16px;
      font-weight: 700;
    }
    .newsa {
      margin-top: 6px;
      font-size: 10px;
      color: rgba(255, 255, 255, 0.9);
    }
    img {
      width: 50px;
      margin-right: 15px;
    }
  }
}

.bosfs {
  margin-top: 15px;
  border-radius: 15px;
  padding: 4px 12px;
  box-sizing: border-box;
  width: 90%;
  margin: 0 auto;
  margin-top: 15px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  -webkit-box-shadow: 0 0.04rem 0.2rem 0 rgba(157, 78, 221, 0.3);
  box-shadow: 0 0.04rem 0.2rem 0 rgba(157, 78, 221, 0.3);
  
  .hgsw {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 40px;
    box-sizing: border-box;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:hover {
      background: rgba(255, 255, 255, 0.05);
    }
    
    &:last-child {
      border-bottom: none;
    }
    
    .firsimg {
      width: 24px;
      filter: brightness(1.5);
    }
    .tit {
      color: #ffffff;
      margin-left: 10px;
      font-weight: 500;
    }
    .tisf {
      flex: 1;
      margin: 0 12px;
      color: rgba(255, 255, 255, 0.6);
      text-align: right;
    }
    .rigiong {
      width: 6px;
      filter: brightness(1.5);
    }
  }
}

.help-footer {
  margin-top: 0.48rem;
  text-align: center;
  color: rgba(255, 255, 255, 0.8);
  padding-bottom: 0.6rem;
  
  a {
    color: #9d4edd;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    
    &:hover {
      text-decoration: underline;
    }
  }
}
</style>
