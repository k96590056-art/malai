<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\UserOperateLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserOperateLogController extends AdminController
{

    protected $type = [1 => '登入',2 => '登出', 3 => '会员操作', 4 => '代理后台登入', 5 => '代理后台登出', 6 => '会员转入接口异常'];
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new UserOperateLog(['user_data']), function (Grid $grid) {
            $grid->model()->orderBy('id','desc');
            $grid->column('id')->sortable();
            $grid->column('user_data.username','用户名');
            $grid->column('type')->using($this->type);
            // $grid->column('login_ua');
            $grid->column('login_ip');
            $grid->column('ip_address');
            $grid->column('desc');
            //$grid->column('info');
            $grid->column('created_at');
            //$grid->column('updated_at')->sortable();
            $grid->disableCreateButton();
            $grid->actions(function (Grid\Displayers\Actions $actions){
                $actions->disableEdit();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('user_data.username','用户名');
                $filter->equal('type')->select($this->type);
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
        return Show::make($id, new UserOperateLog(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('type');
            $show->field('login_ua');
            $show->field('login_ip');
            $show->field('ip_address');
            $show->field('desc');
            $show->field('info');
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
        return Form::make(new UserOperateLog(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('type');
            $form->text('login_ua');
            $form->text('login_ip');
            $form->text('ip_address');
            $form->text('desc');
            $form->text('info');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
