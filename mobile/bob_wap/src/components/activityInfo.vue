<template>
  <div>
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="活动详情" left-arrow @click-left="$router.back()" />
    <div style="height:46px"></div>
    <div v-if="dataInfo.title">
      <img :src="dataInfo.banner" alt="" style="width: 100%; display: block" />
      <div style="text-align: center; font-size: 16px; padding-top: 15px">{{ dataInfo.title }}</div>
      <van-divider dashed :style="{ color: '#000', borderColor: '#ccc', padding: '10px', width: '50%', margin: '0 auto' }">活动详情</van-divider>
      <div class="tables" v-html="dataInfo.content"></div>
      <van-divider dashed :style="{ color: '#000', borderColor: '#ccc', padding: '10px', width: '50%', margin: '0 auto' }">活动说明</van-divider>
      <div class="tables" v-html="dataInfo.memo"></div>
      <div style="height: 120px"></div>
      <div class="bonsf">
        <div v-if="$store.state.token" class="btsdn" @click="doactivityapply">立即申请</div>
        <div v-else class="btsdn" @click="$parent.goNav('/login')">前往登录</div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'activityInfo',
  data() {
    return { dataInfo: {} };
  },
  created() {
    let that = this;
    let query = that.$route.query;
    if (query.id) {
      that.getInfo(query.id);
    }
  },
  methods: {
    getInfo(id) {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun.post('/api/activitydeatil', { id }).then(res => {
        console.log(res);
        if (res.code !== 200) {
          that.$parent.showTost(0, res.message);
        }
        if (res.code === 200) {
          that.dataInfo = res.data;
        }
        that.$parent.hideLoading();
      });
    },
    doactivityapply() {
      let that = this;
      that.$parent.showLoading();
      that.$apiFun.post('/api/doactivityapply', { activityid: that.dataInfo.id }).then(res => {
        that.$parent.hideLoading();
        that.$parent.showTost(1, res.message);
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
// @import '../../../static/css/announcement_modal.css';
.tables {
  font-size: 14px;
  box-sizing: border-box;
  padding: 0 20px;
  line-height: 1.5;
}
.bonsf {
  position: fixed;
  left: 5%;
  bottom: 10px;
  z-index: 95;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 90%;
  height: 50px;
  background: hsla(0, 0%, 100%, 0.9);
  box-shadow: 0 0 10px 0 rgb(0 0 0 / 50%);
  border-radius: 8px;
  .btsdn {
    display: flex;
    height: 38px;
    justify-content: center;
    align-items: center;
    border-radius: 100px;
    box-shadow: 0 10px 20px -8px #fa436a;
    box-shadow: 1px 2px 5px rgb(219 63 96 / 40%);
    background: linear-gradient(90deg, #ffac30, #fa436a, #f56c6c);
    font-size: 14px;
    padding: 0 20px;
    color: #fff;
  }
}
</style>
