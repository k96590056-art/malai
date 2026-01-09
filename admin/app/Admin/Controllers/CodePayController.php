<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\CodePay;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class CodePayController extends AdminController
{
    protected $state = [1 => '可用',0 => '禁用'];
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new CodePay(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('content','标题');
            $grid->column('mch_id','帐号');
            $grid->column('min_price','最低充值金额');
            $grid->column('max_price','最大充值金额');
            $grid->column('status','状态')->using($this->state);
            $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');

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
        return Show::make($id, new CodePay(), function (Show $show) {
            $show->field('id');
            $show->field('payimg')->image();
            $show->field('content');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new CodePay(), function (Form $form) {
            $form->display('id');
             $form->text('content','标题')->required();
            $form->text('mch_id','帐号')->required();
            $form->decimal('min_price','最低充值金额')->required();
            $form->decimal('max_price','最大充值金额')->required();
            $form->image('payimg','收款二维码')->uniqueName()->required()->rules('mimes:jpeg,png,jpg,gif,webp|max:2048');
            $form->radio('status','状态')->options([1 => '可用',0 => '禁用'])->default(1);
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
