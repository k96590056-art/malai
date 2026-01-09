<?php

namespace App\Admin\Controllers;

use App\Models\Sponsor;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;

/**
 * 赞助管理控制器
 */
class SponsorController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '赞助管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Sponsor());

        // 只显示最基本的字段
        $grid->column('id', 'ID');
        $grid->column('name', '赞助名称');
        $grid->column('title', '赞助标题');
        $grid->column('logo', 'Logo')->image('', 50, 50);
        $grid->column('banner', '横幅图片')->image('', 100, 50);
        $grid->column('content_type', '内容类型')->using([
            'link' => '链接地址',
            'article' => '文章内容'
        ])->label([
            'link' => 'info',
            'article' => 'success'
        ]);
        $grid->column('link_url', '链接地址');
        $grid->column('status', '状态')->using([
            'active' => '正常',
            'inactive' => '禁用'
        ]);
        $grid->column('sort_order', '排序')->sortable();
        $grid->column('created_at', '创建时间');

        // 操作按钮
        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Sponsor::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', '赞助名称');
        $show->field('title', '赞助标题');
        $show->field('logo', 'Logo')->image();
        $show->field('banner', '横幅图片')->image();
        $show->field('content_type', '内容类型')->using([
            'link' => '链接地址',
            'article' => '文章内容'
        ]);
        $show->field('content', '文章内容')->unescape();
        $show->field('link_url', '链接地址');
        $show->field('link_type', '链接类型')->using([
            'internal' => '内部链接',
            'external' => '外部链接'
        ]);
        $show->field('status', '状态');
        $show->field('sort_order', '排序');
        $show->field('created_at', '创建时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Sponsor());

        $form->text('name', '赞助名称')->required();
        $form->text('title', '赞助标题')->required();
        $form->image('logo', 'Logo')->uniqueName()->rules('mimes:jpeg,png,jpg,gif,webp|max:2048');
        $form->image('banner', '横幅图片')->uniqueName()->rules('mimes:jpeg,png,jpg,gif,webp|max:4096');
        
        // 内容类型选择
        $form->select('content_type', '内容类型')->options([
            'link' => '链接地址',
            'article' => '文章内容'
        ])->default('link')->when('link', function (Form $form) {
            $form->text('link_url', '链接地址')->help('内部链接请输入相对路径，如：/about，外部链接请输入完整URL');
            $form->select('link_type', '链接类型')->options([
                'internal' => '内部链接',
                'external' => '外部链接'
            ])->default('external');
        })->when('article', function (Form $form) {
            $form->editor('content', '文章内容')->required();
        });
        
        $form->select('status', '状态')->options([
            'active' => '正常',
            'inactive' => '禁用'
        ])->default('active');
        $form->number('sort_order', '排序')->default(0);

        // 表单保存前处理
        $form->saving(function (Form $form) {
            // 判断是否是新增操作
            if ($form->isCreating()) {
                $form->created_at = date('Y-m-d H:i:s');
                $form->updated_at = date('Y-m-d H:i:s');
            } else {
                $form->updated_at = date('Y-m-d H:i:s');
            }
            
            // 处理link_type字段，避免null值
            if ($form->content_type === 'article') {
                $form->link_type = 'external'; // 设置默认值
                $form->link_url = ''; // 清空链接地址
            } elseif ($form->content_type === 'link') {
                // 确保link_type有值
                if (empty($form->link_type)) {
                    $form->link_type = 'external';
                }
                
                // 处理内部链接格式
                if ($form->link_type === 'internal' && !empty($form->link_url)) {
                    // 确保内部链接以 / 开头
                    if (!str_starts_with($form->link_url, '/')) {
                        $form->link_url = '/' . $form->link_url;
                    }
                }
            }
        });

        return $form;
    }
}
