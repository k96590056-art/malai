<!-- components/Captcha/ClickCaptcha.vue -->
<template>
  <div class="captcha-modal" v-if="visible">
    <div class="captcha-overlay" @click="handleClose"></div>
    <div class="captcha-card">
      <div class="card-header">
        <div class="card-title">{{ title }}<span v-if="currentTargetText" class="target-text">「{{ currentTargetText }}」</span></div>
        <div class="card-brand">{{ brand }}</div>
      </div>
      <div class="card-body">
        <div class="image-box" ref="imageContainer" @click="handleImageClick">
          <img
            v-if="imageUrl"
            :src="imageUrl"
            alt="captcha"
            @load="handleImageLoad"
            @error="handleImageError"
          />
          <div
            v-for="h in hintChars"
            :key="h.id"
            class="hint-char"
            :style="{
              left: h.x + 'px',
              top: h.y + 'px',
              transform: 'translate(-50%, -50%) rotate(' + h.rotate + 'deg)',
              color: h.color,
              fontSize: h.fontSize + 'px'
            }"
          >
            {{ h.char }}
          </div>
          <div
            v-for="point in clickedMarkers"
            :key="point.id"
            class="marker"
            :style="{ left: point.x + 'px', top: point.y + 'px' }"
          >
            <span class="marker-num">{{ point.order }}</span>
          </div>
          <div v-if="loading" class="image-loading">
            <div class="spinner"></div>
            <p>图片加载中...</p>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="footer-left">
          <button class="icon-btn" @click="handleRefresh" :disabled="refreshing">
            <span class="icon-rotate"></span>
          </button>
          <button class="icon-btn" @click="handleClose">
            <span class="icon-close"></span>
          </button>
        </div>
        <button class="btn-primary" @click="handleVerify" :disabled="verifying || userSequence.length !== requiredCount">
          {{ verifying ? confirmLoadingText : confirmText }}
        </button>
      </div>
      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
      <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'ClickCaptcha',
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: '请在下图依次点击'
    },
    brand: {
      type: String,
      default: '燕窝酥'
    },
    targetText: {
      type: String,
      default: ''
    },
    pointCount: {
      type: Number,
      default: 3
    },
    generateUrl: {
      type: String,
      default: '/api/captcha/generate'
    },
    verifyUrl: {
      type: String,
      default: '/api/captcha/verify'
    },
    confirmText: {
      type: String,
      default: '确定'
    },
    confirmLoadingText: {
      type: String,
      default: '验证中...'
    },
    requester: {
      type: Function,
      default: null
    }
  },
  data() {
    return {
      sessionId: '',
      imageUrl: '',
      points: [],
      userSequence: [],
      imageLoaded: false,
      loading: false,
      refreshing: false,
      verifying: false,
      errorMessage: '',
      successMessage: '',
      imageSize: { width: 0, height: 0 },
      displaySize: { width: 0, height: 0 },
      baseSize: { width: 300, height: 180 },
      requiredCount: 0,
      currentTargetText: this.targetText
    };
  },
  computed: {
    displayedPoints() {
      const w = this.displaySize.width || this.imageSize.width;
      const h = this.displaySize.height || this.imageSize.height;
      return this.points.map(p => ({
        ...p,
        x: (p.x / (this.baseSize.width || 300)) * w,
        y: (p.y / (this.baseSize.height || 180)) * h
      }));
    },
    clickedMarkers() {
      const w = this.displaySize.width || this.imageSize.width;
      const h = this.displaySize.height || this.imageSize.height;
      return this.userSequence
        .map((id, idx) => {
          const p = this.points.find(pt => pt.id === id);
          if (!p) return null;
          return {
            id: p.id,
            order: idx + 1,
            x: (p.x / (this.baseSize.width || 300)) * w,
            y: (p.y / (this.baseSize.height || 180)) * h
          };
        })
        .filter(Boolean);
    },
    clickedDetails() {
      return this.userSequence
        .map(id => {
          const p = this.points.find(pt => pt.id === id);
          if (!p) return null;
          return { id: p.id, order: p.order, x: p.x, y: p.y };
        })
        .filter(Boolean);
    },
    hintChars() {
      const dps = this.displayedPoints || [];
      const palette = ['#ff6b00', '#ff2d55', '#2d9dff', '#ffd400', '#6c7bff'];
      const chars = Array.from(this.currentTargetText || '').filter(Boolean);
      if (!dps.length || !chars.length) return [];
      const ordered = (this.points || []).slice().sort((a, b) => (a.order || 0) - (b.order || 0));
      return ordered.slice(0, chars.length).map((p, idx) => {
        const dp = dps.find(x => x.id === p.id) || { x: 0, y: 0 };
        return {
          id: `hc-${p.id}`,
          char: chars[idx],
          x: dp.x,
          y: dp.y,
          rotate: Math.round(-25 + Math.random() * 50),
          color: palette[idx % palette.length],
          fontSize: 22
        };
      });
    }
  },
  watch: {
    visible(newVal) {
      if (newVal) {
        this.generateCaptcha();
      } else {
        this.reset();
      }
    },
    targetText(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.currentTargetText = newVal;
        if (this.visible) {
          this.generateCaptcha();
          this.resetUserInput();
        }
      }
    }
  },
  methods: {
    async generateCaptcha() {
      try {
        this.loading = true;
        this.errorMessage = '';
        const post = this.requester || (this.$apiFun && this.$apiFun.post);
        const response = await post(this.generateUrl, {
          point_count: this.pointCount
        });
        const data = response.data.data || response.data;
        this.sessionId = data.session_id;
        this.imageUrl = data.image_url;
        this.points = data.points;
        if (data.target_text) {
          this.currentTargetText = data.target_text;
        }
        if (data.base_width && data.base_height) {
          this.baseSize = { width: data.base_width, height: data.base_height };
        }
        this.requiredCount = Array.isArray(this.points) ? this.points.length : (data.point_count || this.pointCount);
        
      } catch (error) {
        console.error('生成验证码失败:', error);
        this.errorMessage = '验证码生成失败，请重试';
      } finally {
        this.loading = false;
      }
    },
    
    
    handleImageLoad(event) {
      const img = event.target;
      this.imageSize = {
        width: img.naturalWidth,
        height: img.naturalHeight
      };
      this.imageLoaded = true;
      this.errorMessage = '';
      this.$nextTick(() => {
        if (this.$refs.imageContainer) {
          this.displaySize = {
            width: this.$refs.imageContainer.clientWidth,
            height: this.$refs.imageContainer.clientHeight
          };
        }
      });
    },
    
    handleImageError() {
      if (!this.imageUrl) return;
      this.errorMessage = '图片加载失败，请刷新重试';
      this.imageLoaded = false;
    },
    
    handleImageClick(event) {
      if (!this.imageLoaded || this.userSequence.length >= this.requiredCount) {
        return;
      }
      
      const rect = this.$refs.imageContainer.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      
      const clickedPoint = this.findClickedPoint(x, y);
      
      if (clickedPoint && !this.userSequence.includes(clickedPoint.id)) {
        this.userSequence.push(clickedPoint.id);
        this.playClickSound();
      }
    },
    
    findClickedPoint(x, y) {
      const w = this.displaySize.width || this.imageSize.width || 300;
      const tolerance = Math.max(20, Math.round(w * 0.03));
      return this.points.find(point => {
        const dp = this.displayedPoints.find(p => p.id === point.id);
        const dx = dp.x - x;
        const dy = dp.y - y;
        return Math.sqrt(dx * dx + dy * dy) <= tolerance;
      });
    },
    
    async handleVerify() {
      if (this.userSequence.length !== this.requiredCount) {
        this.errorMessage = `请完成所有${this.requiredCount}个点的点击`;
        return;
      }
      
      try {
        this.verifying = true;
        this.errorMessage = '';
        this.successMessage = '';
        const post = this.requester || (this.$apiFun && this.$apiFun.post);
        const response = await post(this.verifyUrl, {
          session_id: this.sessionId,
          click_sequence: this.clickedDetails.map(d => d.order),
          id_sequence: this.userSequence,
          click_points: this.clickedDetails.map(d => ({ x: d.x, y: d.y }))
        });
        let valid = false;
        const d = response.data || response;
        if (d && typeof d.valid !== 'undefined') {
          valid = d.valid;
        } else if (d && d.data && typeof d.data.valid !== 'undefined') {
          valid = d.data.valid;
        }
        if (valid) {
          this.successMessage = '验证成功，请稍等...';
          setTimeout(() => {
            this.$emit('success', this.sessionId);
            this.handleClose();
          }, 2000);
        } else {
          this.errorMessage = '验证失败，请重试';
          this.resetUserInput();
          this.$emit('fail');
        }
      } catch (error) {
        console.error('验证失败:', error);
        this.errorMessage = '验证失败，请重试';
        this.$emit('fail');
      } finally {
        this.verifying = false;
      }
    },
    
    async handleRefresh() {
      this.refreshing = true;
      await this.generateCaptcha();
      this.resetUserInput();
      this.refreshing = false;
    },
    
    handleClose() {
      this.$emit('close');
    },
    
    resetUserInput() {
      this.userSequence = [];
    },
    
    reset() {
      this.sessionId = '';
      this.imageUrl = '';
      this.points = [];
      this.userSequence = [];
      this.imageLoaded = false;
      this.errorMessage = '';
      this.successMessage = '';
      this.imageSize = { width: 0, height: 0 };
      this.displaySize = { width: 0, height: 0 };
      this.requiredCount = 0;
    },
    
    playClickSound() {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();
      
      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);
      
      oscillator.frequency.value = 800;
      oscillator.type = 'sine';
      
      gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
      
      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 0.1);
    }
  }
};
</script>

