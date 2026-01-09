<template>
  <div style="width: 100%; min-height: 100vh; background: rgb(237, 241, 255)">
    <van-nav-bar style="position: fixed; top: 0; left: 0; width: 100%; background-color: #ede9e7" title="交易记录" left-arrow @click-left="$router.back()" />
    <div style="height: 46px"></div>
    <div style="width: 95%; min-width: 250px; margin: 0 auto; background: #fff; border-radius: 10px; box-sizing: border-box; padding: 10px; min-height: 90vh">
      <!-- 筛选条件 -->
      <div class="saibox">
        <div class="sai" @click="showPopup(1)">{{ name }}</div>
        <div class="sai" @click="showPopup(2)">{{ dateName[date] }}</div>
        <div class="sai" @click="showPopup(3)">{{ typeName[type] }}</div>
      </div>
      <van-list style="margin-top: 10px; padding-bottom: 120px" finished-text="没有更多了" offset="300" v-model="loading" :finished="list.length == pageData.total" @load="getData" v-if="list.length > 0">
        <van-cell v-for="(item, index) in list" :key="index">
          <div style="font-size: 0.3rem" v-if="type == 1 || type == 2">订单号：{{ item.out_trade_no }}</div>
          <div style="display: flex; justify-content: space-between;">
            <div style="font-size: 0.3rem">金额 :{{ item.amount }}</div>
            <div style="font-size: 0.3rem">{{ item.pay_way }}</div>
            <div style="font-size: 0.3rem">{{ type == 1 || type == 2 ? stateType12[item.state] : stateType34[item.state] }}</div>
          </div>
          <div style="font-size: 0.3rem">{{ item.created_at }}</div>
        </van-cell>
      </van-list>
      <div v-else style="margin-top: 60px; text-align: center">
        <img src="/static/image/mescroll-empty.png" style="width: 35%" alt="" />
        <van-divider dashed :style="{ color: '#ccc', borderColor: '#ccc', padding: '20px ' }">空空如也</van-divider>
      </div>
    </div>
    <!-- 弹出层 -->
    <van-popup v-model="popup" position="bottom" :style="{ height: 'calc(100% - 1.9rem - 46px)' }">
      <div class="lisg" v-if="showXuan == 1">
        <div class="bs" v-for="(item, index) in dogameLis" :key="index" @click="changDogame(item.name, item.platname)">
          <div :class="api_type == item.platname ? 'lisga act' : 'lisga'">{{ item.name }}</div>
        </div>
      </div>
      <div class="lisg" v-if="showXuan == 2">
        <div class="bs" @click="changtype('date', 1)">
          <div :class="date == 1 ? 'lisga act' : 'lisga'">今日</div>
        </div>
        <div class="bs" @click="changtype('date', 2)">
          <div :class="date == 2 ? 'lisga act' : 'lisga'">近7日</div>
        </div>
        <div class="bs" @click="changtype('date', 3)">
          <div :class="date == 3 ? 'lisga act' : 'lisga'">近15日</div>
        </div>
        <div class="bs" @click="changtype('date', 4)">
          <div :class="date == 4 ? 'lisga act' : 'lisga'">近30日</div>
        </div>
      </div>
      <div class="lisg" v-if="showXuan == 3">
        <div class="bs" @click="changtype('type', 1)">
          <div :class="type == 1 ? 'lisga act' : 'lisga'">存款</div>
        </div>
        <div class="bs" @click="changtype('type', 2)">
          <div :class="type == 2 ? 'lisga act' : 'lisga'">取款</div>
        </div>
        <div class="bs" @click="changtype('type', 3)">
          <div :class="type == 3 ? 'lisga act' : 'lisga'">转入</div>
        </div>
        <div class="bs" @click="changtype('type', 4)">
          <div :class="type == 4 ? 'lisga act' : 'lisga'">转出</div>
        </div>
      </div>
    </van-popup>
  </div>
</template>
<script>
export default {
  name: 'transRecord',
  data() {
    return {
      date: 4,
      list: [],
      pageData: {},
      type: 1,
      page: 1,
      stateType12: ['未定义', '待审核', '审核通过', '审核拒绝'],
      stateType34: ['失败', '成功', '待结算', '未定义'],
      dogameLis: [],
      api_type: '',
      loading: false,
      name: '全平台',
      dateName: ['', '今日', '近7日', '近15日', '近30日'],
      typeName: ['', '存款', '取款', '转入', '转出'],

      popup: false,
      showXuan: 1, //1平台选择 2 日期选择
    };
  },
  created() {
    let that = this;
    that.getdogame();
    that.getData();
  },
  methods: {
    changDogame(name, type) {
      let that = this;
      that.name = name;
      that.api_type = type;
      that.popup = false;
      that.page = 1;
      that.getData();
    },
    changtype(name, val) {
      let that = this;
      that[name] = val;
      that.popup = false;
      that.page = 1;
      that.getData();
    },
    showPopup(val) {
      this.popup = true;
      this.showXuan = val;
    },
    openOrclose() {
      this.show = !this.show;
    },
    changtab() {
      let that = this;
      that.page = 1;
      that.list = [];
      that.pageData = {};
      that.getData();
    },
    getdogame() {
      let that = this;

      that.$apiFun.post('/api/balancelist', {}).then(res => {
        console.log(res);
        if (res.code != 200) {
          that.showTost(res.message);
        }
        if (res.code == 200) {
          that.dogameLis = res.data;
          that.dogameLis.unshift({ name: '全平台', platname: '' });
        }
      });
    },
    changeDate() {
      let that = this;
      that.page = 1;
      that.getData();
    },

    // 获取交易记录
    getData() {
      let that = this;
      let page = that.page;
      if (page > that.pageData.last_page) {
        that.loading = false;

        return;
      }
      that.$parent.showLoading();
      let info = {
        date: that.date,
        type: that.type,
        page: that.page,
        api_type: that.api_type,
      };
      that.$apiFun
        .post('/api/gettransrecord', info)
        .then(res => {
          if (res.code != 200) {
            that.$parent.showTost(0, res.message);
          }
          if (res.code == 200) {
            that.pageData = res.data;
            if (that.page == 1) {
              that.list = res.data.data;
            } else {
              let list = JSON.parse(JSON.stringify(that.list));
              res.data.data.forEach(el => {
                list.push(el);
              });
              that.list = list;
            }
            that.page = page + 1;
          }
          that.loading = false;
          that.$parent.hideLoading();
        })
        .catch(res => {
          that.$parent.hideLoading();
          that.loading = false;
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
.saibox {
  display: flex;
  align-items: center;
  justify-content: space-around;
  height: 1.1rem;
  box-sizing: border-box;
  padding: 0 12px;
  .sai {
    height: 0.8rem;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 30%;
    background: #f7f8fc;
    border-radius: 1.1rem;
    font-size: 0.3rem;
  }
}
.lisg {
  box-sizing: border-box;
  padding: 10px 8px;
  display: flex;
  flex-wrap: wrap;
  .bs {
    width: 25%;
    height: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    .lisga {
      width: calc(100% - 8px);
      height: 0.9rem;
      border: 0.02rem solid #cbced8;
      border-radius: 0.08rem;
      color: #a5a9b3;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.2rem;
      text-align: center;
    }
    .lisga.act {
      background: #1890ff;
      color: #fff;
      border: none;
    }
  }
}
</style>
