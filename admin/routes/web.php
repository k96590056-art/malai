<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// ---------------代理--------------------
Route::domain(env('AGENT_URL'))->group(function (){
    Route::get('kit/captcha/{tmp}', 'Agent\AuthController@captcha');
    Route::any('/login','Agent\AuthController@login');
    // 推广链接跳转路由（不需要认证）
    Route::get('/promotion','Agent\IndexController@promotionRedirect');
    Route::middleware(['auth'])->group(function () {
        Route::get('/','Agent\IndexController@index');
        Route::get('/download-qrcode','Agent\IndexController@generateQrcode');
        Route::get('/agent/qrcode/{id}','Agent\IndexController@showQrcode');
        Route::get('/notice','Agent\IndexController@notice');
        Route::get('/message','Agent\IndexController@message');
        Route::get('/message_detail/{id}','Agent\IndexController@messageDetail');
        Route::get('/notice_detail/{id}','Agent\IndexController@noticeDetail');
        Route::get('/chart','Agent\IndexController@chart');
        Route::get('/today','Agent\IndexController@todayData');
        Route::get('/profit','Agent\IndexController@profit');
        Route::get('/commission','Agent\IndexController@commission');
        Route::get('/subordinate','Agent\IndexController@subordinate');

        Route::any('/add-member','Agent\IndexController@addMember');
        Route::any('/memberlist','Agent\IndexController@memberList');
        Route::get('/agent-list','Agent\IndexController@agentList');
        Route::get('/region-member-list','Agent\IndexController@regionMemberList');
        Route::get('/bet-log','Agent\IndexController@betLog');
        Route::get('/recharge-log','Agent\IndexController@rechargeLog');
        Route::get('/withdraw-log','Agent\IndexController@withdrawLog');
        Route::get('/transfer-log','Agent\IndexController@transferLog');
        Route::get('/rebate','Agent\IndexController@releasewaterLog');

        Route::any('/changefanshui','Agent\AuthController@changefanshui');
        Route::any('/getuserdata','Agent\IndexController@getuserdata');


        Route::any('/editPassword','Agent\AuthController@editPassword');
        Route::get('/logout','Agent\AuthController@logout');
        Route::any('/recharge','Agent\IndexController@recharge');
    });
});



// -------------------手机版-----------------
Route::domain(env('WAP_URL'))->group(function (){

    Route::any('/login','Wap\IndexController@login');
    Route::any('/register','Wap\IndexController@register');
    Route::get('/','Wap\IndexController@index');
    Route::middleware(['auth'])->group(function () {
        Route::get('/logout','Wap\IndexController@logout');
        Route::get('/activity','Wap\IndexController@activity');
        Route::any('/activityapply/{id}','Wap\IndexController@showactivity');
        Route::post('/doactivityapply','Wap\IndexController@doactivity');         
        Route::get('/center','Wap\IndexController@center');
        Route::get('/centernew','Wap\IndexController@centernew');
        Route::any('/backWater','Wap\IndexController@backWater');
        Route::any('/betRecord','Wap\IndexController@betRecord');
        Route::any('/wallet','Wap\IndexController@wallet');
        Route::any('/bet','Wap\IndexController@betRecord');
        Route::any('/transaction','Wap\IndexController@transactionRecord');
        Route::any('/transaction1','Wap\IndexController@transactionRecord1');
        Route::any('/transaction2','Wap\IndexController@transactionRecord2');
        Route::any('/bankcard','Wap\IndexController@bankcard');
        Route::any('/bind-usdt','Wap\IndexController@bindUsdt');
        Route::post('/getUserPtBalance','Wap\IndexController@getUserPlBalance');
        Route::post('/transferUserPlBalance','Wap\IndexController@transferUserPlBalance');
        Route::any('/persondata','Wap\IndexController@persondata');
        Route::any('/messagecenter','Wap\IndexController@messagecenter');
        Route::post('/transMoney','Wap\IndexController@transferMoney');
        Route::post('/getbanklist','Member\AuthController@banklist');
        Route::post('/backWaterAll','Member\AuthController@backWaterAll');
        Route::any('/editpassword','Wap\IndexController@editpassword');

        Route::post('/fillData','Wap\IndexController@fillData');
        Route::post('/editPasswordDo','Wap\IndexController@editPasswordDo');
        Route::post('/editPayPasswordDo','Wap\IndexController@editPayPasswordDo');
        Route::post('/uptransferstatus','Member\MemberController@uptransferstatus');
        // 充值
        Route::post('/getBankCardData','Member\PayController@getBankCardData');
        Route::get('/recharge','Wap\IndexController@recharge');
        Route::post('/rechargeDo','Member\PayController@rechargeDo');
        Route::get('/withdrawals','Wap\IndexController@withdrawals');
        Route::any('/withdrawApply','Member\PayController@withdrawApply');
        Route::get('/transfer','Wap\IndexController@transfer');
        Route::post('/transAll','Member\PayController@transAll');
        Route::post('/getUserBalance','Member\PayController@getUserBalance');

        Route::get('/applyagent','Wap\IndexController@applyagent');
        Route::post('/applyagentdo','Wap\IndexController@applyagentdo');
        Route::get('/addCard','Wap\IndexController@addCard');
        Route::post('/bindCardDo','Member\PayController@bindCardDo');

        Route::get('/game','Member\MemberController@game');
    });
});


