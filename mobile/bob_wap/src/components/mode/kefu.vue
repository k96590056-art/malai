<template>
  <div class="kefu-page">
    <div v-if="url" class="kefu-iframe-container">
      <!-- <iframe style="height: 100%; width: 100%"  ref="iframe" scrolling="auto" frameborder="0"  id="iframe"></iframe> -->
      <iframe style="height: calc(100% - 1.5rem); width: 100%"  ref="iframe" scrolling="auto" frameborder="0"  id="iframe"></iframe>
    </div>
    <div v-else class="kefu-loading">
      <div>加载中...</div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'kefu',
  data() {
    return {
      url: null,
    };
  },
  created() {
    let that = this;
    
      that.getservicerurl();
  
  },
  methods: {
    // 打开客服
    getservicerurl() {
      let that = this;
      that.$apiFun.post('/api/getservicerurl', {}).then(res => {
        if (res.code != 200) {
          that.showTost(0, res.message);
        }
        if (res.code == 200) {
          that.url = res.data.url;
        }
      });
    },
   
   
  },
  mounted() {
    let that = this;
  },
  updated() {
    let that = this;
    that.$refs.iframe.contentWindow.location.replace(that.url);
  },
};
</script>

<style lang="scss" scoped>
.kefu-page {
  min-height: calc(100vh - 60px);
  padding-bottom: 60px;
}

.kefu-iframe-container {
  height: calc(100vh - 60px);
  overflow-y: scroll;
  -webkit-overflow-scrolling: touch;
}

.kefu-loading {
  min-height: calc(100vh - 60px);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-size: 14px;
}
</style>
