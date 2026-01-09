<?php

use Illuminate\Http\Request;



Route::post('/db/caipiao/getBalance',                   'Service\DbcaipiaoService@getBalance');
Route::post('/db/caipiao/safetyTransfer',               'Service\DbcaipiaoService@safetyTransfer');
Route::post('/db/caipiao/boracay/api/safety/transfer',  'Service\DbcaipiaoService@safetyTransfer');
Route::post('/db/caipiao/upateBalance',                 'Service\DbcaipiaoService@upateBalance');

Route::post('/db/zhenren/getBalance','Api\DbzhenrenController@getBalance');
Route::post('/db/zhenren/getBatchBalance','Api\DbzhenrenController@getBatchBalance');
Route::post('/db/zhenren/betConfirm','Api\DbzhenrenController@betConfirm');
Route::post('/db/zhenren/betCancel','Api\DbzhenrenController@betCancel');
Route::post('/db/zhenren/gamePayout','Api\DbzhenrenController@gamePayout');
Route::post('/db/zhenren/activityPayout','Api\DbzhenrenController@activityPayout');
Route::post('/db/zhenren/playerBetting','Api\DbzhenrenController@playerBetting');
Route::post('/db/zhenren/activityRebate','Api\DbzhenrenController@activityRebate');




Route::post('/captcha/generate','Api\CaptchaController@generate');
Route::post('/captcha/verify','Api\CaptchaController@verify');
Route::post('/login','Api\AuthController@login');
Route::post('/login_pc','Api\AuthController@login_pc');
Route::post('/register','Api\AuthController@register');
Route::post('/activitytype','Api\IndexController@activityType'); //获得类型
Route::post('/activitylist','Api\IndexController@activityList'); //活动列表
Route::post('/activitydeatil','Api\IndexController@activitydeatil'); //活动详情
Route::post('/getservicerurl','Api\IndexController@getServicerUrl'); //客户
Route::post('/gamelist','Api\IndexController@getGameList');


Route::post('/gamelistBycode','Api\IndexController@gameslist');
Route::post('/banklist','Api\IndexController@banklist');

Route::post('/bannerList','Api\IndexController@bannerList');
Route::any('/homenotice','Api\IndexController@homenotice');
Route::post('/gameCategories','Api\IndexController@getGameCategories'); // 获取游戏分类列表
Route::any('/getpaybank','Api\IndexController@getpaybank');

Route::post('/homecontent','Api\IndexController@homecontent');

Route::post('/userblance','Api\AuthController@userblance');
Route::post('/systemstatus','Api\IndexController@Systemstatus');

Route::get('/autogetusermoney','Api\AuthController@autogetusermoney');
Route::post('/homenoticelist','Api\IndexController@homenoticelist');
Route::post('/homenoticedeatil','Api\IndexController@homenoticedeatil');
Route::post('/app','Api\IndexController@app');
Route::post('/getAppUrl','Api\IndexController@getAppUrl');
Route::get('/getAgentLoginUrl','Api\IndexController@getAgentLoginUrl');
Route::get('/getVisitUrl','Api\IndexController@getVisitUrl');
Route::any('/getApiUrl','Api\IndexController@getApiUrl');
Route::get('/get_pay_way','Api\PayController@getPayWay');
Route::get('/game/list','Api\IndexController@getAllGameList');
Route::get('/all/plat','Api\IndexController@getAllPlat');
Route::post('/uservip','Api\IndexController@uservip');  
Route::post('/article','Api\IndexController@article');
Route::any('/pay/jc_notify','Api\PayController@jcNotify');
Route::any('/pay/cgpay_notify','Api\PayController@cgpay_notify');
Route::any('/credit','Api\IndexController@credit');
Route::post('/gamelistBycode','Api\IndexController@gamelistBycode');
Route::get('/sponsorList','Api\IndexController@getSponsorList'); // 获取赞助商列表



