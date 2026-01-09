<template>
  <div class="mobile-login-container">
    <!-- 顶部Logo -->
    <div class="login-header">
      <div class="login-logo">
        <img 
          :src="$store.state.appInfo.site_logo || '/static/image/uacPoGJlb02AMGnUAAAYLvRuglw960.png'" 
          alt="Logo" 
          class="logo-img"
          @error="handleLogoError"
        />
      </div>
    </div>

    <!-- 登录表单 -->
    <div class="login-form-wrapper">
      <div class="login-form">
        <!-- 账号输入 -->
        <div class="input-group">
          <input 
            v-model="formData.name" 
            type="text" 
            placeholder="账号" 
            class="login-input"
            maxlength="32"
            autocomplete="off"
          />
        </div>
        
        <!-- 密码输入 -->
        <div class="input-group password-group">
          <input 
            v-model="formData.password" 
            :type="psw1 ? 'password' : 'text'" 
            placeholder="密码" 
            class="login-input"
            maxlength="32"
            autocomplete="off"
          />
          <div class="password-toggle" @click="changPsw('psw1')">
            <span v-if="psw1">👁️</span>
            <span v-else>👁️‍🗨️</span>
          </div>
        </div>

        <!-- 确认密码输入（仅注册模式显示） -->
        <div class="input-group password-group" v-if="!isLogin">
          <input 
            v-model="formData.confirmPass" 
            :type="psw2 ? 'password' : 'text'" 
            placeholder="确定密码" 
            class="login-input"
            maxlength="32"
            autocomplete="off"
          />
          <div class="password-toggle" @click="changPsw('psw2')">
            <span v-if="psw2">👁️</span>
            <span v-else>👁️‍🗨️</span>
          </div>
        </div>

        <!-- 记住密码和忘记密码（仅登录模式显示） -->
        <div class="login-options" v-if="isLogin">
          <label class="remember-password">
            <input type="checkbox" v-model="rememberPassword" />
            <span>记住密码</span>
          </label>
          <a href="#" class="forgot-password" @click.prevent="handleForgotPassword">忘记密码?</a>
        </div>

        <!-- 同意条款（仅注册模式显示） -->
        <div class="agree-terms" v-if="!isLogin">
          <label class="agree-checkbox">
            <input type="checkbox" v-model="agreeTerms" />
            <span class="checkbox-custom"></span>
          </label>
          <span class="agree-text">
            我已阅读并同意
            <a href="#" class="terms-link" @click.prevent="handleTermsClick">相关条款</a>
            和
            <a href="#" class="terms-link" @click.prevent="handlePrivacyClick">隐私政策</a>
          </span>
        </div>

        <!-- 登录/注册按钮 -->
        <button class="login-btn" @click="submitForm">
          {{ isLogin ? '登录' : '注册' }}
        </button>
      </div>
    </div>
    <!-- 底部按钮 -->
    <div class="bottom-actions">
      <button class="action-btn register-btn" @click="toggleLoginMode">
        <img src="/static/image/diy/login_btn1.png" :alt="isLogin ? '立即注册' : '前往登录'" class="btn-image" />
        <span class="btn-text">{{ isLogin ? '立即注册' : '前往登录' }}</span>
      </button>
      <button class="action-btn guest-btn" @click="$parent.goNav('/')">
        <img src="/static/image/diy/login_btn2.png" alt="游客进入" class="btn-image" />
        <span class="btn-text">游客进入</span>
      </button>
      <button class="action-btn service-btn" @click="$parent.openKefu">
        <img src="/static/image/diy/login_btn3.png" alt="在线客服" class="btn-image" />
        <span class="btn-text">在线客服</span>
      </button>
    </div>
    
    <!-- 验证码组件 -->
    <ClickCaptcha
      :visible="showCaptcha"
      title="请在下图依次点击"
      brand=""
      :pointCount="3"
      :generateUrl="generateUrl"
      :verifyUrl="verifyUrl"
      @success="onCaptchaSuccess"
      @close="showCaptcha = false"
    />
    
    <!-- 忘记密码弹窗 -->
    <van-dialog
      v-model="showForgotPasswordDialog"
      title="提示"
      :show-cancel-button="true"
      :show-confirm-button="true"
      confirm-button-text="立即联系"
      cancel-button-text="再想想"
      @confirm="handleContactService"
      @cancel="handleCancelForgotPassword"
    >
      <div class="forgot-password-content">
        密码丢失请联系在线客服
      </div>
    </van-dialog>
  </div>
</template>

