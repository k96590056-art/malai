<template>
  <div class="wallet-wrapper">
    <van-nav-bar 
      class="wallet-nav-bar" 
      title="卡片管理" 
      left-arrow 
      @click-left="$router.back()" 
    />
    <div class="nav-bar-placeholder"></div>

    <van-tabs v-model="type" >
      <van-tab title="虚拟币" name='1'>
        <div class="lis" v-for="(item, index) in usdssLis" :key="index">
          <img class="lefs" src="/static/image/1595237922936176.png" alt="" />
          <div class="cest">
            <div class="type">{{ item.bank }}-{{ item.bank_owner }}</div>
            <div class="num">
              <span>****</span><span>****</span><span>****</span><span>{{ item.bank_no.substr(-4) }}</span>
            </div>
          </div>
          <img class="rigss" @click="delCard(item.id)" src="/static/style/wdb_icon.png" alt="" />
        </div>
        <div class="adds">
          <van-button v-if="usdssLis.length < 5" plain  style="width: 100%" @click="$parent.goNav('/addUsdtCard')">添加USDT地址</van-button>
          <div class="btntits">最多支持添加5个地址</div>
        </div>
      </van-tab>
      <van-tab title="银行卡" name='2'>
        <div class="lis" v-for="(item, index) in usercardLis" :key="index">
          <img class="lefs" :src="item.ico" alt="" />
          <div class="cest">
            <div class="type">{{ item.bank }}</div>
            <div class="type">{{ item.bank_owner }}</div>
            <!-- <div class="type">{{ item.bank_address }}</div> -->
            <div class="num">
              <span>****</span><span>****</span><span>****</span><span>{{ item.bank_no.substr(-4) }}</span>
            </div>
          </div>
          <img class="rigss" @click="delCard(item.id)" src="/static/style/wdb_icon.png" alt="" />
        </div>
        <div class="adds">
          <van-button v-if="usercardLis.length < 5" @click="$parent.goNav('/addBankCard')" plain  style="width: 100%">添加银行卡</van-button>
          <div class="btntits">最多支持添加5张银行卡</div>
        </div>
      </van-tab>
    </van-tabs>
  </div>
</template>
<script>
export default {
  name: 'wallet',
  data() {
    return {
      usercardLis: [],
      usdssLis: [],type:1
    };
  },
  created() {
    let that = this;
        var query = that.$route.query;
    if (query.type) {
      that.type = query.type;
    }
    that.getUsercard();
    that.getUsdssList();
  },
  methods: {
    delCard(id) {
      let that = this;
      that.$dialog
        .confirm({
          title: '温馨提示',
          message: '确定要解除绑定该卡片吗？',
        })
        .then(() => {
          that.$parent.showLoading();

          that.$apiFun.post('/api/delcard', { id }).then(res => {
            if (res.code != 200) {
              that.$parent.showTost(0, res.message);
            }
            that.$parent.hideLoading();

            if (res.code == 200) {
              that.$parent.showTost(1, '解绑成功');
              that.getUsercard();
              that.getUsdssList();
            }
          });
        })
        .catch(() => {});
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
};
</script>

<style lang="scss" scoped>
// 钱包页面容器 - 参考首页的深色主题
.wallet-wrapper {
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
.wallet-nav-bar {
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
  }
  
  :deep(.van-nav-bar__content) {
    color: #ffffff !important;
  }
  
  // 确保返回按钮图标为白色
  :deep(.van-nav-bar__left .van-nav-bar .van-icon) {
    color: #ffffff !important;
  }
}

.nav-bar-placeholder {
  height: 46px;
}

// 标签页样式
:deep(.van-tabs) {
  background: transparent;
  
  .van-tabs__nav {
    background: rgba(0, 0, 0, 0.3);
    padding: 0 15px;
  }
  
  .van-tab {
    color: rgba(255, 255, 255, 0.6);
    
    &.van-tab--active {
      color: #9d4edd;
    }
  }
  
  .van-tabs__line {
    background: #9d4edd;
  }
}

// 添加按钮区域
.adds {
  width: 60%;
  margin: 0 auto;
  margin-top: 100px;
  
  .btntits {
    text-align: center;
    margin-top: 20px;
    font-size: 0.28rem;
    color: rgba(255, 255, 255, 0.6);
  }
}

// 卡片列表项
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
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
  
  &:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(157, 78, 221, 0.3);
  }
  
  .lefs {
    width: 1.5rem;
    filter: brightness(1.2);
  }
  
  .cest {
    flex: 1;
    margin: 0 20px;
    
    .type {
      font-size: 0.4rem;
      font-weight: 700;
      color: #ffffff;
      margin-top: 6px;
    }
    
    .num {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 6px;
      color: rgba(255, 255, 255, 0.8);
      
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
    filter: brightness(1.5);
    cursor: pointer;
    transition: transform 0.3s ease;
    
    &:hover {
      transform: scale(1.1);
    }
  }
}
</style>
