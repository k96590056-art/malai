<template>
  <div v-if="bannerList.length > 0" class="page-wrapper">
    <!-- 红包浮层 -->
    <div id="redPacket" v-if="$store.state.appInfo.redpacket_switch == 1 && hongbashow">
      <i @click="$parent.goNav('/hongbao')" class="grab"></i>
      <img @click="changhongbashow" src="/static/image/hongbaocolse.png" />
    </div>

    <!-- 顶部Header占位元素（当header固定时保持布局） -->
    <div class="header-placeholder" v-if="isHeaderFixed" :style="{ height: headerHeight + 'px' }"></div>
    
    <!-- 顶部Header -->
    <div class="top-header" :class="{ 'header-fixed': isHeaderFixed }">
      <div class="header-center" @click="goToH5">
        <div class="logo-text">
          <span class="logo-title">{{ $store.state.appInfo.site_name || 'PG娱乐' }}</span>
          <span class="logo-domain">{{ ($store.state.appInfo.h5_url || '').replace(/^https?:\/\//, '') }}</span>
        </div>
      </div>
      <div class="header-right">
        <div class="login-btn" @click="$parent.goNav('/login')" v-if="!$store.state.token">登录/注册</div>
        <div class="login-btn logged" v-else>{{ $store.state.userInfo.username }}</div>
      </div>
    </div>

    <!-- 促销横幅 -->
    <div class="promo-banner">
      <van-swipe ref="bannerSwipe" :autoplay="3000" :initial-swipe="current" @change="onChange">
        <van-swipe-item v-for="(item, index) in bannerList" :key="index">
          <img :src="item.src" style="width: 100%" alt="" class="banner-image" />
        </van-swipe-item>
      </van-swipe>
      <div class="banner-pagination">
        <div 
          v-for="(item, index) in bannerList" 
          :key="index"
          :class="['pagination-dot', current === index ? 'active' : '']"
          @click="goToBanner(index)"
        ></div>
      </div>
    </div>

    <!-- 实时爆奖区域 -->
    <div class="jackpot-section">
      <div class="jackpot-wrapper">
        <div class="jackpot-label">合作伙伴</div>
        <div class="jackpot-list">
          <div class="jackpot-item" v-for="(item, index) in jackpotList" :key="index">
            <div class="jackpot-image">
              <img :src="item.image" alt="" />
        </div>
      </div>
    </div>
        </div>
      </div>
      
    <!-- 游戏分类图标行 -->
    <div class="game-category-nav">
      <!-- 热门分类（固定显示） -->
      <div class="category-item" :class="{ active: selectedCategory === 'hot' }" @click="selectCategory('hot')">
        <div class="category-icon">
          <span>🔥</span>
        </div>
        <div class="category-text">热门</div>
        </div>
      <!-- 从后端获取的分类列表 -->
      <div 
        class="category-item" 
        :class="{ active: selectedCategory === getCategoryCode(item.code) }" 
        @click="selectCategory(getCategoryCode(item.code))"
        v-for="(item, index) in gameCategories" 
        :key="item.id"
      >
        <div class="category-icon">
          <img 
            v-if="item.icon" 
            :src="item.icon" 
            :alt="item.name"
            @error="handleCategoryIconError"
          />
          <span v-else>📦</span>
          </div>
        <div class="category-text">{{ item.name }}</div>
            </div>
          </div>
          
    <!-- 热门推荐游戏网格 -->
    <div class="game-recommend-section">
      <div class="section-header">
        <div class="section-title">热门推荐</div>
        <div class="section-subtitle">精选热门游戏</div>
      </div>
      <div class="game-grid">
            <div 
          class="game-item" 
          v-for="(item, index) in displayGameList" 
          :key="`${selectedCategory}-${index}`"
              @click="handleGameClick(item, index)"
            >
          <div class="game-image-wrapper">
                <img :src="getCardImage(item)" alt="" @error="handleImageError" />
            <div class="game-overlay" v-if="item.multiplier">
              <div class="overlay-text">全场最高 {{ item.multiplier }}倍</div>
              </div>
            </div>
          <div class="game-name">{{ getCardTitle(item, index) }}</div>
        </div>
      </div>
    </div>

    <!-- 弹出层 -->
    <van-popup v-model="leftshow" position="left" :style="{ height: '100%' }">
      <div class="leftbox">
        <div class="side__main__1NhyG">
          <h3>Hi，欢迎进入{{ $store.state.appInfo.title }}</h3>
          <dl class="side__vip__1dW8w">
            <div class="topxs">专属VIP体验</div>
            <p>立享会员特权</p>
            <p>享受只属于你的与众不同</p>
            <dd @click="$parent.goNav('/vip')">会员中心</dd>
          </dl>
          <ul class="menu-list">
            <li v-if="$store.state.token" @click="$parent.goNav('/message')"><img src="/static/image/meunIcon.39f38dc98ad956615952d485d0e6af04.svg" />消息中心<span class="side__subtitle__3QtYC"></span></li>
            <li @click="$parent.openKefu"><img src="/static/image/meunIcon2.5d0d78496889fb8b027f603254286fdf.svg" />意见反馈<span class="side__subtitle__3QtYC"></span></li>
            <li @click="doCopy($store.state.appInfo.h5_url)">
              <img src="/static/image/menuIcon5.5687ef4d1512d53aa3535e3b1088fe70.png" />永久域名<span class="side__subtitle__3QtYC">{{ $store.state.appInfo.h5_url }}</span>
            </li>
            <li @click="$parent.goNav('/abouts')"><img src="/static/image/meunIcon3.c51bbb9ebece978f1976397ab12acba7.svg" />关于我们<span class="side__subtitle__3QtYC"></span></li>
          </ul>
          <div class="nisd login-btn" v-if="!$store.state.token" @click="$parent.goNav('/login')">立即登录</div>
          <div class="nisd logout-btn" v-else @click="$parent.outLogin"><img src="/static/image/tuichu.93c1b9e3d4b4a7772481916ca32c074f.svg" />安全退出</div>
        </div>
      </div>
    </van-popup>

    <!-- 公告弹窗 -->
    <div class="domainModal_domainView__FWCzg" v-if="goInfo">
      <div class="domainModal_mask__24Y2m domainModal_fadeIn__1I3AS false" @click="goInfo = null"></div>
      <div class="domainModal_content__1nBgc" style="width: 80%">
        <img src="/static/image/hongbaocolse.png" @click="goInfo = null" style="position: absolute; top: 5px; right: 13px; width: 0.7rem" alt="" />
        <div class="domainModal_middle__3gQPm" style="padding: 35px 10px 15px">
          {{ goInfo }}
          <van-button type="info" style="margin: 0 auto; margin-top: 20px; width: 120px; border-radius: 10px; height: 35px" @click="$parent.goNav('/message')">更多公告</van-button>
        </div>
      </div>
    </div>

    <!-- 官网弹窗 -->
    <div class="domainModal_domainView__FWCzg" v-if="$store.state.appInfo.index_modal == 1 && tanshow">
      <div class="domainModal_mask__24Y2m domainModal_fadeIn__1I3AS false" @click="changtanshow"></div>
      <div class="domainModal_content__1nBgc" style="width: 80%">
        <div id="domain" class="domainModal_contentTop__2C4jc">
          <img src="/static/image/close.png" @click="changtanshow" style="position: absolute; top: 5px; right: 13px; width: 0.6rem; z-index: 2;" alt="" />
          <div class="domainModal_top__1omYS">欢迎来到{{ $store.state.appInfo.title }}</div>
          <div class="domainModal_middle__3gQPm" v-html="$store.state.appInfo.webcontent"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'index',
  data() {
    return {
      hongbashow: true,
      current: 0,
      bannerList: [],
      homenoticelis: [],
      leftshow: false,
      activeKey: 0,
      gameType: 0,
      tanshow: true,
      goInfo: null,
      selectedCategory: 'hot', // 当前选中的分类
      gameCategories: [], // 游戏分类列表
      isHeaderFixed: false, // 顶部header是否固定
      scrollTop: 0, // 滚动位置
      headerHeight: 0, // top-header的高度
      pageTopHeight: 0, // pageTop元素的高度（红包浮层等）
      jackpotList: [
        // 占位数据，后续可以从接口获取
        {
          image: '/static/image/diy/jackpot1.jpg',
          multiplier: 'x367.5(爆)',
          amount: '29,484.00',
          game: 'PP电子-奥林匹斯财富',
          user: 'x***88'
        }, {
          image: '/static/image/diy/jackpot2.jpg',
          multiplier: '',
          amount: '22,449.00',
          game: 'PG电子-麻将胡了',
          user: 'g***rk'
        }, {
          image: '/static/image/diy/jackpot3.jpg',
          multiplier: '',
          amount: '22,449.00',
          game: 'PG电子-麻将胡了',
          user: 'g***rk'
        }
      ]
    };
  },
  computed: {
    // 根据选中的分类返回对应的游戏列表
    displayGameList() {
      // 定义所有分类的映射
      const categoryMap = {
        'realbet': this.$store.state.realbetList || [],
        'electronic': this.$store.state.conciseList || [],
        'joker': this.$store.state.jokerList || [],
        'fishing': [], // 捕鱼游戏，如果没有数据则返回空
        'lottery': this.$store.state.lotteryList || [],
        'sport': this.$store.state.sportList || []
      };
      
      if (this.selectedCategory === 'hot') {
        // 热门：显示所有分类的合集中 is_hot 为 1 的元素
        const allGames = [];
        // 遍历所有分类，收集所有游戏
        Object.values(categoryMap).forEach(gameList => {
          if (Array.isArray(gameList)) {
            allGames.push(...gameList);
          }
        });
        // 筛选出 is_hot 为 1 的游戏
        const hotGames = allGames.filter(game => game.is_hot === 1 || game.is_hot === '1');
        return hotGames;
      }
      
      return categoryMap[this.selectedCategory] || [];
    },
    imagePathPrefix() {
      const prefixMap = {
        'realbet': 'realbet',
        'electronic': 'concise',
        'joker': 'joker',
        'fishing': 'game',
        'lottery': 'lottery',
        'sport': 'sport'
      };
      return prefixMap[this.selectedCategory] || '';
    }
  },
  created() {
    let that = this;
    that.$store.commit('changGameList');
    that.getBanList();
    that.homenotice();
    that.getGameCategories();
  },
  methods: {
    openGogao(val) {
      this.goInfo = val;
    },
    changtanshow() {
      this.tanshow = !this.tanshow;
    },
    selectCategory(category) {
      this.selectedCategory = category;
      // 映射到原有的gameType以便兼容现有逻辑
      const categoryToGameType = {
        'realbet': 0,
        'sport': 1,
        'gaming': 2,
        'joker': 3,
        'electronic': 4,
        'lottery': 5,
        'fishing': 6
      };
      if (categoryToGameType.hasOwnProperty(category)) {
        this.gameType = categoryToGameType[category];
      }
    },
    // 将后端返回的code映射到前端使用的code
    getCategoryCode(code) {
      const codeMap = {
        'realbet': 'realbet',
        'sport': 'sport',
        'concise': 'electronic',
        'gaming': 'gaming',
        'joker': 'joker',
        'lottery': 'lottery',
        'fishing': 'fishing'
      };
      return codeMap[code] || code;
    },
    handleCategoryIconError(event) {
      // 分类图标加载失败时隐藏图片，显示默认图标
      event.target.style.display = 'none';
      const parent = event.target.parentElement;
      if (parent && !parent.querySelector('span')) {
        const span = document.createElement('span');
        span.textContent = '📦';
        parent.appendChild(span);
      }
    },
    changGameType(type) {
      this.gameType = type;
    },
    doCopy(msg) {
      let cInput = document.createElement('input');
      cInput.style.opacity = '0';
      cInput.value = msg;
      document.body.appendChild(cInput);
      cInput.select();
      document.execCommand('copy');
      this.$parent.showTost(1, '复制成功！');
    },
    changleftshow() {
      this.leftshow = !this.leftshow;
    },
    getBanList() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/bannerList', { type: 2 })
        .then(res => {
          if (res.code != 200) {
            that.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.bannerList = res.data;
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    homenotice() {
      let that = this;
      that.$apiFun.post('/api/homenotice', {}).then(res => {
        if (res.code != 200) {
          that.showTost(0, res.message);
        }
        if (res.code == 200) {
          that.homenoticelis = res.data;
          that.ok = true;
        }
      });
    },
    getGameCategories() {
      let that = this;
      that.$apiFun.post('/api/gameCategories', {}).then(res => {
        if (res.code == 200) {
          that.gameCategories = res.data || [];
        }
      }).catch(err => {
        console.error('获取游戏分类失败:', err);
      });
    },
    onChange(index) {
      this.current = index;
    },
    goToBanner(index) {
      this.current = index;
      if (this.$refs.bannerSwipe) {
        this.$refs.bannerSwipe.swipeTo(index);
      }
    },
    changhongbashow() {
      this.hongbashow = false;
    },
    handleImageError(event) {
      event.target.style.display = 'none';
      console.warn('图片加载失败:', event.target.src);
    },
    handleLogoError(event) {
      // Logo加载失败时使用默认图片
      if (event.target.src.indexOf('uacPoGJlb02AMGnUAAAYLvRuglw960.png') === -1) {
        event.target.src = '/static/image/uacPoGJlb02AMGnUAAAYLvRuglw960.png';
      }
    },
    getCardTitle(item, index) {
      return item.name || item.platform_name || '';
    },
    getCardImage(item) {
      // 优先使用接口返回的图片
      if (item.mobile_img) return item.mobile_img;
      if (item.api_logo_img) return item.api_logo_img;
      if (item.app_img) return item.app_img;
      
      // 根据category_id推断图片路径
      let imagePrefix = 'game'; // 默认路径
      if (item.category_id) {
        const categoryMap = {
          'realbet': 'realbet',
          'joker': 'joker',
          'gaming': 'gaming',
          'sport': 'sport',
          'lottery': 'lottery',
          'concise': 'concise'
        };
        imagePrefix = categoryMap[item.category_id] || imagePrefix;
      } else {
        // 如果没有category_id，使用当前选中的分类
        imagePrefix = this.imagePathPrefix || 'game';
      }
      
      let platformName = item.platform_name;
      if ((this.selectedCategory === 'lottery' || item.category_id === 'lottery') && item.platform_name === 'ig') {
        platformName = item.game_code;
      }
      return `/static/image/${imagePrefix}/${platformName}.png`;
    },
    handleGameClick(item, index) {
      this.$parent.openGamePage(item.platform_name, item.game_code, '');
    },
    goToH5() {
      const url = this.$store.state.appInfo.h5_url;
      if (url) {
        window.location.href = url;
      }
    },
  },
  mounted() {
    let that = this;
    
    // 延迟获取元素高度，确保 DOM 已渲染
    const initScrollHandler = () => {
      // 获取 top-header 的高度
      const headerEl = document.querySelector('.top-header');
      if (headerEl) {
        that.headerHeight = headerEl.offsetHeight;
      } else {
        // 如果还没渲染，使用默认值
        that.headerHeight = 60; // 默认高度
      }
      
      // 获取 pageTop 元素的高度（红包浮层等顶部元素）
      const pageTopEl = document.querySelector('#redPacket');
      if (pageTopEl && pageTopEl.offsetHeight > 0) {
        that.pageTopHeight = pageTopEl.offsetHeight;
      } else {
        that.pageTopHeight = 0;
      }
      
      // 添加滚动监听
      that.handleScroll = () => {
        // 获取滚动位置
        that.scrollTop = window.pageYOffset || 
                        document.documentElement.scrollTop || 
                        document.body.scrollTop || 
                        0;
        
        // 计算总高度（pageTop + top-header）
        // 如果高度还没计算出来，使用默认值
        const headerH = that.headerHeight || 60;
        const pageTopH = that.pageTopHeight || 0;
        const totalHeight = pageTopH + headerH;
        
        // 当滚动位置大于 pageTop 和 top-header 的总高度时，固定顶部header
        // 当页面在最顶端（scrollTop <= 0）时，不固定
        const shouldFix = that.scrollTop > 0 && that.scrollTop > totalHeight;
        
        // 更新状态
        that.isHeaderFixed = shouldFix;
      };
      
      // 使用 window 的 scroll 事件
      window.addEventListener('scroll', that.handleScroll, { passive: true });
      
      // 初始化检查
      that.handleScroll();
    };
    
    // 使用 $nextTick 确保 DOM 已渲染
    that.$nextTick(() => {
      // 如果 bannerList 还没加载，等待一下
      if (that.bannerList.length === 0) {
        setTimeout(initScrollHandler, 100);
      } else {
        initScrollHandler();
      }
    });
  },
  updated() {},
  beforeDestroy() {
    let that = this;
    // 移除滚动监听
    if (that.handleScroll) {
      window.removeEventListener('scroll', that.handleScroll);
      document.removeEventListener('scroll', that.handleScroll);
    }
  },
};
</script>

<style lang="scss" scoped>
// 深色主题背景
.page-wrapper {
  padding-bottom: 80px;
  width: 100%;
  max-width: 100%;
  min-height: calc(100vh - 60px);
  background: #000000;
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
  overflow-x: hidden;
  position: relative;
}

// 顶部Header占位元素
.header-placeholder {
  width: 100%;
  flex-shrink: 0;
}

// 顶部Header
.top-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 15px;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(10px);
  position: relative; // 默认不固定
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  max-width: 100%;
  z-index: 100;
  transition: all 0.3s ease;
  box-sizing: border-box;
  
  &.header-fixed {
    position: fixed !important; // 固定定位，使用 !important 确保生效
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
  }
  
  .header-center {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 10px;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    
    .header-logo {
      width: 40px;
      height: 40px;
      object-fit: contain;
      flex-shrink: 0;
    }
    
    .logo-text {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      min-width: 0;
      flex: 1;
      overflow: hidden;
      
      .logo-title {
        font-size: 20px;
        font-weight: bold;
        color: #9d4edd;
        text-shadow: 0 0 10px rgba(157, 78, 221, 0.8);
        letter-spacing: 1px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
      }
      
      .logo-domain {
        font-size: 10px;
        color: #ffffff;
        margin-top: 2px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
      }
    }
  }
  
  .header-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    
    .login-btn {
      padding: 6px 12px;
      background: #9d4edd;
      color: #ffffff;
      border-radius: 16px;
      font-size: 12px;
      font-weight: 500;
      cursor: pointer;
      white-space: nowrap;
      max-width: 100px;
      overflow: hidden;
      text-overflow: ellipsis;
      
      &.logged {
        background: rgba(157, 78, 221, 0.5);
        font-size: 11px;
        padding: 6px 10px;
        max-width: 80px;
      }
    }
    
    .kefu-icon {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: #ffffff;
      
      svg {
        width: 20px;
        height: 20px;
      }
    }
  }
}

// 促销横幅
.promo-banner {
  position: relative;
  margin: 0 15px 15px;
  border-radius: 12px;
  overflow: hidden;
  width: calc(100% - 30px);
  max-width: 100%;
  box-sizing: border-box;
  
  .banner-image {
    width: 100%;
    display: block;
  }
  
.banner-pagination {
  position: absolute;
    bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
    gap: 6px;
  z-index: 10;
  
  .pagination-dot {
      width: 6px;
      height: 6px;
    border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.3s ease;
    
    &.active {
        background: #ffffff;
      width: 20px;
        border-radius: 3px;
    }
  }
}
}

// 实时爆奖区域
.jackpot-section {
  margin: 0 15px 15px;
  width: calc(100% - 30px);
  max-width: 100%;
  box-sizing: border-box;
  
  .jackpot-wrapper {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

  .jackpot-label {
    font-size: 14px;
    font-weight: bold;
  color: #ffffff;
    writing-mode: vertical-lr;
    text-orientation: upright;
    letter-spacing: 4px;
    padding: 10px 5px;
    flex-shrink: 0;
  }
  
  .jackpot-list {
    display: flex;
    gap: 10px;
    flex: 1;
    min-width: 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    
    &::-webkit-scrollbar {
      display: none;
    }
    
    .jackpot-item {
      flex-shrink: 0;
      width: 160px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      padding: 10px;
      display: flex;
      flex-direction: column;
      
      .jackpot-image {
  width: 100%;
        height: 80px;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 8px;
        background: rgba(255, 255, 255, 0.1);
        
        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
      }
      
      .jackpot-info {
        flex: 1;
        
        .jackpot-multiplier {
          font-size: 12px;
          color: #ff6b6b;
          font-weight: bold;
          margin-bottom: 4px;
        }
        
        .jackpot-amount {
          font-size: 16px;
          font-weight: bold;
          color: #ffffff;
          margin-bottom: 4px;
        }
        
        .jackpot-game {
          font-size: 11px;
          color: rgba(255, 255, 255, 0.7);
          margin-bottom: 4px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
        
        .jackpot-user {
          font-size: 10px;
          color: rgba(255, 255, 255, 0.5);
  }
}
    }
  }
}

// 游戏分类导航
.game-category-nav {
  display: flex;
  padding: 15px;
  background: rgba(0, 0, 0, 0.3);
  margin-bottom: 15px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  gap: 15px;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
  
  &::-webkit-scrollbar {
    display: none;
  }
  
  .category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 60px;
    flex-shrink: 0;
    
    .category-icon {
      width: 44px;
      height: 44px;
      border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      margin-bottom: 6px;
    transition: all 0.3s ease;
      overflow: hidden;
      
      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      
      span {
        display: block;
      }
    }
    
    .category-text {
      font-size: 12px;
      color: rgba(255, 255, 255, 0.6);
      transition: all 0.3s ease;
    }
    
    &.active {
      .category-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.5);
      }
      
      .category-text {
  color: #ffffff;
  font-weight: bold;
      }
    }
  }
}

// 热门推荐游戏区域
.game-recommend-section {
  margin: 0 15px 20px;
  
  .section-header {
    margin-bottom: 15px;
    
    .section-title {
      font-size: 18px;
      font-weight: bold;
      color: #ffffff;
      margin-bottom: 4px;
    }
    
    .section-subtitle {
      font-size: 12px;
      color: rgba(255, 255, 255, 0.6);
    }
  }
  
  .game-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    
    .game-item {
      cursor: pointer;
      
      .game-image-wrapper {
        position: relative;
        width: 100%;
        padding-top: 133.33%; // 3:4 比例
        border-radius: 8px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.1);
        margin-bottom: 8px;
        
        img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
          height: 100%;
          object-fit: cover;
        }
        
        .game-overlay {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
          padding: 8px;
          
          .overlay-text {
            font-size: 11px;
            color: #ffd700;
    font-weight: bold;
          }
        }
      }
      
      .game-name {
        font-size: 12px;
    color: #ffffff;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
  }
}
}

