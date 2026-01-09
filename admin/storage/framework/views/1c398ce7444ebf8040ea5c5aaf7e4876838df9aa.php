<?php $__env->startSection('content'); ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary mb-4">
            公告信息
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            标题
                        </th>
                        <th>
                            发布时间
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php echo e($item->id, false); ?>

                        </td>
                        <td>
                            <a href="/notice_detail/<?php echo e($item->id, false); ?>" target="_blank" title="<?php echo e($item->title ?? $item->name, false); ?>"><?php echo e($item->title ?? $item->name, false); ?></a>
                        </td>
                        <td>
                            <?php echo e($item->created_at, false); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        <?php echo e($list->links(), false); ?>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    $('#collapseTwo').addClass('show');
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('agent.layouts.agent_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/admin/resources/views/agent/notice/notice.blade.php ENDPATH**/ ?>