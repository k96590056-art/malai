<template>

</template>

<script>
export default {
  name: 'register',
  data() {
    return {
      registerInfo: {},check_agree:true
    };
  },
  created() {
    let that = this;
  },
  methods: {
    register() {
      let that = this;

      let info = that.registerInfo;
      console.log(info);
 
      if (!that.check_agree) {
        that.$parent.showTost(0, '请阅读并同意相关条款和隐私协议！');
        return;
      }
      if (!info.name || info.name.length < 6) {
        that.$parent.showTost(0, ' 用户名长度6~16位，以字母或数字组合！');
        return;
      }
      if (!info.password || info.password.length < 6) {
        that.$parent.showTost(0, '请输入正确的密码长度，最少6位！');
        return;
      }
      if (!info.confirmPass || info.confirmPass != info.password) {
        that.$parent.showTost(0, '两次密码不一致！');
        return;
      }
      if (!info.realname || info.realname.length < 2) {
        that.$parent.showTost(0, '请输入您的真实姓名!');
        return;
      }

      if (!info.paypassword || info.paypassword.length < 6) {
        that.$parent.showTost(0, '请输入正确的支付密码长度，最少6位！');
        return;
      }
      that.$parent.showLoading();
      that.$apiFun.register(info).then(res => {
        that.$parent.showTost(1, res.message);
        if (res.code == 200) {
          sessionStorage.setItem('token', res.data.api_token);
          // that.$cookies.set('token', res.data.api_token)

          that.$store.commit('changToken');
          that.getUserInfo();
          that.$parent.openDaoTime();
        } else {
          that.$parent.hideLoading();
        }
      });
    },
    getUserInfo() {
      let that = this;
      that.$apiFun.post('/api/user', {}).then(res => {
        console.log(res);
        if (res.code !== 200) {
          that.$parent.showTost(0, res.message);
        }
        if (res.code === 200) {
          localStorage.setItem('userInfo', JSON.stringify(res.data));
          that.$store.commit('changUserInfo');
          that.$router.push({ path: '/' });
        }
        that.$parent.hideLoading();
      });
    },
  },
  mounted() {
 
    
  },
  updated() {
    let that = this;
  },
  beforeDestroy() {},
};
</script>
<style lang="scss" scoped>
// @import '../../static/css/registermember.css';

</style>
