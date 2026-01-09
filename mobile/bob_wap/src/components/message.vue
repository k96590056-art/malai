<template>
  <div class="message-wrapper">
    <van-nav-bar 
      class="page-nav-bar" 
      title="消息中心" 
      left-arrow 
      @click-left="$router.back()" 
    />
    <div class="nav-bar-placeholder"></div>
    <div class="van-tabs-view van-tabs">
      <div class="van-tabs__wrap">
        <div role="tablist" class="van-tabs__nav van-tabs__nav--card">
          <div role="tab" @click="changType(1)" aria-selected="true" :class="type == 1 ? 'van-tab van-tab--active' : 'van-tab'"><span class="van-tab__text van-tab__text--ellipsis">公告</span></div>
          <div role="tab" @click="changType(2)" :class="type == 2 ? 'van-tab van-tab--active' : 'van-tab'">
            <span class="van-tab__text van-tab__text--ellipsis"><span>站内信</span></span>
          </div>
        </div>
      </div>
      <div class="van-tabs__content" style="width: 90%; margin: 0 auto">
        <div v-if="type == 1" role="tabpanel" class="van-tab__pane" style="">
          <van-list finished-text="没有更多了" :finished="true" v-if="homenoticelis.length > 0">
            <van-cell v-for="(item, index) in homenoticelis" :key="index">
              <div class="content">{{ item }}</div>
            </van-cell>
          </van-list>
          <van-divider v-else dashed :style="{ color: '#ccc', borderColor: '#ccc', padding: '20px 100px' }">没有更多了~</van-divider>
        </div>
        <div v-if="type == 2" role="tabpanel" class="van-tab__pane">
          <van-list finished-text="没有更多了" offset="300" v-model="loading" :finished="noticeList.length == noticeListInfo.total" @load="getDatalist" v-if="noticeList.length > 0">
            <van-cell v-for="(item, index) in noticeList" :key="index">
              <h3 class="unReadTitle">
                <span>{{ item.title }}</span>
              </h3>
              <div class="content" v-html="item.content">{{ item }}</div>
              <div class="content">{{ item.created_at }}</div>
            </van-cell>
          </van-list>
          <van-divider v-else dashed :style="{ color: '#ccc', borderColor: '#ccc', padding: '20px 100px' }">没有更多了~</van-divider>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'message',
  data() {
    return { type: 1, noticeList: [], homenoticelis: [], noticeListInfo: {}, page: 1 };
  },
  created() {
    let that = this;
    var query = that.$route.query;
    if (query.type) {
      that.type = query.type * 1;
    }

    that.homenotice();
    that.getDatalist();
  },
  methods: {
    changType(type) {
      let that = this;
      that.type = type;
    },
    homenotice() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun.post('/api/homenotice', {}).then(res => {
        if (res.code != 200) {
          that.showTost(0, res.message);
        }
        if (res.code == 200) {
          that.homenoticelis = res.data;
        }
        that.$parent.hideLoading();
      });
    },
    getDatalist() {
      let that = this;
      let page = that.page;
      if (page > that.noticeListInfo.last_page) {
        that.loading = false;

        return;
      }
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/noticeList', { page })
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.noticeListInfo = res.data;
            if (page == 1) {
              that.noticeList = res.data.data;
            } else {
              let list = JSON.parse(JSON.stringify(that.list4));
              res.data.data.forEach(el => {
                list.push(el);
              });
              that.noticeList = list;
            }
            that.page = page + 1;
          }
          that.loading = false;

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
  beforeDestroy() {},
};
</script>
<style lang="scss" scoped>
// 页面容器 - 参考首页的深色主题
.message-wrapper {
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
  height: 60px;
}

// 标签页样式
:deep(.van-tabs) {
  background: transparent;
  
  .van-tabs__nav--card {
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 4px;
  }
  
  .van-tabs__nav--card .van-tab {
    color: rgba(255, 255, 255, 0.6);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    
    &:last-child {
      border-right: none;
    }
  }
  
  .van-tabs__nav--card .van-tab.van-tab--active {
    color: #ffffff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 6px;
  }
}

// 列表样式
:deep(.van-list) {
  background: transparent;
}

:deep(.van-cell) {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  margin-bottom: 10px;
  color: #ffffff;
  
  .content {
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
  }
  
  .unReadTitle {
    color: #ffffff;
    font-weight: bold;
    margin-bottom: 8px;
  }
}

:deep(.van-divider) {
  color: rgba(255, 255, 255, 0.6) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
}
</style>
