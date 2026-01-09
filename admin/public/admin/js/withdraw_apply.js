$(document).ready(function() {
    function get_count() {
        $.ajax({
            url: '/leyu666/alert',
            type: 'post',
            async:false,
            success: function(res) {
                auto_refresh_interval = res.auto_refresh_interval;
                $('#withdraw_apply_audio').attr('src','');
                $('#withdraw_apply_audio')[0].pause();
                $('#activity_apply_audio').attr('src','');
                $('#activity_apply_audio')[0].pause();
                $('#agent_apply_audio').attr('src','');
                $('#agent_apply_audio')[0].pause();
                $('#recharge_apply_audio').attr('src','');
                $('#recharge_apply_audio')[0].pause();
                if (res.alert_type == '1' || res.alert_type == '3') {
                    // $('.alert-item').addClass('show');
                    // $('#recharge').text(res.recharge_apply);
                    // $('#withdraw').text(res.withdraw_apply);
                    // $('#agent_apply').text(res.agent_apply);
                    // $('#activity_apply').text(res.activity_apply);
                }
                if (res.alert_type == '1' || res.alert_type == '3') {
                    if (res.withdraw_apply > 0 && res.withdraw_apply_audio) {
                        // 安全的音频播放
                        try {
                            $('#withdraw_apply_audio').attr('src',res.withdraw_apply_audio);
                            const playPromise = $('#withdraw_apply_audio')[0].play();
                            if (playPromise !== undefined) {
                                playPromise.catch(function(error) {
                                    // 静默处理音频播放失败
                                });
                            }
                        } catch (error) {
                            // 静默处理音频播放异常
                        }
                    }
                    
                }
                if (res.auto_refresh == 1) {
                    $('.grid-refresh').click();
                }

            }
        })
        localStorage.setItem("auto_refresh_interval", auto_refresh_interval);
    }
	
    setInterval(function(){ get_count();}, localStorage.getItem("auto_refresh_interval")*1000)
})