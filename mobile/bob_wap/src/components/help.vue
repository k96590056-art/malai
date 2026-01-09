<template>
  <div data-v-f531b812="" class="app app-ti_green">
    <div data-v-8a75a126="" data-v-f531b812="" class="header">
      <div data-v-8a75a126="" class="header__top-wrapper">
        <div data-v-8a75a126="" class="van-nav-bar van-nav-bar--fixed fixed-top nav-header">
          <div class="van-nav-bar__content">
            <div class="van-nav-bar__left" @click="$router.back()">
              <i class="van-icon van-icon-arrow-left van-nav-bar__arrow"></i>
            </div>
            <div class="van-nav-bar__title van-ellipsis">帮助中心</div>
          </div>
        </div>
      </div>
    </div>
    <van-tabs v-model="type">
      <van-tab title="如何注册" name="0">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container">
            <div data-v-5de375fa="" class="help-center-register__article">
              <div data-v-7b40e3b3="" data-v-5de375fa="" class="main-container-content">
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">欢迎来到平台，我们致力于通过精良的技术、前卫的产品，成为亚洲博彩业的领导品牌</div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">步骤如下</div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第一步：在网站或App上直接点击注册；</div>
              </div>
            </div>
            <div data-v-5de375fa="" class="help-center-register__article">
              <div data-v-32de3775="" data-v-5de375fa="" class="main-container-title">手机号码注册</div>
              <div data-v-7b40e3b3="" data-v-5de375fa="" class="main-container-content">
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第二步：按照提示填写资料； <span data-v-5de375fa="" data-v-7b40e3b3="" class="red">手机号码注册-输入手机号码，并验证码手机号码。 </span></div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第三步：按照提示填写资料； <span data-v-5de375fa="" data-v-7b40e3b3="" class="red">设置登录密码。</span></div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第四步：设置账户昵称。</div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第五步：注册成功。</div>
              </div>
            </div>
            <div data-v-5de375fa="" class="help-center-register__article">
              <div data-v-32de3775="" data-v-5de375fa="" class="main-container-title">账户名注册</div>
              <div data-v-7b40e3b3="" data-v-5de375fa="" class="main-container-content">
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第二步：按照提示填写资料；； <span data-v-5de375fa="" data-v-7b40e3b3="" class="red">账户名注册-输入账户名和密码。。</span></div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第三步：设置账户昵称</div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">第四步：注册成功</div>
              </div>
            </div>
            <div data-v-5de375fa="" class="help-center-register__article">
              <div data-v-32de3775="" data-v-5de375fa="" class="main-container-title">备注</div>
              <div data-v-7b40e3b3="" data-v-5de375fa="" class="main-container-content">
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">1.请注意，注册时要求填写真实而准确的信息。</div>
                <div data-v-5de375fa="" data-v-7b40e3b3="" class="main-container-content__text">2.用户使用网站前，请务必先阅读规则与条款，并确定已年满18岁。</div>
              </div>
            </div>
          </div>
        </div>
      </van-tab>
      <van-tab title="常见问题" name="1">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox1.content"></div>
        </div>
      </van-tab>
      <van-tab title="隐私政策" name="2">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox2.content"></div>
        </div>
      </van-tab>
      <van-tab title="免责说明" name="3">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox3.content"></div>
        </div>
      </van-tab>
      <van-tab title="联系我们" name="4">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox4.content"></div>
        </div>
      </van-tab>
      <van-tab title="代理加盟" name="5">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox5.content"></div>
        </div>
      </van-tab>
      <van-tab title="博彩责任" name="7">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox8.content"></div>
        </div>
      </van-tab>
      <van-tab title="关于我们" name="6">
        <div data-v-5de375fa="" data-v-033e62fa="" class="help-center-register helpcreg_green">
          <div data-v-5de375fa="" class="help-center-register__container" v-html="dataBox7.content"></div>
        </div>
      </van-tab>
    </van-tabs>

    <div data-v-f531b812="" class="float-divbox"></div>
    <span data-v-7b0f8a3e="" data-v-f531b812="" class="customer-service-container"></span><span data-v-f531b812=""></span>
    <div data-v-55ec3770="" data-v-f531b812="" class="select-service-line-view select-service-line-view">
      <dl data-v-55ec3770="" class="select-service-list"><div data-v-55ec3770="" style="height: 55px"></div></dl>
    </div>
  </div>
</template>
<script>
export default {
  name: 'help',
  data() {
    return {
      url: null,
      // 1常见问题  2隐私政策  3免责说明  4联系我们  5代理加盟  7关于我们 8博彩责任
      type: 0,
      dataBox1: {},
      dataBox2: {},
      dataBox3: {},
      dataBox4: {},
      dataBox5: {},
      dataBox7: {},
      dataBox8: {},
    };
  },
  created() {
    let that = this;
    let query = that.$route.query;
    if (query.type) {
      that.type = query.type * 1;
    }
    let arr = [1, 2, 3, 4, 5, 7, 8];
    arr.forEach(el => {
      that.getAllCont(el);
    });
  },
  methods: {
    getAllCont(type) {
      let that = this;
      that.$parent.showLoading();

      that.$apiFun
        .post('/api/article', { type })
        .then(res => {
          let name = 'dataBox' + type;
          that[name] = res.data;
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
};
</script>

<style lang="scss" scoped>
@import '../../static/css/helpCenter.348756ae.css';
.body {
  background: #f6f7f8 !important;
  width: 100%;
  min-height: 100vh;
}
</style>