// 工单系统API接口
Route::post('/workorder/list','Api\WorkOrderController@list'); // 获取工单列表
Route::post('/workorder/create','Api\WorkOrderController@create'); // 创建工单
Route::post('/workorder/detail','Api\WorkOrderController@detail'); // 获取工单详情
Route::post('/workorder/reply','Api\WorkOrderController@reply'); // 回复工单
Route::post('/workorder/close','Api\WorkOrderController@close'); // 关闭工单
Route::middleware(['crosstttp','api_auth'])->group(function () {
    // 用户
    
    Route::post('/uploadimg','Api\AuthController@uploadimg');  //更新用户转账模式
	Route::post('/userapimoney/{api_code}','Api\IndexController@userapimoney');	
    Route::post('/updateuserinfo','Api\AuthController@updateuserinfo');  //更新用户转账模式
    
    Route::post('/editPassword','Api\AuthController@editPassword');  //修改登录密码
    Route::post('/editPayPassword','Api\AuthController@editPayPasswordDo'); //修改支付密码
    Route::post('/user','Api\AuthController@user');  //获取用户信息
    Route::post('/uptransferstatus','Api\IndexController@uptransferstatus');  //更新用户转账模式
    Route::post('/payinfo','Api\PayController@getpayinfo');
  //更新用户转账模式
    
    
    // 充值
    Route::post('/systembankcardinfo','Api\IndexController@systemBankCardInfo');  //获取后台支持的银行
    Route::post('/recharge','Api\PayController@recharge');   //充值
    Route::any('/getPayRange','Api\PayController@getPayRange'); //充值通道范围
    Route::post('/bindcard','Api\PayController@bindCard');   //绑定银行卡
    Route::post('/delcard','Api\PayController@DelbindCard');   //删除银行卡
    Route::post('/getcard','Api\PayController@getAllUserCard');  //获得用户卡
    Route::post('/getBetAmount','Api\PayController@getBetAmount');
    Route::post('/withdraw','Api\PayController@withdraw');  //提现
    Route::post('/transfer','Api\PayController@transfer');  //转账
    Route::post('/transall','Api\PayController@transall'); //一键回收
    Route::post('/refreshusermoney','Api\PayController@refreshusermoney');//个人中心  
     
    Route::post('/doactivityapply','Api\IndexController@doactivity');  //优惠活动
    Route::post('/activityApplyLog','Api\IndexController@activityApplyLog');

    // 其它
    Route::post('/noticeList','Api\IndexController@noticeList');  //公告列表

    Route::post('/getGameUrl','Api\IndexController@getGameUrl');  //获得游戏链接

    Route::post('/betrecord','Api\IndexController@betRecord');  //获取下注记录
    Route::post('/balancelist','Api\IndexController@userbalancelist');  //
    Route::post('/balancelist2','Api\IndexController@userbalancelist');  //
    //
    Route::post('/gettransrecord','Api\IndexController@transRecord');  //获取转账记录
    
    Route::post('/getrechargerecord','Api\IndexController@rechargeRecord');  //获取充值记录
    
    Route::post('/getwithdrawrecord','Api\IndexController@WithdrawRecord'); //获取提现记录

    Route::post('/message','Api\IndexController@messagecenter');//个人中心
    
    Route::post('/showmessage','Api\IndexController@message');//个人中心
    
    Route::post('/getdogame','Api\IndexController@getdogame');//个人中心

    Route::post('/getfanshui','Api\IndexController@fanshui');  //获取返水记录
    Route::post('/dofanshui','Api\IndexController@dofanshui');  //领取返水
    Route::post('/balance','Api\AuthController@getUserBalance');
    Route::post('/logoff','Api\AuthController@logoff');
    Route::post('/applyagentdo','Api\IndexController@applyagentdo');
    // 红包
    Route::post('/getredpacket','Api\PayController@getRedPacket');
    Route::any('/redpacket','Api\PayController@redPacket');
    Route::get('/userredpacket','Api\PayController@userRedPacket');
    Route::post('/douserredpacket','Api\PayController@doUserRedPacket');

});