<style scoped>
.captcha-modal {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
}
.captcha-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.45);
}
.captcha-card {
  position: relative;
  width: 92%;
  max-width: 420px;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 14px 30px rgba(0,0,0,0.25);
  overflow: hidden;
}
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  color: #333;
}
.card-title {
  font-size: 14px;
}
.target-text { margin-left: 6px; color: #5a3bff; font-weight: 600; }
.card-brand {
  font-size: 16px;
  font-weight: 600;
}
.card-body {
  padding: 0 16px 12px;
}
.image-box {
  position: relative;
  width: 100%;
  height: 200px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}
.image-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.marker {
  position: absolute;
  transform: translate(-50%, -50%);
  width: 34px;
  height: 34px;
  background: linear-gradient(180deg, #6c7bff 0%, #5a3bff 100%);
  border: 2px solid #ffffff;
  border-radius: 50%;
  box-shadow: 0 6px 18px rgba(90,59,255,0.4);
  z-index: 2;
}
.marker:after {
  content: '';
  position: absolute;
  top: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 10px;
  height: 10px;
  background: linear-gradient(180deg, #6c7bff 0%, #5a3bff 100%);
  border: 2px solid #ffffff;
  border-bottom: none;
  border-left: none;
  border-radius: 10px 0 10px 10px;
}
.marker-num {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 14px;
}
.image-loading {
  position: absolute;
  inset: 0;
  background: rgba(255,255,255,0.9);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.hint-char {
  position: absolute;
  font-weight: 900;
  text-shadow: 0 1px 0 rgba(255,255,255,0.7), 0 0 10px rgba(0,0,0,0.3);
  user-select: none;
  pointer-events: none;
  z-index: 1;
}
.spinner {
  width: 30px;
  height: 30px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #5a3bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 8px;
}
@keyframes spin { 0% { transform: rotate(0deg) } 100% { transform: rotate(360deg) } }
.card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px 16px;
}
.footer-left { display: flex; gap: 10px; }
.icon-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid #DADDE6;
  background: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.icon-rotate {
  width: 16px;
  height: 16px;
  border: 2px solid #6c7bff;
  border-top-color: transparent;
  border-radius: 50%;
}
.icon-close {
  position: relative;
  width: 16px;
  height: 16px;
}
.icon-close:before, .icon-close:after {
  content: '';
  position: absolute;
  left: 50%;
  top: 50%;
  width: 14px;
  height: 2px;
  background: #6c7bff;
}
.icon-close:before { transform: translate(-50%, -50%) rotate(45deg); }
.icon-close:after { transform: translate(-50%, -50%) rotate(-45deg); }
.btn-primary {
  flex: 1;
  margin-left: 12px;
  height: 38px;
  border: none;
  border-radius: 8px;
  background: linear-gradient(180deg, #7aa1ff 0%, #5a82ff 100%);
  color: #fff;
  font-size: 15px;
}
.btn-primary:disabled { opacity: 0.6 }
.error-message {
  margin: 0 16px 16px;
  background: #f8d7da;
  color: #721c24;
  padding: 10px;
  border-radius: 6px;
  text-align: center;
  font-size: 14px;
}
.success-message {
  margin: 0 16px 16px;
  background: #d4edda;
  color: #155724;
  padding: 10px;
  border-radius: 6px;
  text-align: center;
  font-size: 14px;
}
</style>
