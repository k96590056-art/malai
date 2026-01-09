<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\GameCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class GameCategoryController extends AdminController
{
    protected $title = "游戏分类";

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new GameCategory(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name', '分类名称');
            $grid->column('code', '分类编码');
            $grid->column('icon', '分类图标')->image('', 50, 50);
            $grid->column('status', '状态')->display(function($status) {
                return $status == 1 ? '<span style="color: green;">开启</span>' : '<span style="color: red;">关闭</span>';
            });
            $grid->column('created_at', '创建时间');
            $grid->column('updated_at', '更新时间')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name', '分类名称');
                $filter->like('code', '分类编码');
                $filter->equal('status', '状态')->select([1 => '开启', 0 => '关闭']);
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
        return Show::make($id, new GameCategory(), function (Show $show) {
            $show->field('id');
            $show->field('name', '分类名称');
            $show->field('code', '分类编码');
            $show->field('icon', '分类图标')->image();
            $show->field('status', '状态')->using([1 => '开启', 0 => '关闭']);
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
        return Form::make(new GameCategory(), function (Form $form) {
            $form->display('id');
            $form->text('name', '分类名称')->required();
            $form->text('code', '分类编码')->required()->help('例如：realbet, sport, concise等');
            $form->image('icon', '分类图标')->uniqueName();
            $form->radio('status', '是否开启')->options([1 => '开启', 0 => '关闭'])->default(1);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}

