<?php

namespace App\Admin\Actions\WorkOrder;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Reply extends Action
{
    /**
     * @var string
     */
    protected $title = '回复';

    /**
     * @var string
     */
    protected $modalId;

    public function __construct($title = null, $modalId = null)
    {
        $this->title = $title ?: $this->title;
        $this->modalId = $modalId ?: 'reply-modal-' . uniqid();
    }

    /**
     * 处理动作的响应
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        return $this->response()
            ->success('操作成功')
            ->refresh();
    }

    /**
     * 确认弹窗信息
     *
     * @return string|array|void
     */
    public function confirm()
    {
        return '确定要回复这个工单吗？';
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

        $this->setAttribute('data-toggle', 'modal');
        $this->setAttribute('data-target', '#' . $this->modalId);
        $this->setAttribute('class', 'btn btn-sm btn-info');
    }

    /**
     * 设置按钮的HTML，这里我们将返回一个按钮，点击后弹出回复表单
     *
     * @return string|void
     */
    public function html()
    {
        $modalId = $this->modalId;
        $workOrderId = $this->getKey();

        return <<<HTML
        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#{$modalId}">
            {$this->title}
        </button>
        
        <!-- 回复弹窗 -->
        <div class="modal fade" id="{$modalId}" tabindex="-1" role="dialog" aria-labelledby="{$modalId}Label" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="{$modalId}Label">回复工单</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="reply-form-{$workOrderId}">
                            <div class="form-group">
                                <label for="reply-content-{$workOrderId}">回复内容</label>
                                <textarea class="form-control" id="reply-content-{$workOrderId}" name="reply_content" rows="5" placeholder="请输入回复内容..." required></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary" onclick="submitReply({$workOrderId})">提交回复</button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function submitReply(workOrderId) {
            const content = document.getElementById('reply-content-' + workOrderId).value.trim();
            if (!content) {
                alert('请输入回复内容');
                return;
            }
            
            // 显示加载状态
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '提交中...';
            submitBtn.disabled = true;
            
            // 发送请求
            fetch('/admin/work-orders/' + workOrderId + '/reply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    reply_content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // 成功
                    Dcat.success(data.message);
                    // 关闭弹窗
                    $('#{$modalId}').modal('hide');
                    // 刷新页面
                    Dcat.reload();
                } else {
                    // 失败
                    Dcat.error(data.message || '回复失败');
                }
            })
            .catch(error => {
                Dcat.error('网络错误，请重试');
            })
            .finally(() => {
                // 恢复按钮状态
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
        </script>
HTML;
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