<script>
import ClickCaptcha from '@/components/libs/ClickCaptcha.vue'
export default {
  name: 'login',
  components: { ClickCaptcha },
  data() {
    return {
      showCaptcha: false,
      generateUrl: '/api/captcha/generate',
      verifyUrl: '/api/captcha/verify',
      formData: {
        name: '',
        password: '',
        confirmPass: '',
        realname: '',
        paypassword: '',
      },
      isLogin: true,
      psw1: true,
      psw2: true,
      psw3: true,
      pid: '',
      rememberPassword: false,
      agreeTerms: false,
      showForgotPasswordDialog: false, // 忘记密码弹窗显示状态
    };
  },
  created() {
    let that = this;
    var query = that.$route.query;
    if (query.type) {
      that.isLogin = query.type === '0';
    }
    if (query.pid) {
      that.pid = query.pid;
    }
  },
  methods: {
    changPsw(name) {
      this[name] = !this[name];
    },
    toggleLoginMode() {
      // 切换登录/注册模式
      this.isLogin = !this.isLogin;
      // 切换时清空确认密码
      if (this.isLogin) {
        this.formData.confirmPass = '';
      }
    },
    handleForgotPassword() {
      // 显示忘记密码弹窗
      this.showForgotPasswordDialog = true;
    },
    handleContactService() {
      // 立即联系客服，跳转到客服页面
      this.showForgotPasswordDialog = false;
      this.$parent.goNav('/kefu');
    },
    handleCancelForgotPassword() {
      // 取消，关闭弹窗，继续输入用户名密码
      this.showForgotPasswordDialog = false;
    },
    handleTermsClick() {
      // 处理相关条款点击
      this.$parent.showTost(0, '相关条款功能开发中');
    },
    handlePrivacyClick() {
      // 处理隐私政策点击
      this.$parent.showTost(0, '隐私政策功能开发中');
    },
    handleLogoError(e) {
      // Logo加载失败时的处理
      console.error('Logo加载失败:', e.target.src);
      // 可以设置一个默认logo或者隐藏图片
      e.target.style.display = 'none';
    },
    onCaptchaSuccess(sessionId) {
      if (this.isLogin) {
        this.doLogin(sessionId);
      } else {
        this.doRegister(sessionId);
      }
    },
    submitForm() {
      let that = this;
      let info = that.formData;
      
      if (!info.name || !info.password) {
        that.$parent.showTost(0, '请输入您的账号和密码！');
        return;
      }
      
      // 注册模式需要验证确认密码和同意条款
      if (!that.isLogin) {
        if (!info.confirmPass) {
          that.$parent.showTost(0, '请输入确认密码！');
          return;
        }
        if (info.password !== info.confirmPass) {
          that.$parent.showTost(0, '两次输入的密码不一致！');
          return;
        }
        if (!that.agreeTerms) {
          that.$parent.showTost(0, '请先阅读并同意相关条款和隐私政策！');
          return;
        }
      }
      
      // 验证通过，显示验证码
      this.showCaptcha = true;
    },
    doRegister(sessionId) {
      let that = this;
      let info = that.formData;
      
      if (sessionId) {
        info.captcha_id = sessionId;
      }

      that.$parent.showLoading();
      if (that.pid) {
        info.pid = that.pid;
      }
      
      that.$apiFun.register(info).then(res => {
        that.$parent.hideLoading();
        if (res.code == 200) {
          // 检查返回数据中是否有 api_token
          const apiToken = res.data && res.data.api_token ? res.data.api_token : null;
          
          if (apiToken) {
            // 保存 token 并自动登录
            sessionStorage.setItem('token', apiToken);
            that.$store.commit('changToken');
            that.$parent.showTost(1, res.message || '注册成功');
            // 获取用户信息
            that.$parent.getUserInfo();
            // 打开倒计时
            that.$parent.openDaoTime();
            // 跳转到首页
            setTimeout(() => {
              that.$parent.goNav('/');
            }, 500);
          } else {
            // 如果没有 token，可能需要重新登录
            that.$parent.showTost(0, '注册成功，请登录');
            // 切换到登录模式
            that.isLogin = true;
            // 清空密码字段
            that.formData.password = '';
            that.formData.confirmPass = '';
          }
        } else {
          that.$parent.showTost(0, res.message || '注册失败');
        }
      }).catch(err => {
        that.$parent.hideLoading();
        console.error('注册错误:', err);
        that.$parent.showTost(0, '注册失败，请重试');
      });
    },
    doLogin(sessionId) {
      let that = this;
      let info = {
        name: that.formData.name,
        password: that.formData.password
      };
      
      if (sessionId) {
        info.captcha_id = sessionId;
      }

      that.$parent.showLoading();
      that.$apiFun.login(info).then(res => {
        if (res.code !== 200) {
          that.$parent.showTost(0, res.message);
          that.$parent.hideLoading();
        }
        if (res.code === 200) {
          sessionStorage.setItem('token', res.data.api_token);
          that.$store.commit('changToken');
          that.$parent.getUserInfo();
          that.$parent.openDaoTime();
          that.$parent.goNav('/');
        }
        that.$parent.hideLoading();
      });
    },
  },
  mounted() {
    // this.refresh();
  }
};
</script>

