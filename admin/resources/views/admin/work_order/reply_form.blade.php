<div class="work-order-reply-form">
    <h4>回复工单</h4>
    
    @if($workOrder->canReply())
        <form id="reply-form-{{ $workOrder->id }}" class="mt-3">
            <div class="form-group">
                <label for="reply-content-{{ $workOrder->id }}">回复内容</label>
                <textarea 
                    class="form-control" 
                    id="reply-content-{{ $workOrder->id }}" 
                    name="reply_content" 
                    rows="4" 
                    placeholder="请输入回复内容..." 
                    required
                ></textarea>
                <small class="form-text text-muted">最多1000个字符</small>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-primary" onclick="submitReply({{ $workOrder->id }})">
                    提交回复
                </button>
            </div>
        </form>
        
        <script>
        function submitReply(workOrderId) {
            const content = document.getElementById('reply-content-' + workOrderId).value.trim();
            if (!content) {
                alert('请输入回复内容');
                return;
            }
            
            if (content.length > 1000) {
                alert('回复内容不能超过1000个字符');
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
    @else
        <div class="alert alert-warning">
            工单已关闭，无法回复
        </div>
    @endif
</div>

<style>
.work-order-reply-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
}

.work-order-reply-form h4 {
    margin-bottom: 15px;
    color: #333;
}

.work-order-reply-form .form-group {
    margin-bottom: 15px;
}

.work-order-reply-form .form-control {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.work-order-reply-form .form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.work-order-reply-form .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.work-order-reply-form .btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.work-order-reply-form .btn-primary:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
