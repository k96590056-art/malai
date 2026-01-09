<template>
  <div class="acts">
    <div class="pageTop">赞助</div>
    <div style="height: 30px"></div>
    <div class="lis" @click="$parent.goNav('/zhanzhuye?type=1')">
        <img style="width: 100%;" src="/static/image/01.png">
    </div>
    <div class="lis" @click="$parent.goNav('/zhanzhuye?type=2')">
        <img style="width: 100%;" src="/static/image/02.png">
    </div>
    <div v-if="1==2" class="lis" @click="$parent.goNav('/zhanzhuye?type=1')">
      <div class="lfs">
        <img src="/static/image/ddf471901f2b4fff9ee57015a1698227.png" />
        <div class="you">尤文图斯</div>
        <div class="te">官方区域合作伙伴</div>
      </div>
      <div class="rig">
        <img src="/static/image/93b000fa1d3246ce9b90a62c018714af.png" alt="" />
      </div>
    </div>
     <div v-if="1==2" class="lis" @click="$parent.goNav('/zhanzhuye?type=2')">
      <div class="lfs">
        <img src="/static/image/ddf471901f2b4fff9ee57015a1698227.png" />
        <div class="you">阿斯顿维拉</div>
        <div class="te">官方全球顶级合作伙伴</div>
      </div>
      <div class="rig">
        <img src="/static/image/bd72c14c428d41ce8105a0d82a1bb696.png" alt="" />
      </div>
    </div>
      <van-divider dashed :style="{ color: '#ccc', borderColor: '#ccc', padding: '20px 100px' }">没有更多了~</van-divider>

  </div>
</template>
<script>
export default {
  name: 'zhanzhu',
  data() {
    return {};
  },
  created() {
    let that = this;
  },
  methods: {},
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
.acts {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
}
.lis {
  display: flex;
  width: calc(100% - 40px);
  margin: 0 auto;
  margin-top: 20px;
  box-sizing: border-box;

  border-radius: 20px;
  overflow: hidden;
  background-color: #f8f9ff;
  -webkit-box-shadow: 0 0.04rem 0.2rem rgb(199 212 255 / 42%);
  box-shadow: 0 0.04rem 0.2rem rgb(199 212 255 / 42%);
  border: 0.02rem solid #fff;
  .lfs {
    height: 100%;
    width: 45%;
    text-align: center;
    color: #98a8c5;
    img {
      width: 30px;
      display: block;
      margin: 0 auto;
      margin-top: 20px;
    }
    .you {
      font-weight: 700;
      font-size: 18px;
      margin-top: 10px;
    }
    .te {
      font-size: 12px;
      margin-top: 5px;
    }
  }
  .rig {
    width: 55%;
    font-size: 0;
    img {
      width: 100%;
    }
  }
}
</style>
