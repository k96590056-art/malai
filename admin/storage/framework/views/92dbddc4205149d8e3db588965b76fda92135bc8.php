<?php $__env->startSection('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>公告信息</h5>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">公告内容</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <tbody>
                    <tr>
                        <td align="center"><b><?php echo e($item->title ?? $item->name, false); ?></b></td>
                    </tr>
                    <tr>
                        <td align="center">发布时间：<?php echo e($item->created_at, false); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $item->content ?? ''; ?></td>
                    </tr>
                    <tr>
                        <!-- <td align="right">
                            <a href="notice.html" class="btn btn-warning btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                                <span class="text">返回公告列表</span>
                            </a>
                        </td> -->

                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('agent.layouts.agent_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/admin/resources/views/agent/notice/notice_detail.blade.php ENDPATH**/ ?>