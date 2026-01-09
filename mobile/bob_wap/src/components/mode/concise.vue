<template>
  <div style="width: 100%; min-height: 100vh; background: rgb(237, 241, 255">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
    <img src="/static/image/73b07f2.jpg" style="width: 100%; border-radius: 20px" alt="" />
    <van-tabs v-if="1==2" v-model="gamecode" @change="handleClick" class="gameBox">
      <van-tab v-if="1==2" title="OB电子" name="obgdy">
        <div class="gameList">
          <div class="gameLis" v-for="(item, index) in obgdyList" :key="index" @click="$parent.openGamePage(item.catecode, item.gamecode, '')">
            <img :src="item.gamepic" alt="" />
            <p>{{ item.gamename }}</p>
          </div>
        </div>
      </van-tab>
      <van-tab v-if="1==2" title="FG电子" name="fgdz">
        <div class="gameList">
          <div class="gameLis" v-for="(item, index) in fgdzList" :key="index" @click="$parent.openGamePage(item.catecode, item.gamecode, '')">
            <img :src="item.gamepic" alt="" />
            <p>{{ item.gamename }}</p>
          </div>
        </div></van-tab>
      <!-- 天美社区源码网 timibbs.net timibbs.com timibbs.vip -->
      <van-tab v-if="1==2" title="PP电子" name="pp">
        <div class="gameList">
          <div class="gameLis" v-for="(item, index) in ppList" :key="index" @click="$parent.openGamePage(item.catecode, item.gamecode, '')">
            <img :src="item.gamepic" alt="" />
            <p>{{ item.gamename }}</p>
          </div>
        </div></van-tab>
      <van-tab v-if="1==2" title="AE电子" name="ae">
        <div class="gameList">
          <div class="gameLis" v-for="(item, index) in aeList" :key="index" @click="$parent.openGamePage(item.catecode, item.gamecode, '')">
            <img :src="item.gamepic" alt="" />
            <p>{{ item.gamename }}</p>
          </div>
        </div></van-tab>
        <van-tab :title="JDB电子" name="jdb">
        <div class="gameList">
          <div class="gameLis" v-for="(item, index) in jdbList" :key="index" @click="$parent.openGamePage(item.catecode, item.gamecode, '')">
            <img :src="item.gamepic" alt="" />
            <p>{{ item.gamename }}</p>
          </div>
        </div></van-tab>
    </van-tabs>
    <div class="gameList">
          <div class="gameLis" v-for="(item, index) in jdbList" :key="index" @click="$parent.openGamePage(item.catecode, item.gamecode, '')">
            <img :src="item.gamepic" alt="" />
            <p>{{ item.gamename }}</p>
          </div>
        </div>
  </div>
</template>
<script>
export default {
  name: 'concise',
  data() {
    return { gamecode: 'obgdy', jdbList:[], obgdyList: [], ppList: [], fgdzList: [], aeList: [] };
  },
  created() {
    let that = this;
    let query = that.$route.query;
    if (query.type) {
      that.gamecode = query.type;
    }
    console.log(query);
    that.gamelistBycode();
  },
  methods: {
    handleClick() {
      this.gamelistBycode();
    },
    gamelistBycode() {
      let that = this;

      let name = that.gamecode + 'List';
      if (that[name].length > 0) {
        return;
      }
      that.$parent.showLoading();
      that.$apiFun
        .post('/api/gamelistBycode', { gamecode: that.gamecode })
        .then(res => {
          console.log(res);
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that[name] = res.data;
          }
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
  watch: {
    //监听路由地址的改变
    $route: {
      immediate: true,
      handler() {
        let that = this;
        let query = this.$route.query;
        if (query.type) {
          that.gamecode = query.type;
          that.gamelistBycode();
        }
      },
    },
  },
};
</script>
<style lang="scss" scoped>
// @import '../../../static/css/announcement_modal.css';
.gameBox {
  width: 90%;
  margin: 8px auto;
  height: auto;
  padding-bottom: 100px;
}
.gameList {
  display: flex;
  flex-wrap: wrap;
  .gameLis {
    position: relative;

    width: calc(25% - 8px);
    margin-right: 8px;
    margin-top: 8px;
    cursor: pointer;
    img {
      display: block;
      width: 90%;
      margin: 0 auto;
    }
    p {
      text-align: center;
      font-size: 8px;
    }
  }
}
.gameLisps {
  position: absolute;
  bottom: 15px;
  left: 0;
  width: 100%;
}
</style>
