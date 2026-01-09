<template>
  <div style="width: 100%; min-height: 100vh; background: rgb(237, 241, 255)">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" :title="type == 1 ? '修改登录密码' : '设置提现密码'" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
    <div class="usrse">

      <div class="hgs">
        <div class="nams">原密码</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="passwordInfo.password" type="password" placeholder="请输入当前密码" />
          </van-cell-group>
        </div>
      </div>
      <div class="hgs">
        <div class="nams">新密码</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="passwordInfo.paypassword" type="password" placeholder="请输入新密码" />
          </van-cell-group>
        </div>
      </div>
            <div class="hgs">
        <div class="nams">确认新密码</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="passwordInfo.newpasword" type="password" placeholder="请再次输入密码" />
          </van-cell-group>
        </div>
      </div>
      <van-button type="info" style="margin-top: 20px; width: 100%" @click="editPassword(type)">确认修改</van-button>
      <div style="height: 60px"></div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'password',
  data() {
    return {
      passwordInfo: {},
      type: 1,
      psw1: true,
      psw2: true,
      psw3: true,
    };
  },
  created() {
    let that = this;
    let query = that.$route.query;

    if (query.type) {
      that.type = query.type * 1;
    }
  },
  methods: {
    changPsw(name) {
      this[name] = !this[name];
    },
    changtab() {
      let that = this;
      that.passwordInfo = {};
      that.info = {};
    },
    editPassword(pasType) {
      let that = this;
      if (!that.passwordInfo.password) {
        that.$parent.showTost(0, '请输入旧密码');
        return;
      }
      if (!that.passwordInfo.paypassword) {
        that.$parent.showTost(0, '请输入新密码');
        return;
      }
      if (that.passwordInfo.password.length < 6) {
        that.$parent.showTost(0, '请输入正确的旧密码长度');
        return;
      }
      if (that.passwordInfo.paypassword.length < 6) {
        that.$parent.showTost(0, '请输入正确的新密码长度');
        return;
      }
      if (!that.passwordInfo.newpasword) {
        that.$parent.showTost(0, '请输入确认密码');
        return;
      }
      if (that.passwordInfo.newpasword != that.passwordInfo.paypassword) {
        that.$parent.showTost(0, '两次密码不一致！');
        return;
      }
      if (that.passwordInfo.password == that.passwordInfo.paypassword) {
        that.$parent.showTost(0, '新旧密码不能一致！');
        that.passwordInfo = {};
        return;
      }
      let url = pasType == 1 ? '/api/editPassword' : '/api/editPayPassword';

      that.$parent.showLoading();
      that.$apiFun.post(url, {paypassword:that.passwordInfo.paypassword,password:that.passwordInfo.password}).then(res => {
        console.log(res);
        if (res.code != 200) {
          that.$parent.showTost(0, res.message);
        }
        that.$parent.hideLoading();
        if (res.code == 200) {
          that.$parent.showTost(1, '密码修改成功！');
          that.passwordInfo = {};
          if (pasType == 1) {
            that.$parent.closeDaoTime();
            localStorage.clear();
            sessionStorage.clear();
            that.$store.commit('changUserInfo');
            that.$store.commit('changToken');
            that.$router.push({ path: '/login' });
          }
        }
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
.usrse {
  background: #fff;
  box-sizing: border-box;
  padding: 6px 20px 0;
  .nams {
    font-size: 0.38rem;
    color: #000;
    vertical-align: middle;
    margin-top: 10px;
    font-weight: 700;
  }
  .imgsa {
    position: relative;
    height: 2rem;
    border-bottom: 1px solid #f2f2f2;
    padding-bottom: 0.2rem;
    .bisn {
      width: 0.8rem;
      position: absolute;
      bottom: 0.3rem;
      left: 1.4rem;
    }
    img {
      width: 2rem;
      border-radius: 50%;
    }
  }
}
[class*='van-hairline']:after {
  border: none;
}
</style>
