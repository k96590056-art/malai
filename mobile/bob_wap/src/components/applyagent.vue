<template>
  <div class="applyagent-wrapper">
    <van-nav-bar 
      class="page-nav-bar" 
      title="合营计划" 
      left-arrow 
      @click-left="$router.back()" 
    />
    <div class="nav-bar-placeholder"></div>
    <div class="pahsn">
      <img data-v-56fcd294="" src="/static/image/__al__title01.7a3975958589d48b22c30b3b976a95fc.png" style="display: block; width: 80%; margin: 0 auto; padding-top: 30px" />
      <img data-v-56fcd294="" src="/static/image/__al__person01.8b896040f87c2dfffa7e8de68ed19c42.png" style="display: block; width: 100%; margin: 0 auto" />
      <div @click="$parent.openKefu" class="zixun">
        <img data-v-56fcd294="" src="/static/image/16044962635685155.png" />
        <div class="cnets">
          <div class="tos">合营部</div>
          <div class="bos">立即咨询</div>
        </div>
        <div class="zusnb">咨询</div>
      </div>
      <div class="bsd">
        <van-form>
          <van-field label="用户名" v-model="$store.state.userInfo.username" disabled />
          <van-field label="真实姓名" v-model="$store.state.userInfo.realname" disabled />
          <van-field label="联系方式" v-model="info.mobile" placeholder="请输入您的联系方式" />
          <van-field label="申请理由" v-model="info.apply_info" placeholder="请输入申请说明" />
        </van-form>
        <van-button class="submit-btn" @click="shenqing" block >加入我们</van-button>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'applyagent',
  data() {
    return {
      info: {},
    };
  },
  created() {
    let that = this;
  },
  methods: {
    shenqing() {
      let that = this;
      let info = that.info;
      let regExp = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
      if (!regExp.test(info.mobile)) {
        that.$parent.showTost(0, '请输入正确手机号');
        return;
      }

      if (!info.apply_info) {
        that.$parent.showTost(0, '请输入申请理由');
        return;
      }

      that.$parent.showLoading();
      that.$apiFun
        .post('/api/applyagentdo', info)
        .then(res => {
          that.$parent.showTost(1, res.message);
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
.applyagent-wrapper {
  width: 100%;
  min-height: 100vh;
  background: #000000;
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
  padding-bottom: 20px;
}

// 导航栏样式
.page-nav-bar {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  background-color: rgba(0, 0, 0, 0.85) !important;
  backdrop-filter: blur(10px);
  z-index: 100;
  
  :deep(.van-nav-bar__title) {
    color: #ffffff !important;
    font-weight: bold;
  }
  
  :deep(.van-nav-bar__arrow) {
    color: #ffffff !important;
    font-size: 18px !important;
  }
  
  :deep(.van-nav-bar__left) {
    color: #ffffff !important;
    
    .van-icon {
      color: #ffffff !important;
    }
  }
  
  :deep(.van-nav-bar__content) {
    color: #ffffff !important;
  }
  
  :deep(.van-icon) {
    color: #ffffff !important;
  }
}

.nav-bar-placeholder {
  height: 46px;
}

.pahsn {
  background: url(/static/image/__al__background.2e29d452d69738493237414076a048d3.png) no-repeat;
  background-size: 100% 100%;
  margin-top: 40px;
  min-height: 100vh;
  width: 100%;
  position: relative;
  
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
  
  > * {
    position: relative;
    z-index: 2;
  }
}

.zixun {
  width: 88%;
  margin: 0 auto;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  display: flex;
  align-items: center;
  padding: 10px;
  position: relative;
  margin-top: -96px;
  img {
    width: 30px;
    margin-right: 10px;
  }
  .cnets {
    flex: 1;
    border-left: 1px solid rgba(255, 255, 255, 0.2);
    padding-left: 10px;

    .tos {
      font-size: 12px;
      color: rgba(255, 255, 255, 0.6);
    }
    .bos {
      font-size: 14px;
      color: #ffffff;
    }
  }
  .zusnb {
    width: 60px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    font-size: 12px;
    color: #fff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
  }
}

.bsd {
  width: 90%;
  margin: 0 auto;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  padding: 20px 10px;
  margin-top: 10px;
  
  :deep(.van-field) {
    background: transparent;
    
    .van-field__label {
      color: rgba(255, 255, 255, 0.8);
    }
    
    .van-field__control {
      color: #ffffff;
      
      &::placeholder {
        color: rgba(255, 255, 255, 0.5);
      }
    }
  }
  
  :deep(.van-field--disabled) {
    .van-field__control {
      color: rgba(255, 255, 255, 0.6);
    }
  }
}

.submit-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border: none !important;
  color: #ffffff !important;
  font-weight: 600 !important;
}
</style>
