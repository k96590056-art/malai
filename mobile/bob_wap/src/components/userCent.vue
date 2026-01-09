<template>
  <div style="width: 100%; min-height: 100vh; background: #f1f1f1">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="个人资料" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
    <div class="tops">
      <img src="/static/image/safety.d3a323b5ad7cca95958707791f3861b1.png" alt="" />
      <div class="tes">完善账户信息，更安全</div>
    </div>
    <div class="boxst">
      <div class="hgs" @click="$parent.goNav('/userInfo')">
        <div class="lfs">
          <div class="topas">完善个人资料</div>
          <div class="tisg">资料更完整，我们的服务更加周到</div>
        </div>
        <div class="rigs">去完善 <img src="/static/image/right.b9a9c7c64558347505384ad01922580c.png" alt="" /></div>
      </div>
      <div class="hgs" @click="$parent.goNav('/wallet')">
        <div class="lfs">
          <div class="topas">卡片管理</div>
          <div class="tisg">如需提现，请绑定银行卡或虚拟币地址</div>
        </div>
        <div class="rigs"><img src="/static/image/right.b9a9c7c64558347505384ad01922580c.png" alt="" /></div>
      </div>
      <div class="hgs" @click="$parent.goNav('/password?type=1')">
        <div class="lfs">
          <div class="topas">登录密码管理</div>
          <div class="tisg">定期修改登录密码，有利账户安全</div>
        </div>
        <div class="rigs"><img src="/static/image/right.b9a9c7c64558347505384ad01922580c.png" alt="" /></div>
      </div>
      <div class="hgs" @click="$parent.goNav('/password?type=2')">
        <div class="lfs">
          <div class="topas">取款密码管理</div>
          <div class="tisg">定期修改登录密码，有利账户安全</div>
        </div>
        <div class="rigs"><img src="/static/image/right.b9a9c7c64558347505384ad01922580c.png" alt="" /></div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'userCent',
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
};
</script>

<style lang="scss" scoped>
// @import '../../static/css/chunk-764158fc.acb18eaa.css';

.tops {
  background: url(/static/image/safety_back.577bcf768c07205d5e3e2c7949d5677c.577bcf76.png) no-repeat;
  background-size: 100% 100%;
  width: 100%;
  height: 5rem;
  box-sizing: border-box;
  padding: 30px 0;
  min-height: 200px;
  img {
    width: 3rem;
    display: block;
    margin: 0 auto;
  }
  .tes {
    color: #fff;
    font-size: 0.3rem;
    text-align: center;
    padding-top: 15px;
  }
}

.boxst {
  width: 90%;
  background: #fff;
  border-radius: 20px;
  position: relative;
  margin: 0 auto;
  margin-top: -10px;
  box-sizing: border-box;
  padding: 20px;

  .hgs {
    border-bottom: 1px solid #f1f1f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 1.3rem;
    min-height: 50px;
    .lfs {
      flex: 1;
      .topas {
        font-size: 0.3rem;
      }
      .tisg {
        margin-top: 4px;
        color: #999;
        font-size: 0.3rem;
      }
    }
    .rigs {
      display: flex;
      align-items: center;
      color: #999;
      font-size: 0.3rem;

      img {
        width: 0.34rem;
        margin-left: 4px;
      }
    }
  }
}
</style>