// 弹出层样式（保持原有样式）
.leftbox {
  width: 7.5rem;
  height: 100%;
  background: linear-gradient(to right, #002040, #004080);
  color: #ffffff;
  .side__main__1NhyG {
    box-sizing: border-box;
    padding: 0 20px;
    h3 {
      font-size: 20px;
      font-weight: 400;
      margin: 0;
      padding-top: 72px;
    }
    .side__vip__1dW8w {
      background: url(/static/style/sidebr_vip_card.1ce7485811699286f87aae1827de7acf.png) no-repeat;
      background-size: 100% 100%;
      box-sizing: border-box;
      padding: 20px;
      color: #fff;
      position: relative;
      p {
        color: hsla(0, 0%, 100%, 0.6);
        font-size: 12px;
        margin: 5px 0 0 0;
      }
      .topxs {
        font-size: 16px;
      }
      dd {
        float: right;
        border: 0.02rem solid #fff;
        border-radius: 0.24rem;
        height: 0.48rem;
        line-height: 0.48rem;
        width: 1.88rem;
        text-align: center;
        font-size: 10px;
        position: absolute;
        top: 20px;
        right: 20px;
      }
    }
  }
  ul {
    list-style: none;
    margin-top: 0.36rem;
    li {
      display: block;
      line-height: 0.96rem;
      height: 0.96rem;
      border-bottom: 0.02rem solid #e6ebf6;
      color: #4e6693;
      font-size: 0.28rem;
      padding: 0 0.14rem;
      img {
        width: 0.36rem;
        vertical-align: middle;
        margin: -0.04rem 0.24rem 0 0;
      }
      span {
        float: right;
      }
    }
  }
  .nisd {
    position: absolute;
    width: 4.72rem;
    height: 0.8rem;
    line-height: 0.8rem;
    left: 0.9rem;
    bottom: 1rem;
    background: #dfe5ff;
    border-radius: 0.4rem;
    border: 0;
    color: #4e6693;
    font-size: 0.28rem;
    display: flex;
    justify-content: center;
    align-items: center;
    img {
      vertical-align: middle;
      margin: -0.04rem 0.08rem 0 -0.08rem;
      width: 0.32rem;
    }
  }
}

.menu-list {
  li {
    background: rgba(255, 255, 255, 0.1);
    margin-bottom: 10px;
  border-radius: 8px;
    transition: all 0.3s ease;
    &:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateX(5px);
    }
  }
}

.login-btn {
  background: #9d4edd !important;
  color: #ffffff !important;
  border-radius: 20px !important;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3) !important;
  font-weight: bold !important;
  transition: all 0.3s ease !important;
  &:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4) !important;
  }
}

.logout-btn {
  background: linear-gradient(to right, #ff3366, #ff0033) !important;
  color: #ffffff !important;
  border-radius: 20px !important;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3) !important;
    }
    
@import '../../../static/css/2d87bbdbffeb4734e5c7.css';

.domainModal_content__1nBgc {
  overflow: auto;
}

.domainModal_domainView__FWCzg {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 9999;
    }

.domainModal_mask__24Y2m {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
}

.domainModal_content__1nBgc {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #ffffff;
  border-radius: 12px;
  max-height: 80vh;
  overflow-y: auto;
}

.domainModal_contentTop__2C4jc {
  position: relative;
  padding: 20px;
  border-radius: 12px 12px 0 0;
  }
  
.domainModal_top__1omYS {
      font-size: 18px;
  font-weight: bold;
  margin-bottom: 15px;
      }
      
.domainModal_middle__3gQPm {
  font-size: 14px;
  line-height: 1.6;
}
</style>
