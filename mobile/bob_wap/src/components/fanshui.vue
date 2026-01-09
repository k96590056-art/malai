<template>
  <div class="fanshui-wrapper">
    <van-nav-bar 
      class="page-nav-bar" 
      title="返水中心" 
      left-arrow 
      @click-left="$router.back()" 
    />
    <div class="nav-bar-placeholder"></div>
    <div class="content-container">
      <div class="header-row">
        <span class="section-title">返水记录</span>
        <van-button class="action-btn" @click="lingqu">点击领取</van-button>
      </div>
      <div class="stats-row">
        <div class="stat-item">
          <div class="stat-label">累计领取</div>
          <div class="stat-value">￥{{ jisuan }}</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-label">待领取</div>
          <div class="stat-value">￥{{ nojisuan }}</div>
        </div>
      </div>
      <!-- 筛选条件 -->
      <div class="saibox">
        <div class="sai" @click="showPopup(1)">{{ name }}</div>
        <div class="sai" @click="showPopup(2)">{{ dateName[date] }}</div>
      </div>
      <van-list style="margin-top: 10px; padding-bottom: 120px" finished-text="没有更多了" offset="300" v-model="loading" :finished="list.length == pageData.total" @load="getData" v-if="list.length > 0">
        <van-cell v-for="(item, index) in list" :key="index">
          <div class="record-item">
            <div class="record-header">
              {{ item.gamename }} <span class="record-amount">返水金额 :{{ item.money }}</span>
            </div>
            <div class="record-time">返水时间:{{ item.created_at }}</div>
            <div class="record-time">领取时间：{{ item.state == 0 ? '暂未领取' : item.updated_at }}</div>
          </div>
        </van-cell>
      </van-list>
      <div v-else class="empty-state">
        <img src="/static/image/mescroll-empty.png" alt="" />
        <van-divider dashed>空空如也</van-divider>
      </div>
    </div>
    <!-- 弹出层 -->
    <van-popup v-model="popup" position="bottom" :style="{ height: 'calc(100% - 3.9rem - 46px)' }">
      <div class="lisg" v-if="showXuan == 1">
        <div class="bs" v-for="(item, index) in dogameLis" :key="index" @click="changDogame(item.name, item.platname)">
          <div :class="api_type == item.platname ? 'lisga act' : 'lisga'">{{ item.name }}</div>
        </div>
      </div>
      <div class="lisg" v-if="showXuan == 2">
        <div class="bs" @click="changtype('date', 1)">
          <div :class="date == 1 ? 'lisga act' : 'lisga'">今日</div>
        </div>
        <div class="bs" @click="changtype('date', 2)">
          <div :class="date == 2 ? 'lisga act' : 'lisga'">近7日</div>
        </div>
        <div class="bs" @click="changtype('date', 3)">
          <div :class="date == 3 ? 'lisga act' : 'lisga'">近15日</div>
        </div>
        <div class="bs" @click="changtype('date', 4)">
          <div :class="date == 4 ? 'lisga act' : 'lisga'">近30日</div>
        </div>
      </div>
    </van-popup>
  </div>
