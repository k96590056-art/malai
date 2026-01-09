<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Api;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\AdminController;

class ApiController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
 
	protected $category = ['1' => '<font color="blue">可用</font>','0' => '<font color="red">禁用</font>'];  
    protected function grid()
    {
        return Grid::make(new Api(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('api_code');
            $grid->column('api_name');
            $grid->column('api_service', '接口代码');
            $grid->column('api_money')->display(function (){
				$id = 'money_'.$this->api_code;
                return '<span id='.$id.'>'.$this->api_money."</span>&nbsp;&nbsp;&nbsp;<a  onclick='test(this)' id='$this->api_code'>刷新</a>";
            });			
            $grid->column('state')->using($this->category);
			$grid->column('order_by');
            $grid->column('created_at');
            // $grid->column('updated_at')->sortable();
            
            // 添加快捷开关按钮
            $grid->column('快捷开关')->display(function () {
                $checked = $this->state == 1 ? 'checked' : '';
                $id = 'switch_' . $this->id;
                $token = csrf_token();
                return <<<HTML
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="{$id}" {$checked} onchange="toggleApiState({$this->id}, this.checked)">
                    <label class="custom-control-label" for="{$id}"></label>
                </div>
                <script>
                function toggleApiState(id, state) {
                    console.log('切换接口状态:', id, state);
                    $.ajax({
                        url: 'apis/' + id + '/toggle',
                        type: 'POST',
                        data: {
                            _token: '{$token}',
                            state: state ? 1 : 0
                        },
                        success: function(response) {
                            console.log('成功响应:', response);
                            if (response.status) {
                                Dcat.success('状态更新成功');
                                // 刷新页面
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                Dcat.error('状态更新失败');
                                // 恢复开关状态
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX错误:', xhr, status, error);
                            Dcat.error('网络错误: ' + error);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    });
                }
                </script>
HTML;
            });
            
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
				$filter->like('api_code');
        
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
        return Show::make($id, new Api(), function (Show $show) {
            $show->field('id');
            $show->field('api_code');
            $show->field('api_name');
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
        return Form::make(new Api(), function (Form $form) {
            $form->display('id');
            $form->text('api_code');
            $form->text('api_name');
            $form->text('api_service', '接口代码')->help('用于后期组合游戏接口类，例如：dp');
			$form->image('app_icon','接口图标')->uniqueName();
			$form->text('order_by')->help("数字越小越靠前");
            $form->radio('state')->options([1 => '可用',0 => '禁用']);        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
    
    /**
     * 切换接口状态
     */
    public function toggle($id)
    {
        try {
            \Log::info('开始切换接口状态', ['id' => $id, 'request' => request()->all()]);
            
            $api = \App\Models\Api::findOrFail($id);
            \Log::info('找到接口', ['api' => $api->toArray()]);
            
            $state = request('state', 0);
            \Log::info('准备更新状态', ['old_state' => $api->state, 'new_state' => $state]);
            
            $result = $api->update(['state' => $state]);
            \Log::info('更新结果', ['result' => $result]);
            
            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => '状态更新成功',
                    'data' => ['id' => $id, 'state' => $state]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => '状态更新失败：数据库更新返回false'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('切换接口状态失败', [
                'id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => '状态更新失败：' . $e->getMessage()
            ]);
        }
    }
}
