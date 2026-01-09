<template>
  <div class="bout-ball-bet-info-wrapper">
    <div class="header-placeholder"></div>
    <img class="bancgs" @click="$router.back()" src="/static/image/bank_020021515.png" alt="" />
    <div class="topsf">
      <div class="tois">
        <img src="/static/style/tx.0d38194b71b5a32ef1df50b7090ca7f5.png" alt="" />
        <div class="dwd">
          <div class="tisaa">Hi,尊敬的会员用户</div>
          <div class="newsa">早上好，欢迎来到帮助中心</div>
          <div class="newsa">若相关问题仍未解决，可咨询在线客服</div>
        </div>
      </div>
      <div style="color: #fff; text-align: center; margin-top: 10px; font-size: 20px">{{ title }}</div>
    </div>
    <div class="content-text" v-html="dataBox.content"></div>
    <div v-if="dataBox.content" class="help-footer">没有找到解决办法？请联系<a @click="$parent.openKefu">人工客服</a>解决</div>
  </div>
</template>
<script>
export default {
  name: 'boutBallBetInfo',
  data() {
    return {
      title: '',
      // 1常见问题  2隐私政策  3免责说明  4联系我们  5代理加盟  7关于我们 8博彩责任
      type: 0,
      dataBox: {},
    };
  },
  created() {
    let that = this;
    let query = that.$route.query;
    if (query.type) {
      let type = query.type * 1;
      that.type = type;
      if (type == 1) {
        that.title = '常见问题';
      }
      if (type == 2) {
        that.title = '隐私政策';
      }
      if (type == 3) {
        that.title = '免责说明';
      }
      if (type == 4) {
        that.title = '联系我们';
      }
      if (type == 5) {
        that.title = '代理加盟';
      }
      if (type == 7) {
        that.title = '关于我们';
      }
      if (type == 8) {
        that.title = '博彩责任';
      }
      that.getAllCont(type);
    }
  },
  methods: {
    getAllCont(type) {
      let that = this;
      that.$parent.showLoading();

      that.$apiFun
        .post('/api/article', { type })
        .then(res => {
          that.dataBox = res.data;
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
// 页面容器 - 参考首页的深色主题
.bout-ball-bet-info-wrapper {
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
  
  > div[style*="color: #fff"] {
    position: relative;
    z-index: 2;
  }
}

.content-text {
  color: rgba(255, 255, 255, 0.9) !important;
  padding: 0px 20px;
  box-sizing: border-box;
  line-height: 1.8;
  
  :deep(p) {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 10px;
  }
  
  :deep(h1), :deep(h2), :deep(h3), :deep(h4), :deep(h5), :deep(h6) {
    color: #ffffff;
    margin-top: 15px;
    margin-bottom: 10px;
  }
  
  :deep(ul), :deep(ol) {
    color: rgba(255, 255, 255, 0.9);
    padding-left: 20px;
  }
  
  :deep(li) {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 5px;
  }
  
  :deep(a) {
    color: #9d4edd;
  }
  
  :deep(strong), :deep(b) {
    color: #ffffff;
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
