<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

/**
 * Admin routes
 */
Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('agent-tree', 'AgentCommissionController@agentTree');
    $router->get('/', 'HomeController@index');
    $router->resource('users', 'UserController');
    $router->resource('user-vips', 'UserVipController');
    $router->post('user-vips/{id}/toggle-switch', 'UserVipController@toggleSwitch');
    $router->resource('messages', 'MessageController');
    $router->resource('recharge','RechargeController');
    $router->resource('red-envelopes','RedEnvelopesController');
    $router->resource('code-pay','CodePayController');

    $router->resource('withdraws','WithdrawController');
    $router->resource('banks','BankController');
    $router->resource('syslog','SyslogController');
    $router->resource('pay-settings','PaySettingController');
    $router->get('/pay-config','SystemConfigController@index');
    $router->resource('activities','ActivityController');
    $router->resource('fanshui','FanshuiLogController');
    $router->resource('activity-apply','ActivityApplyController');
    $router->resource('activity-types','ActivityTypeController');
    $router->resource('transfer-logs','TransferLogController');
    $router->resource('finance-report','FinanceReportController');
    $router->resource('game-records','GameRecordController');
    $router->resource('apis','ApiController');
    $router->post('apis/{id}/toggle', 'ApiController@toggle');
    $router->resource('game-categories','GameCategoryController');
    $router->resource('game-lists','GameListController');
    $router->resource('game-lists-app','GameListAppController');
    $router->get('/system-setting','SystemConfigController@siteSetting');
    $router->resource('bet-report','BetReportController');
    $router->resource('bet-sum','BetSumController');
    $router->resource('templates','TemplateController');
    $router->get('/templates','TemplateController@index');
    $router->get('/setDefaultTemplate/{id}/{type}','TemplateController@setDefaultTemplate');
    $router->resource('agents','AgentController');
    $router->resource('agent-applys','AgentApplyController');
    $router->resource('agent-commission','AgentCommissionController');
    $router->resource('agent-settlements','AgentSettlementController');
    $router->resource('agent-interfaces','AgentInterfaceController');
    $router->resource('regions','RegionController');

    $router->resource('userredpacket','UserredpacketController');
    $router->resource('usercard','UserCardController');
    $router->resource('articlescate','ArticlescateController');
    $router->resource('articles','ArticleController');

    $router->get('/user/upbalance/{id}','UserController@upbalance');
    $router->resource('user-operate-logs','UserOperateLogController');
    $router->resource('banners','BannerController');
    $router->resource('sponsors','SponsorController'); // 赞助管理
    $router->get('clear','SystemConfigController@clear');
    $router->post('alert','HomeController@getAlertData');

    // 工单系统路由
    $router->resource('work-orders', 'WorkOrderController');
    $router->post('work-orders/{id}/reply', 'WorkOrderController@handleReply');
    $router->put('work-orders/{id}', 'WorkOrderController@update');
    $router->post('work-orders/{id}/close', 'WorkOrderController@close');
    $router->post('work-orders/{id}/open', 'WorkOrderController@open');
    
    // 测试路由 - 临时调试用
    $router->get('work-orders/{id}/test', 'WorkOrderController@testReply');
    $router->get('work-orders/{id}/test-delete', 'WorkOrderController@testDelete');
    
    // 测试控制器路由
    $router->get('test', 'TestController@index');
    $router->get('test-html', 'TestController@html');
    $router->get('test-error', 'TestController@error');

});
