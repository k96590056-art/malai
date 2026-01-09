<ul class="nav navbar-nav">
    <li class="dropdown dropdown-notification nav-item">
        <a class="nav-link nav-link-label" href="#" data-toggle="dropdown" aria-expanded="true"><i class="ficon feather icon-bell"></i></a>
        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right alert-item">
            <li class="dropdown-menu-header">

            </li>
            <li class="scrollable-container media-list ps ps--active-y">
                <!--<a class="d-flex justify-content-between" href="/RKSYwTPtWc/recharge">-->
                <!--    <div class="media d-flex align-items-start">-->
                <!--        <div class="media-body">-->
                <!--            您有<span id="recharge"></span>条充值请求未处理-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</a>-->
                <!--<a class="d-flex justify-content-between" href="/RKSYwTPtWc/withdraws">-->
                <!--    <div class="media d-flex align-items-start">-->
                <!--        <div class="media-body">-->
                <!--            您有<span id="withdraw"></span>条提现请求未处理-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</a>-->
                <!--<a class="d-flex justify-content-between" href="/RKSYwTPtWc/agent-applys">-->
                <!--    <div class="media d-flex align-items-start">-->
                <!--        <div class="media-body">-->
                <!--            您有<span id="agent_apply"></span>条代理申请请求未处理-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</a>-->
                <!--<a class="d-flex justify-content-between" href="/RKSYwTPtWc/activity-apply">-->
                <!--    <div class="media d-flex align-items-start">-->
                <!--        <div class="media-body">-->
                <!--            您有<span id="activity_apply"></span>条活动申请请求未处理-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</a>-->
                <!--<div class="ps__rail-x" style="left: 0px; bottom: 0px;">-->
                <!--    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>-->
                <!--</div>-->
                <!--<div class="ps__rail-y" style="top: 0px; right: 0px; height: 254px;">-->
                <!--    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 184px;"></div>-->
                <!--</div>-->
                <audio src="" id="recharge_apply_audio"></audio>
                <audio src="" id="withdraw_apply_audio"></audio>
                <audio src="" id="activity_apply_audio"></audio>
                <audio src="" id="agent_apply_audio"></audio>
                <audio src="" id="work_order_audio"></audio>
            </li>
        </ul>
    </li>
</ul>
<script>
	function test(e){
        $.ajax({
            url: '/api/credit',
            type: 'post',
			data:{api_code:e.id},
            success: function(res) {
				if(res.code == 200){
					$("#money_"+e.id).html(res.data);
				}else{
					$("#money_"+e.id).html(res.message);
				}
            }
        })
	}
    $(document).ready(function() {
         // 检测用户交互，用于音频播放权限
         document.hasInteracted = false;
         
         // 监听用户交互事件
         ['click', 'touchstart', 'keydown', 'scroll'].forEach(function(event) {
             document.addEventListener(event, function() {
                 document.hasInteracted = true;
             }, { once: true });
         });
         
         function get_count() {
             $.ajax({
                 url: '/<?php echo e(env('ADMIN_ROUTE_PREFIX'), false); ?>/alert',
                 type: 'post',
                 success: function(res) {
                     // 安全的音频播放函数
                     function safePlayAudio(audioElement, audioSrc) {
                         if (audioElement && audioSrc) {
                             try {
                                 audioElement.src = audioSrc;
                                 // 检查用户是否已经与页面交互过
                                 if (document.hasInteracted || document.visibilityState === 'visible') {
                                     const playPromise = audioElement.play();
                                     if (playPromise !== undefined) {
                                         playPromise.catch(function(error) {
                                             // 如果是自动播放策略错误，记录但不显示错误
                                             if (error.name === 'NotAllowedError') {
                                                 // 静默处理自动播放策略错误
                                             }
                                         });
                                     }
                                 } else {
                                     // 页面未激活或用户未交互，跳过音频播放
                                 }
                             } catch (error) {
                                 // 静默处理音频播放异常
                             }
                         }
                     }
                     
                     if (res.activity_apply > 0) {
                         safePlayAudio($('#activity_apply_audio')[0], res.activity_apply_audio);
                     }
                     if (res.agent_apply > 0) {
                         safePlayAudio($('#agent_apply_audio')[0], res.agent_apply_audio);
                     }
                     if (res.recharge_apply > 0) {
                         safePlayAudio($('#recharge_apply_audio')[0], res.recharge_apply_audio);
                     } 
                     if (res.withdraw_apply > 0) {
                         safePlayAudio($('#withdraw_apply_audio')[0], res.withdraw_apply_audio);
                     }
                     
                     // 工单提醒 - 只对"待处理"状态的工单播放提示音
                     if (res.work_order_count > 0 && res.work_order_audio) {
                         safePlayAudio($('#work_order_audio')[0], res.work_order_audio);
                     }
                 }
             })
         }
    	
         setInterval(function(){ get_count();}, 5000)
     })
</script><?php /**PATH D:\www\bob\admin\resources\views/admin/alert.blade.php ENDPATH**/ ?>