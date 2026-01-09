<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\AgentInterface;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AgentInterfaceController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new AgentInterface(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name', '代理接口名称');
            $grid->column('code', '代理接口代码');
            $grid->column('created_at', '创建时间');
            $grid->column('updated_at', '更新时间')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name', '代理接口名称');
                $filter->like('code', '代理接口代码');
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableDelete();
                $actions->disableView();
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
        return Show::make($id, new AgentInterface(), function (Show $show) {
            $show->field('id');
            $show->field('name', '代理接口名称');
            $show->field('code', '代理接口代码');
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new AgentInterface(), function (Form $form) {
            $form->display('id');
            $form->text('name', '代理接口名称')->required();
            $form->text('code', '代理接口代码')->required()
                ->help('请输入代理接口类的名称，例如：YesAgent');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}