// ----pc----
Route::get('/','Web\IndexController@index');
Route::get('/download','Web\IndexController@app');
Route::get('/sport','Web\IndexController@sport');
Route::get('/realbet','Web\IndexController@realbet');
Route::get('/joker','Web\IndexController@joker');
Route::get('/gaming','Web\IndexController@gaming');
Route::get('/test','Web\IndexController@test');
Route::get('/test1','Web\IndexController@test1');
Route::get('/lottery','Web\IndexController@lottery');
Route::get('/concise','Web\IndexController@concise');
Route::get('/activity','Web\IndexController@activity');
Route::get('/articles','Web\IndexController@articles');
Route::get('/agent','Web\IndexController@agent');
Route::get('/appindex','Web\IndexController@appindex');
Route::get('/register','Member\AuthController@register');
Route::post('/registerDo','Member\AuthController@registerDo');
Route::get('/login','Member\AuthController@login')->name('login');
Route::post('/loginDo','Member\AuthController@loginDo');
Route::get('/logout','Member\AuthController@logout');
Route::post('/upload','Web\IndexController@upload');//上传文件
Route::any('/notice','Web\IndexController@notice');
Route::any('/notify','Member\PayController@notify');
Route::any('/fourwaynotify','Member\PayController@fourwaynotify');
Route::any('/zgp-withdraw-callback','Member\PayController@zgpWithdrawCallback');
Route::get('/vip','Web\IndexController@vip');

Route::post('/gamelist','Member\MemberController@gamelist');
Route::get('/content/{id}','Web\IndexController@content');
Route::any('/activityapply/{id}','Member\MemberController@activity');
Route::post('/doactivityapply','Member\MemberController@doactivity');
Route::post('/user_balance','\App\Admin\Renderable\UserBalance@user_balance');
Route::get('/pull','Web\IndexController@pull');
Route::prefix('member')->middleware(['auth'])->group(function () {
    Route::get('/center','Member\MemberController@center');//个人中心
    Route::get('/centernew','Member\MemberController@centernew');//个人中心
    Route::post('/fillData','Member\MemberController@fillData');//完善信息
    Route::get('/editPassword','Member\AuthController@editPassword');//修改密码
    Route::post('/editPasswordDo','Member\AuthController@editPasswordDo');
    Route::get('/editPayPassword','Member\AuthController@editPayPassword');//修改取款密码
    Route::post('/editPayPasswordDo','Member\AuthController@editPayPasswordDo');
    Route::any('/getbanklist','Member\AuthController@banklist');
    Route::any('/activityprogress','Member\MemberController@progress');
    // 充值
    Route::get('/recharge','Member\PayController@recharge');
    Route::post('/rechargeDo','Member\PayController@rechargeDo');
    Route::get('/bindCard','Member\PayController@bindCard');
    Route::get('/bindZgpay','Member\PayController@bindZgpay');
    Route::post('/bindCardDo','Member\PayController@bindCardDo');
    Route::any('/editCard/{id}','Member\PayController@editCard');
    Route::get('/getCardData/{id}','Member\PayController@getCardData');
    Route::get('/withdraw','Member\PayController@withdraw');
    Route::any('/withdrawApply','Member\PayController@withdrawApply');
    Route::post('/getAllUserCard','Member\PayController@getAllUserCard');
    Route::get('/delCard/{id}','Member\PayController@delCard');
    Route::post('/transAll','Member\PayController@transAll');
    Route::any('/BalanceAll','Member\PayController@BalanceAll');
    Route::any('/wallet','Member\PayController@wallet');
    Route::post('/getUserBalance','Member\PayController@getUserBalance');
    Route::post('/getUserPtBalance','Member\PayController@getUserPlBalance');
    Route::any('/transfer','Member\MemberController@transfer');
    Route::post('/usertransfer','Member\MemberController@usertransfer');
    Route::post('/uptransferstatus','Member\MemberController@uptransferstatus');

    Route::get('/applyagent','Web\IndexController@applyagent');
    Route::post('/applyagentdo','Web\IndexController@applyagentdo');
        
    Route::post('/getredpacket','Member\MemberController@getRedPacket');
    Route::post('/getfanshui','Member\MemberController@getfanshui');

    Route::any('/redpacket','Member\MemberController@redPacket');
    Route::any('/fanshui','Member\MemberController@fanshui');
    Route::any('/transRecord','Member\MemberController@transRecord');
    Route::any('/betRecord','Member\MemberController@betRecord');
    Route::any('/suggestion','Member\MemberController@suggestion');// 意见反馈
    Route::get('/vip','Member\MemberController@vip');
    Route::get('/mail/{type}','Member\MemberController@mail');
    Route::get('/mail_detail/{id}','Member\MemberController@mailDetail');

    Route::get('/game','Member\MemberController@game');
    Route::get('/userredpacket','Member\MemberController@userRedPacket');
    Route::post('/douserredpacket','Member\MemberController@doUserRedPacket');
    Route::any('/getuserallbalance','Member\PayController@userAllBalance');

    Route::get('/getUserBalance','Member\MemberController@getUserBalance');//个人中心
    Route::get('/message','Web\IndexController@messagecenter');//个人中心
    Route::any('/activityRecord','Member\MemberController@progress');
    Route::any('/redpacket','Member\MemberController@redPacket');
    
});

