<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Region;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class RegionController extends AdminController
{
    protected $title = "地区管理";

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Region(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name', '地区名称');
            $grid->column('code', '地区代码');
            $grid->column('status', '状态')->display(function ($status) {
                return $status ? '<span class="label label-success">启用</span>' : '<span class="label label-danger">禁用</span>';
            });
            $grid->column('created_at', '创建时间');
            $grid->column('updated_at', '更新时间')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name', '地区名称');
                $filter->like('code', '地区代码');
                $filter->equal('status', '状态')->radio([
                    '' => '全部',
                    1 => '启用',
                    0 => '禁用',
                ]);
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableView();
                $actions->disableDelete(); // 禁止删除地区
            });
            
            // 禁用批量删除
            $grid->disableBatchDelete();
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
        return Show::make($id, new Region(), function (Show $show) {
            $show->field('id');
            $show->field('name', '地区名称');
            $show->field('code', '地区代码');
            $show->field('status', '状态')->using([1 => '启用', 0 => '禁用']);
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
        return Form::make(new Region(), function (Form $form) {
            $form->display('id');
            $form->text('name', '地区名称')->required()->help('请输入地区名称，例如：菲律宾、南斯拉夫、阿联酋');
            $form->text('code', '地区代码')->help('请输入地区代码，例如：B、N、A');
            $form->radio('status', '状态')->options([1 => '启用', 0 => '禁用'])->default(1);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}

