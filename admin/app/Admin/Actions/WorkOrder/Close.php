<?php

namespace App\Admin\Actions\WorkOrder;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Close extends Action
{
    /**
     * @var string
     */
    protected $title = '关闭';

    /**
     * 处理动作的响应
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        $workOrderId = $this->getKey();
        
        try {
            // 发送关闭请求
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-CSRF-TOKEN' => csrf_token(),
            ])->post("/admin/work-orders/{$workOrderId}/close");
            
            if ($response->successful()) {
                $data = $response->json();
                return $this->response()
                    ->success($data['message'] ?? '工单关闭成功')
                    ->refresh();
            } else {
                $data = $response->json();
                return $this->response()
                    ->error($data['message'] ?? '工单关闭失败');
            }
        } catch (\Exception $e) {
            return $this->response()
                ->error('工单关闭失败：' . $e->getMessage());
        }
    }

    /**
     * 确认弹窗信息
     *
     * @return string|array|void
     */
    public function confirm()
    {
        return '确定要关闭这个工单吗？关闭后将无法回复。';
    }

    /**
     * 设置要POST到接口的数据
     *
     * @return array
     */
    public function parameters()
    {
        return [];
    }

    /**
     * 设置按钮的HTML属性
     *
     * @return void
     */
    protected function setupHtmlAttributes()
    {
        parent::setupHtmlAttributes();

        $this->setAttribute('class', 'btn btn-sm btn-warning');
    }

    /**
     * 检查权限
     *
     * @param Model|Authenticatable|HasPermissions|null $user
     *
     * @return bool
     */
    protected function authorize($user): bool
    {
        return true;
    }
}