</template>
<script>
export default {
  name: 'fanshui',
  data() {
    return {
      date: 4,
      list: [],
      pageData: {},
      page: 1,
      dogameLis: [],
      api_type: '',
      loading: false,
      name: '全平台',
      show: false,
      jisuan: 0,
      nojisuan: 0,
      dateName: ['', '今日', '近7日', '近15日', '近30日'],
      popup: false,
      showXuan: 1, //1平台选择 2 日期选择
    };
  },
  created() {
    let that = this;
    that.getdogame();
    that.getData();
  },
  methods: {
    changDogame(name, type) {
      let that = this;
      that.name = name;
      that.api_type = type;
      that.popup = false;
      that.page = 1;
      that.getData();
    },
    changtype(name, val) {
      let that = this;
      that[name] = val;
      that.popup = false;
      that.page = 1;
      that.getData();
    },
    showPopup(val) {
      this.popup = true;
      this.showXuan = val;
    },
    lingqu() {
      let that = this;
      let fanshui = that.nojisuan;
      if (fanshui <= 0) {
        that.$parent.showTost(0, '暂无领取额度！');
        return;
      }
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/dofanshui', {})
        .then(res => {
          console.log(res);
          that.$parent.getUserInfo();
          that.$parent.showTost(1, res.message);
          that.getfanshui();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
 
    openOrclose() {
      this.show = !this.show;
    },
    changtab() {
      let that = this;
      that.page = 1;
      that.list = [];
      that.pageData = {};
      that.getData();
    },
    getdogame() {
      let that = this;

      that.$apiFun.post('/api/balancelist', {}).then(res => {
        console.log(res);
        if (res.code != 200) {
          that.$parent.showTost(res.message);
        }
        if (res.code == 200) {
          that.dogameLis = res.data;
          that.dogameLis.unshift({ name: '全平台', platname: '' });
        }
      });
    },
    changeDate() {
      let that = this;
      that.page = 1;
      that.getData();
    },

    // 获取交易记录
    getData() {
      let that = this;
      let page = that.page;
      if (page > that.pageData.last_page) {
        that.loading = false;

        return;
      }
      that.$parent.showLoading();
      let info = {
        date: that.date,
        page: that.page,
        api_type: that.api_type,
        type: '',
      };
      that.$apiFun
        .post('/api/getfanshui', info)
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.pageData = res.data.list;
            that.jisuan = res.data.jisuan;
            that.nojisuan = res.data.nojisuan;
            if (that.page == 1) {
              that.list = res.data.list.data;
            } else {
              let list = JSON.parse(JSON.stringify(that.list));
              res.data.list.data.forEach(el => {
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
.fanshui-wrapper {
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
  color: #ffffff;
}

.header-row {
  padding-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  
  .section-title {
    font-size: 0.3rem;
    color: #ffffff;
    font-weight: 600;
  }
}

.empty-state {
  margin-top: 60px;
  text-align: center;
  
  img {
    width: 35%;
  }
}

.stats-row {
  display: flex;
  box-sizing: border-box;
  padding: 0 12px;
  font-size: 0.3rem;
  justify-content: space-between;
  height: 1.1rem;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  
  .stat-item {
    font-size: 0.3rem;
    text-align: center;
    width: 49%;
    
    .stat-label {
      font-size: 0.3rem;
      color: rgba(255, 255, 255, 0.8);
    }
    
    .stat-value {
      font-size: 0.3rem;
      color: #9d4edd;
      font-weight: 700;
    }
  }
  
  .stat-divider {
    height: 76%;
    border-left: 1px solid rgba(255, 255, 255, 0.1);
  }
}

.record-item {
  color: rgba(255, 255, 255, 0.9);
  
  .record-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.3rem;
    margin-bottom: 6px;
    color: #ffffff;
    
    .record-amount {
      font-size: 0.3rem;
      color: #9d4edd;
      font-weight: 600;
    }
  }
  
  .record-time {
    font-size: 0.3rem;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 4px;
  }
}

.action-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border: none !important;
  color: #ffffff !important;
  font-weight: 600 !important;
}

:deep(.van-cell) {
  background: rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.9);
  border-radius: 8px;
  margin-bottom: 8px;
}

:deep(.van-divider) {
  color: rgba(255, 255, 255, 0.6) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
}

.saibox {
  display: flex;
  align-items: center;
  justify-content: space-around;
  height: 1.1rem;
  box-sizing: border-box;
  padding: 0 12px;
  .sai {
    height: 0.8rem;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40%;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1.1rem;
    font-size: 0.3rem;
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:hover {
      background: rgba(255, 255, 255, 0.15);
    }
  }
}
.lisg {
  box-sizing: border-box;
  padding: 10px 8px;
  display: flex;
  flex-wrap: wrap;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 10px;
  
  .bs {
    width: 25%;
    height: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    .lisga {
      width: calc(100% - 8px);
      height: 0.9rem;
      border: 0.02rem solid rgba(255, 255, 255, 0.3);
      border-radius: 0.08rem;
      color: rgba(255, 255, 255, 0.8);
      background: rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.2rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      
      &:hover {
        background: rgba(255, 255, 255, 0.15);
      }
    }
    .lisga.act {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      border: none;
    }
  }
}
</style>
