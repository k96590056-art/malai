<template>
  <div class="app app-ti_green">


        <router-view />
        <Foot />
   
  </div>
</template>

<script>
import Header from './libs/Header';
import Foot from './libs/Foot';
export default {
  name: 'Main',
  data() {
    return {
      baseURL: '',
    };
  },
  components: {
    Header,
    Foot,
  },
  created() {
    let that = this;

    that.baseURL = sessionStorage.getItem('baseURL') || '';
  },
  updated() {
    let that = this;
  },
  mounted() {
    let that = this;
  },
  methods: {
    outLogin() {
      this.$parent.outLogin();
    },
    openDaoTime() {
      this.openDaoTime();
    },
    closeDaoTime() {
      this.$parent.closeDaoTime();
    },
    openGamePage(name, type, code) {
      this.$parent.openGamePage(name, type, code);
    },

    goNav(url) {
      this.$parent.goNav(url);
    },

    getUserInfo() {
      this.$parent.getUserInfo();
    },
    getUserInfoShowLoding() {
      this.$parent.getUserInfoShowLoding();
    },
    doCopy(msg) {
      this.$parent.doCopy(msg);
    },
    showLoading() {
      this.$parent.showLoading();
    },
    hideLoading() {
      this.$parent.hideLoading();
    },
    openKefu() {
      this.$parent.openKefu();
    },
    getAgentLoginUrl() {
      this.$parent.getAgentLoginUrl();
    },
    showTost(type, title) {
      this.$parent.showTost(type, title);
    },
    getBalance() {
      this.$parent.getBalance();
    },
  },
  beforeDestroy() {
    let that = this;
    if (that.daoTime) {
      clearInterval(that.daoTime);
    }
    that.daoTime = null;
  },
  // watch: {
  //   //监听路由地址的改变
  //   $route: {
  //     immediate: true,
  //     handler() {
  //       let that = this;
  //       that.path = this.$route.path;
  //     },
  //   },
  // },
};
</script>

<style lang="scss" scoped>
// @import '../../static/css/announcement_modal.css';
</style>