////////////////////////////////APP操作////////////////////////////////
Route::any('/app/open/{code}','Api\AppController@open');  //注册
Route::post('/app/register','Api\AppController@register');  //注册
Route::post('/app/login','Api\AppController@login');  //登陆
Route::any('/app/pay_list','Api\AppController@pay_list');  //获取所有充值通道状态
Route::post('/app/islogin','Api\AppController@islogin');  //检查登陆状态
Route::post('/app/getMoney','Api\AppController@getMoney');  //检查余额，网站+接口
Route::post('/app/update_password','Api\AppController@update_password');  //修改密码
Route::post('/app/hall_list','Api\AppController@hall_list');   //获取大厅游戏
Route::post('/app/api_login','Api\AppController@api_login');  //获取游戏登陆链接
Route::post('/app/service_center','Api\AppController@service_center');  //获取公告信息
Route::post('/app/systeminfo','Api\AppController@systeminfo');  //获取在线客服链接
Route::post('/app/querys','Api\AppController@querys');  //获取APP常见问题
Route::post('/app/userChildren','Api\AppController@userChildren');  //获取代理直属下线数据
Route::post('/app/Regurgitation','Api\AppController@Regurgitation');  //获取代理直属下线数据
Route::post('/app/gameRecordList','Api\AppController@gameRecordList');  //获取自己投注记录
Route::post('/app/usergameForm','Api\AppController@usergameForm');  //获取个人报表
Route::post('/app/activities','Api\AppController@activities');  //获取活动列表
Route::post('/app/activitiesgo','Api\AppController@activitiesgo');  //申请活动
Route::post('/app/bindbanklist','Api\AppController@bindbanklist');  //获取可绑定银行卡列表
Route::post('/app/post_update_bank_info','Api\AppController@post_update_bank_info');  //绑定银行卡
Route::post('/app/post_drawing','Api\AppController@post_drawing');  //提现
Route::post('/app/cash_list','Api\AppController@cash_list');  //提现记录
Route::post('/app/getbank_info','Api\AppController@getbank_info');  //获取充值银行卡信息
Route::post('/app/alipay_info','Api\AppController@alipay_info');  //获取充值支付宝信息
Route::post('/app/wechat_info','Api\AppController@wechat_info');  //获取充值微信信息
Route::post('/app/usdt_info','Api\AppController@usdt_info');  //获取充值USDT信息
Route::post('/app/cgpay_info','Api\AppController@cgpay_info');  //获取充值CGPay信息
Route::post('/app/bank_pay','Api\AppController@bank_pay');  //银行卡充值
Route::post('/app/alipay_pay','Api\AppController@alipay_pay');  //支付宝充值
Route::post('/app/wechat_pay','Api\AppController@wechat_pay');  //微信充值
Route::post('/app/usdt_pay','Api\AppController@usdt_pay');  //USDT充值
Route::post('/app/cgpay_pay','Api\AppController@cgpay_pay');  //CGPay充值
Route::post('/app/zxcgpay_pay','Api\AppController@zxcgpay_pay');  //在线CGPay充值
Route::post('/app/recharge_list','Api\AppController@recharge_list');  //充值记录

// 工单系统API路由
Route::group(['prefix' => 'work-orders', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/', 'Api\WorkOrderController@store');
    Route::get('/', 'Api\WorkOrderController@index');
    Route::get('/{id}', 'Api\WorkOrderController@show');
    Route::post('/{id}/reply', 'Api\WorkOrderController@reply');
    Route::post('/{id}/close', 'Api\WorkOrderController@close');
    Route::get('/options/categories', 'Api\WorkOrderController@options');
});

// TRON USDT充值相关接口（修正命名空间到 Api）
Route::post('/tron-usdt-pay', 'Api\\AppController@tron_usdt_pay');
Route::post('/submit-tron-hash', 'Api\\AppController@submit_tron_hash');
Route::post('/tron-recharge-status', 'Api\\AppController@tron_recharge_status');
Route::post('/generate-tron-qrcode', 'Api\\AppController@generate_tron_qrcode');

// TRON回调接口（修正命名空间到 Api）
Route::post('/tron/callback/usdt-recharge', 'Api\\TronCallbackController@handleUsdtRecharge');
Route::post('/tron/manual-verify', 'Api\\TronCallbackController@manualVerify');
Route::get('/tron/network-status', 'Api\\TronCallbackController@getNetworkStatus');

Route::get('/agent/yongjin','Api\AuthController@agent_yongjin');  //计算佣金

