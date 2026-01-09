<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\GameList;
use App\Models\Api;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Services\TgService;
use App\Admin\Tools\UrlEdit;
use Illuminate\Support\Facades\DB;
class GameListController extends AdminController
{
    protected $category = ['realbet' => '真人','sport' => '体育','concise' => '电子','gaming' => '电竞','joker' => '棋牌','lottery' => '彩票','fishing' => '捕鱼'];
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        // $tg = new TgService();
        // dd($tg->gameslist('ae'));
        return Grid::make(new GameList(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('platform_name');
            $grid->column('name');
            $grid->column('game_code');
            $grid->column('api_id', '游戏接口')->display(function($api_id) {
                if ($api_id) {
                    $api = Api::find($api_id);
                    return $api ? $api->api_name . ' (' . $api->api_code . ')' : '-';
                }
                return '-';
            });
            //$grid->column('game_icon')->image('',100,100);
            // $grid->column('name_en');
            // $grid->column('keywords');
            $grid->column('category_id')->using($this->category);
            // $grid->column('order_by');
            // $grid->column('state');
            $grid->column('is_hot','热门状态')->switch();
            $grid->column('site_state','站点状态')->switch();
            $grid->column('app_state','APP状态')->switch();
            $grid->column('created_at');
            // $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('category_id')->select($this->category);
                $filter->like('name');
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableDelete();
              
            });
			//$grid->tools(new UrlEdit());
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new GameList(), function (Show $show) {
            $show->field('id');
            $show->field('platform_name');
            $show->field('name');
            $show->field('name_en');
            // $show->field('keywords');
            // $show->field('category_id');
            // $show->field('game_code');
            // $show->field('game_img');
            // $show->field('order_by');
            // $show->field('state');
            // $show->field('is_hot');
            // $show->field('is_new');
            // $show->field('is_recommend');
            // $show->field('is_pc');
            // $show->field('is_mobile');
            $show->field('site_state');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
        return Form::make(new GameList(), function (Form $form) {
            $plat = [];
            $form->display('id');
            $form->text('platform_name')->required();
            $form->text('name')->required();
            $form->url('game_url', '游戏外链')->help('选填，如果填写了游戏外链，将直接使用此外链作为游戏链接，不会调用游戏接口');
            $form->text('game_code', '游戏代码')->default('0')->required();
            // $form->text('name_en')->required();
            // $form->text('keywords');
            
            // 获取所有启用的游戏接口选项（只显示状态为启用的接口）
            $apis = Api::where('state', 1)->get();
            $apiOptions = [];
            foreach ($apis as $api) {
                $apiOptions[$api->id] = $api->api_name . ' (' . $api->api_code . ')';
            }
            $form->select('api_id', '游戏接口')->options($apiOptions)->required()->help('选择游戏接口（仅显示启用的接口）');
            
            $form->text('venue_code', '场馆编码')->help('主要用于dp接口类');
			$form->select('category_id')->options($this->category)->required();
			$form->image('api_logo_img','接口图标')->uniqueName();
            //$form->image('check_yes_img','PC选中状态')->uniqueName();
            //$form->image('check_no_img','PC未选中状态')->uniqueName();
            $form->image('mobile_img','手机端图片')->uniqueName();
			$form->image('app_img','手机热门图片')->uniqueName();
            $form->number('order_by')->default(0)->help("数字越小越靠前");
            $form->radio('is_hot','热门状态')->options([1 => '是',0 => '否'])->default(0)->help("热门游戏会显示在热门分类中");
            $form->radio('site_state')->options([1 => '正常',0 => '关闭'])->default(1);
            $form->radio('app_state','APP状态')->options([1 => '正常',0 => '关闭'])->default(1);
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
