<template>
  <div style="width: 100%; min-height: 100vh; background: #f1f1f1">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="个人资料" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
    <div class="usrse">
      <div class="hgs">
        <div class="nams">个人头像</div>
        <div class="imgsa">
          <img mode="aspectFill" :src="$store.state.userInfo.avatar ? $store.state.userInfo.avatar : '/static/image/imageAvatar02@3x.png'" alt="" />
          <input class="inputsw" type="file" @change="onchangemd" single accept="image/gif,image/png" />
          <img class="bisn" mode="aspectFill" src="/static/image/avatarEdit.cf65ea838bb7aba043f461f551f740ac.png" />
        </div>
      </div>
      <div class="hgs">
        <div class="nams">用户名</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="$store.state.userInfo.username" placeholder="请输入用户名" disabled />
          </van-cell-group>
        </div>
      </div>
      <div class="hgs">
        <div class="nams">真实姓名</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="$store.state.userInfo.realname" placeholder="请输入真实姓名" disabled />
          </van-cell-group>
        </div>
      </div>
      <div class="hgs" @click="changShow">
        <div class="nams">出生日期</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="birthday" placeholder="请选择出入日期" disabled />
          </van-cell-group>
        </div>
      </div>

      <div class="hgs">
        <div class="nams">手机号码</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="mobile" placeholder="绑定手机号，保障账号安全" />
          </van-cell-group>
        </div>
      </div>
      <div class="hgs">
        <div class="nams">电子邮箱</div>
        <div style="border-bottom: 1px solid #f2f2f2">
          <van-cell-group>
            <van-field v-model="email" placeholder="绑定邮箱保护账号安全" />
          </van-cell-group>
        </div>
      </div>
      <van-button type="info" style="margin-top: 20px; width: 100%" @click="isOk">确认修改</van-button>
    </div>
    <van-datetime-picker style="position: fixed; width: 100%; bottom: 0px; left: 0; background: #f1f1f1" type="date" v-if="showPicker" :min-date="minDate" @confirm="onConfirm" @cancel="changShow" />
  </div>
</template>
<script>
export default {
  name: 'userInfo',
  data() {
    return {
      mobile: null,
      email: null,
      birthday: null,
      showPicker: false,
      minDate: new Date(1980, 0, 1),
    };
  },
  created() {
    let that = this;
    let userInfo = JSON.parse(localStorage.getItem('userInfo'));
    that.mobile = userInfo.mobile;
    that.email = userInfo.email;
    that.birthday = userInfo.birthday;
  },
  methods: {
    onchangemd(e) {
      let that = this;

      console.log(e.target.files); //这个就是选中文件信息
      let formdata = new FormData();
      Array.from(e.target.files).map(item => {
        console.log(item);
        formdata.append('file', item); //将每一个文件图片都加进formdata
      });
      //  axios.post("接口地址", formdata).then(res => { console.log(res) })
      that.$parent.showLoading();

      that.$apiFun.post('/api/uploadimg', formdata).then(res => {
        that.$parent.hideLoading();

        that.$parent.getUserInfoShowLoding();
      });
    },
    timeFormat(time) {
      var time = new Date(time.getTime() + 8 * 60 * 60 * 1000);
      var myDate = time.toJSON().split('T').join(' ').substr(0, 10);
      return myDate;
    },
    changShow() {
      this.showPicker = !this.showPicker;
      console.log(123);
    },
    onConfirm(time) {
      let that = this;
      this.birthday = that.timeFormat(time);
      this.showPicker = false;
    },
    isOk() {
      let that = this;
      let info = { email: that.email, mobile: that.mobile, birthday: that.birthday };
      console.log(that.birthday);
      let regExp = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
      let regEmail = /^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/;
      let dateFormat = /^(\d{4})-(\d{2})-(\d{2})$/;
      if (!regExp.test(that.mobile)) {
        that.$parent.showTost(0, '请输入正确手机号');
        return;
      }
      if (!regEmail.test(that.email)) {
        that.$parent.showTost(0, '请输入正确邮箱号');
        return;
      }
      if (!dateFormat.test(that.birthday)) {
        that.$parent.showTost(0, '请输入正确的日期格式：YYYY-MM-DD');
        return;
      }

      that.$parent.showLoading();
      that.$apiFun
        .post('/api/updateuserinfo', info)
        .then(res => {
          console.log(res);
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            let userInfo = JSON.parse(localStorage.getItem('userInfo'));
            userInfo.mobile = info.mobile;
            userInfo.email = info.email;
            userInfo.birthday = info.birthday;
            localStorage.setItem('userInfo', JSON.stringify(userInfo));
            that.$parent.getUserInfo();
            that.$parent.showTost(1, '操作成功');
          }
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
        });
    },
    onchangemd(e) {
      let that = this;

      console.log(e.target.files); //这个就是选中文件信息
      let formdata = new FormData();
      Array.from(e.target.files).map(item => {
        console.log(item);
        formdata.append('file', item); //将每一个文件图片都加进formdata
      });
      //  axios.post("接口地址", formdata).then(res => { console.log(res) })
      that.$parent.showLoading();
      that.$apiFun.post('/api/uploadimg', formdata).then(res => {
        that.$parent.getUserInfoShowLoding();
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
// @import '../../static/css/bf324575ed6355910127.css';
.usrse {
  margin-top: 10px;
  background: #fff;
  box-sizing: border-box;
  padding: 20px;
  .nams {
    font-size: 0.38rem;
    color: #000;
    vertical-align: middle;
    margin-top: 10px;
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
