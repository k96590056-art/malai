<template>
  <div class="activity-record-wrapper">
    <van-nav-bar 
      class="page-nav-bar" 
      title="活动申请记录" 
      left-arrow 
      @click-left="$router.back()" 
    />
    <div class="nav-bar-placeholder"></div>
    <div class="content-container">
  
   
    
        <van-list style="margin-top: 10px; padding-bottom: 120px" finished-text="没有更多了" offset="300" v-model="loading" :finished="list.length == pageData.total" @load="getData" v-if="list.length > 0">
          <van-cell v-for="(item, index) in list" :key="index">
            <div style="color: #888 !important">
              <div>活动标题：{{ item.activity_name }}</div>
              <div style="display: flex; justify-content: space-between">
                申请时间：{{ item.created_at }} <span>状态： {{ statuTypeS[item.state] }}</span>
              </div>
            </div>
          </van-cell>
        </van-list>
      <div v-else style="margin-top: 60px; text-align: center">
        <img src="/static/image/mescroll-empty.png" style="width: 35%" alt="" />
        <van-divider dashed :style="{ color: '#ccc', borderColor: '#ccc', padding: '20px ' }">空空如也</van-divider>
      </div>
    </div>

  </div>
</template>
<script>
export default {
  name: 'activityRecord',
  data() {
    return {
      list: [],
      pageData: {},
      page: 1,
      loading: false,
      statuTypeS: ['0未约定', '待审核', '通过', '拒绝', '4未约定'],
    };
  },
  created() {
    let that = this;
    that.getData();
  },
  methods: {
    getData() {
      let that = this;
      let page = that.page;
      if (page > that.pageData.last_page) {
        that.loading = false;

        return;
      }
      that.$parent.showLoading();
      let info = {
        page: that.page,
      };
      that.$apiFun
        .post('/api/activityApplyLog', info)
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.pageData = res.data;

            if (that.page == 1) {
              that.list = res.data.data;
            } else {
              let list = JSON.parse(JSON.stringify(that.list));
              res.data.data.forEach(el => {
                list.push(el);
              });
              that.list = list;
            }
            that.page = page + 1;
          }
          that.loading = false;
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
          that.loading = false;
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
.activity-record-wrapper {
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

.content-container {
  width: 95%;
  min-width: 250px;
  margin: 0 auto;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  box-sizing: border-box;
  padding: 10px;
  min-height: 90vh;
}

:deep(.van-cell) {
  background: rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.9);
  border-radius: 8px;
  margin-bottom: 8px;
  
  div[style*="color: #888"] {
    color: rgba(255, 255, 255, 0.9) !important;
  }
}

:deep(.van-divider) {
  color: rgba(255, 255, 255, 0.6) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
}
</style>
