// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import apiFun from "@/http/api.js";
// 引入 我们准备好的 store
import store from '@/store/index.js'
// import ElementUI from 'element-ui';
// import 'element-ui/lib/theme-chalk/index.css'
// Vue.use(ElementUI);
import  "amfe-flexible";
import Vant from 'vant';
import 'vant/lib/index.css';
Vue.use(Vant);

// 全局注册
Vue.prototype.$apiFun = apiFun;//请求接口api

Vue.config.productionTip = false

router.afterEach((to, from, next) => {
  
  window.scrollTo(0, 0);//每到一个新的页面 就自动回顶
  if(document.querySelector(".index-page")){
    document.querySelector(".index-page").scrollTo(0, 0) ;//

  }
});
router.beforeEach((to, from, next) => {
  let token = sessionStorage.getItem('token') ? sessionStorage.getItem('token') : '';
  if (to.matched.some(res => res.meta.requireAuth)) { // 验证是否需要登陆
    if (sessionStorage.getItem('token')) { // 查询本地存储信息是否已经登陆
      next();
    } else {
      next({
        path: '/login', // 未登录则跳转至login页面
        query: { redirect: to.fullPath } // 登陆成功后回到当前页面，这里传值给login页面，to.fullPath为当前点击的页面
      });
    }
  } else {
    next();
  }


});
/* eslint-disable no-new */
new Vue({
  el: '#app',
  store,//使用store
  router,
  components: { App },
  template: '<App/>'
})
