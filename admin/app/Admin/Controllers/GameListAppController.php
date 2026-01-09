<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\GameListApp;
use App\Models\GameCategory;
use App\Models\Api;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Services\TgService;
use App\Admin\Tools\UrlEdit;
class GameListAppController extends AdminController
{
    protected $is_hot = [0 => '非热门',1 => '热门']; 	
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        // $tg = new TgService();
        // dd($tg->gameslist('ae'));
        return Grid::make(new GameListApp(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('platform_name');
            $grid->column('name');
            $grid->column('game_code');
            $grid->column('category_id')->display(function($categoryId) {
                if ($categoryId) {
                    $category = GameCategory::find($categoryId);
                    return $category ? $category->name : '-';
                }
                return '-';
            });
            $grid->column('app_state')->using([1 => '正常',0 => '关闭']);
            $grid->column('created_at');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                // 获取所有开启的游戏分类
                $categories = GameCategory::where('status', 1)->get();
                $categoryOptions = [];
                foreach ($categories as $category) {
                    $categoryOptions[$category->id] = $category->name;
                }
                $filter->equal('category_id')->select($categoryOptions);
                $filter->like('name');
            });
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableDelete();
              
            });
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
        return Show::make($id, new GameListApp(), function (Show $show) {
            $show->field('id');
            $show->field('platform_name');
            $show->field('name');
            $show->field('name_en');
            $show->field('app_state');
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
        
        return Form::make(new GameListApp(), function (Form $form) {
            $plat = [];
            $form->display('id');
            
            // 获取所有启用的游戏接口选项（只显示状态为启用的接口）
            $apis = Api::where('state', 1)->get();
            $platformOptions = [];
            foreach ($apis as $api) {
                $platformOptions[$api->api_code] = $api->api_name . ' (' . $api->api_code . ')';
            }
            $form->select('platform_name', '游戏接口')->options($platformOptions)->required()->help('选择游戏接口（仅显示启用的接口）');
            
            $form->text('name')->required();
            $form->text('game_code', '游戏代码')->default('0')->required();
            $form->text('venue_code', '场馆编码')->help('主要用于dp接口类');
            // 获取所有开启的游戏分类
            $categories = GameCategory::where('status', 1)->get();
            $categoryOptions = [];
            foreach ($categories as $category) {
                $categoryOptions[$category->id] = $category->name;
            }
			$form->select('category_id')->options($categoryOptions)->required();
            $form->image('app_img','手机热门图片')->uniqueName();
            $form->number('order_by','排序')->default(0)->help("数字越小越靠前");
            $form->radio('app_state')->options([1 => '正常',0 => '关闭'])->default(1);
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
