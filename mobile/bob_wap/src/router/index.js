import Vue from 'vue'
import Router from 'vue-router'
import Main from '@/components/Main'

import index from '@/components/mode/index'
import app from '@/components/mode/app'
import kefu from '@/components/mode/kefu'
import gamePage from '@/components/mode/gamePage'
import hongbao from '@/components/mode/hongbao'
import activity from '@/components/mode/activity'
import zhanzhu from '@/components/mode/zhanzhu'
import mine from '@/components/mode/mine'



import login from '@/components/login'
import activityInfo from '@/components/activityInfo'
import zhanzhuye from '@/components/zhanzhuye'
import vip from '@/components/vip'
import applyagent from '@/components/applyagent'
import boutBallBet from '@/components/boutBallBet'
import boutBallBetInfo from '@/components/boutBallBetInfo'
import message from '@/components/message'
import fanshui from '@/components/fanshui'
import userInfo from '@/components/userInfo'
import userCent from '@/components/userCent'
import wallet from '@/components/wallet'
import addUsdtCard from '@/components/addUsdtCard'
import addBankCard from '@/components/addBankCard'
import usdtmore from '@/components/usdtmore'
import password from '@/components/password'
import welfare from '@/components/welfare'
import betRecord from '@/components/betRecord'
import activityRecord from '@/components/activityRecord'
import transRecord from '@/components/transRecord'
import money from '@/components/money'
import recharge from '@/components/recharge'
import payInfo from '@/components/payInfo'
import withdrawal from '@/components/withdrawal'
import transfer from '@/components/transfer'
import concise from '@/components/mode/concise'








Vue.use(Router)

export default new Router({
  // mode: 'history',

  // meta: {
  //   requireAuth: true,
  //   keepAlive: true,//是否缓存组件
  //   useCatch:false//是否用缓存
  // }
  mode: 'hash',

  routes: [
    {
      path: '/',
      name: 'Main',
      component: Main,
      children: [
        {
          path: '/',
          name: 'index',
          component: index,
          meta: {
            keepAlive: true,//是否缓存组件
            useCatch: false//是否用缓存
          }
        }, {
          path: '/hongbao',
          name: 'hongbao',
          component: hongbao,

        },
        {
          path: '/activity',
          name: 'activity',
          component: activity, meta: {
            keepAlive: true,//是否缓存组件
            useCatch: false//是否用缓存
          }

        }, {
          path: '/zhanzhu',
          name: 'zhanzhu',
          component: zhanzhu, meta: {
            keepAlive: true,//是否缓存组件
            useCatch: false//是否用缓存
          }

        }, {
          path: '/mine',
          name: 'mine',
          component: mine, meta: {
            keepAlive: true,//是否缓存组件

            useCatch: false,//是否用缓存
            requireAuth: true,

          }

        },
        {
          path: '/app',
          name: 'app',
          component: app, meta: {
            keepAlive: true,//是否缓存组件

            useCatch: false,//是否用缓存
            requireAuth: true,

          }

        },

        {
          path: '/kefu',
          name: 'kefu',
          component: kefu,

        }
      ]
    }, {
      path: '/login',
      name: 'login',
      component: login,
    }, {
      path: '/gamePage',
      name: 'gamePage',
      component: gamePage,

    }, {
      path: '/activityInfo',
      name: 'activityInfo',
      component: activityInfo,
    }, {
      path: '/zhanzhuye',
      name: 'zhanzhuye',
      component: zhanzhuye,
    },
    {
      path: '/vip',
      name: 'vip',
      component: vip,
      meta: {
        keepAlive: true,//是否缓存组件
        useCatch: false//是否用缓存
      }

    }, {
      path: '/applyagent',
      name: 'applyagent',
      component: applyagent,


    }, {
      path: '/boutBallBet',
      name: 'boutBallBet',
      component: boutBallBet,
    }, {
      path: '/boutBallBetInfo',
      name: 'boutBallBetInfo',
      component: boutBallBetInfo,
    }, {
      path: '/message',
      name: 'message',
      component: message, meta: {
        requireAuth: true,
      }
    }, {
      path: '/fanshui',
      name: 'fanshui',
      component: fanshui, meta: {
        requireAuth: true,
      }
    }, {
      path: '/userInfo',
      name: 'userInfo',
      component: userInfo,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/userCent',
      name: 'userCent',
      component: userCent,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/wallet',
      name: 'wallet',
      component: wallet,
      meta: {
        requireAuth: true,
      }
    }
    , {
      path: '/addBankCard',
      name: 'addBankCard',
      component: addBankCard,
      meta: {
        requireAuth: true,
      }
    }
    , {
      path: '/addUsdtCard',
      name: 'addUsdtCard',
      component: addUsdtCard,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/usdtmore',
      name: 'usdtmore',
      component: usdtmore,

    }, {
      path: '/password',
      name: 'password',
      component: password,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/welfare',
      name: 'welfare',
      component: welfare,
      meta: {
        requireAuth: true,
      }
    }
    , {
      path: '/betRecord',
      name: 'betRecord',
      component: betRecord,
      meta: {
        requireAuth: true,
      }
    }
    , {
      path: '/activityRecord',
      name: 'activityRecord',
      component: activityRecord,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/transRecord',
      name: 'transRecord',
      component: transRecord,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/money',
      name: 'money',
      component: money,
      meta: {
        requireAuth: true,
      }
    }, {
      path: '/recharge',
      name: 'recharge',
      component: recharge,
      meta: {
        requireAuth: true,
        keepAlive: true,//是否缓存组件
        useCatch: false//是否用缓存
      }
    }, {
      path: '/payInfo',
      name: 'payInfo',
      component: payInfo,


    }, {
      path: '/concise',
      name: 'concise',
      component: concise,
      meta: {
        keepAlive: true,//是否缓存组件
        useCatch: false//是否用缓存
      }

    },

    {
      path: '/withdrawal',
      name: 'withdrawal',
      component: withdrawal,
      meta: {
        requireAuth: true,

      }
    }, {
      path: '/transfer',
      name: 'transfer',
      component: transfer,
      meta: {
        requireAuth: true,

      }
    }
    , {
      path: '*', // 重定向页面地址
      redirect: '/'
    }

  ]
})
