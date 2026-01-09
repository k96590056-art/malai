import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
let userInfo = JSON.parse(localStorage.getItem('userInfo')) || {};
let token = sessionStorage.getItem("token") || '';

let appInfo = JSON.parse(localStorage.getItem('appInfo')) || {};
let messageNum = token ? localStorage.getItem("messageNum") || 0 : 0;

// 初始化时从 localStorage 加载游戏列表
let realbetList = localStorage.getItem('realbetList') ? JSON.parse(localStorage.getItem('realbetList')) : [];
let jokerList = localStorage.getItem('jokerList') ? JSON.parse(localStorage.getItem('jokerList')) : [];
let gamingList = localStorage.getItem('gamingList') ? JSON.parse(localStorage.getItem('gamingList')) : [];
let sportList = localStorage.getItem('sportList') ? JSON.parse(localStorage.getItem('sportList')) : [];
let lotteryList = localStorage.getItem('lotteryList') ? JSON.parse(localStorage.getItem('lotteryList')) : [];
let conciseList = localStorage.getItem('conciseList') ? JSON.parse(localStorage.getItem('conciseList')) : [];

export default new Vuex.Store({
  //state存放状态,
  state: {
    userInfo,
    token,
    messageNum,
    appInfo,
    bannerList: [],
    realbetList,
    jokerList,
    gamingList,
    sportList,
    lotteryList,
    conciseList,
  },
  //getter为state的计算属性
  getters: {

  },
  //mutations可更改状态的逻辑，同步操作
  mutations: {
    changGameList(state) {
      let bannerList = localStorage.getItem('bannerList') ? JSON.parse(localStorage.getItem('bannerList')) : [];
      state.bannerList = bannerList;
      let realbetList = localStorage.getItem('realbetList') ? JSON.parse(localStorage.getItem('realbetList')) : [];
      state.realbetList = realbetList;
      let jokerList = localStorage.getItem('jokerList') ? JSON.parse(localStorage.getItem('jokerList')) : [];
      state.jokerList = jokerList;
      let gamingList = localStorage.getItem('gamingList') ? JSON.parse(localStorage.getItem('gamingList')) : [];
      state.gamingList = gamingList;
      let sportList = localStorage.getItem('sportList') ? JSON.parse(localStorage.getItem('sportList')) : [];
      state.sportList = sportList;
      let lotteryList = localStorage.getItem('lotteryList') ? JSON.parse(localStorage.getItem('lotteryList')) : [];
      state.lotteryList = lotteryList;
      let conciseList = localStorage.getItem('conciseList') ? JSON.parse(localStorage.getItem('conciseList')) : [];
      state.conciseList = conciseList;

    },
    changUserInfo(state) {
      let userInfo = localStorage.getItem('userInfo') ? JSON.parse(localStorage.getItem('userInfo')) : {};
      state.userInfo = userInfo;
    },
    changToken(state) {
      state.token = sessionStorage.getItem('token') || '';
    }
    , changMessageNum(state) {
      let show = localStorage.getItem('show');
      state.messageNum = show ? 0 : localStorage.getItem('messageNum');
    }
    , changappInfo(state) {
      let appInfo = JSON.parse(localStorage.getItem('appInfo'))
      state.appInfo = appInfo;
    },
  },
  //提交mutation，异步操作
  actions: {

  },
  // 将store模块化
  modules: {
  }
})