<style lang="scss" scoped>
.mobile-login-container {
  width: 100vw;
  min-height: 100vh;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  background-image: url('/static/image/diy/login_bg.jpg');
  background-size: cover;
  background-position: center;
  background-blend-mode: overlay;
  padding: 60px 15px 20px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

// 顶部Logo
.login-header {
  text-align: center;
  padding: 30px 0 40px;
  
  .login-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    
    .logo-img {
      width: 60%;
      display: block;
      object-fit: contain;
    }
  }
}

// 登录表单
.login-form-wrapper {
  margin-bottom: 0.5rem;
}

.login-form {
  .input-group {
    position: relative;
    margin-bottom: 15px;
    
    .login-input {
      width: 100%;
      height: 50px;
      padding: 0 15px;
      background: rgba(200, 200, 200, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
      font-size: 16px;
      color: #ffffff;
      box-sizing: border-box;
      
      &::placeholder {
        color: rgba(255, 255, 255, 0.6);
      }
      
      &:focus {
        outline: none;
        border-color: #1890ff;
        background: rgba(200, 200, 200, 0.4);
      }
    }
    
    &.password-group {
      .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 20px;
      }
    }
  }
  
  .login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
    
    .remember-password {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #ffffff;
      font-size: 14px;
      cursor: pointer;
      
      input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #1890ff;
        cursor: pointer;
      }
    }
    
    .forgot-password {
      color: #ffffff;
      font-size: 14px;
      text-decoration: none;
      
      &:hover {
        text-decoration: underline;
      }
    }
  }
  
  .agree-terms {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
    
    .agree-checkbox {
      position: relative;
      display: inline-block;
      cursor: pointer;
      
      input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        
        &:checked + .checkbox-custom {
          background: #1890ff;
          border-color: #1890ff;
          
          &::after {
            display: block;
          }
        }
      }
      
      .checkbox-custom {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 4px;
        background: transparent;
        position: relative;
        flex-shrink: 0;
        
        &::after {
          content: '';
          position: absolute;
          left: 5px;
          top: 2px;
          width: 4px;
          height: 8px;
          border: solid white;
          border-width: 0 2px 2px 0;
          transform: rotate(45deg);
          display: none;
        }
      }
    }
    
    .agree-text {
      color: rgba(255, 255, 255, 0.9);
      font-size: 12px;
      line-height: 1.5;
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 2px;
      
      .terms-link {
        color: #1890ff;
        text-decoration: none;
        font-size: 12px;
        
        &:hover {
          text-decoration: underline;
        }
      }
    }
  }
  
  .login-btn {
    width: 100%;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    
    &:active {
      transform: scale(0.98);
      opacity: 0.9;
    }
  }
  
}

// 底部按钮
.bottom-actions {
  display: flex;
  gap: 10px;
  padding: 0 10px;
  
  .action-btn {
    flex: 1;
    height: auto;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 12px;
    padding: 15px 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    
    .btn-image {
      width: 30%;
      max-width: 35px;
      height: auto;
      display: block;
    }
    
    .btn-text {
      color: rgba(255, 255, 255, 0.9);
      font-size: 12px;
      text-align: center;
    }
    
    &:active {
      transform: scale(0.98);
      opacity: 0.9;
      background: rgba(255, 255, 255, 0.15);
    }
  }
}

// 忘记密码弹窗样式
::v-deep .van-dialog {
  background: linear-gradient(to bottom, #9d4edd 0%, rgba(157, 78, 221, 0.5) 50%, rgba(157, 78, 221, 0.35) 100%) !important;
  border-radius: 12px;
  overflow: hidden;
  
  .van-dialog__header {
    padding: 15px 20px !important;
    background: transparent !important;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #74249b !important;
    
    .van-dialog__title {
      color: #ffffff