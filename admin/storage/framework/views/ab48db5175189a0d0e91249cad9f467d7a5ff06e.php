<?php $__env->startSection('content'); ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary mb-4">
            今日概况
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>下级会员总数</th>
                        <th>下级代理总数</th>
                        <th>直属会员数</th>
                        <th>下级直属代理数</th>
                        <th>今日新增会员数</th>
                        <th>今日总存款</th>
                        <th>今日总提款</th>
                        <th>今日投注</th>
                        <th>今日有效投注</th>
                        <th>今日输赢</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td><?php echo e($child_member_count, false); ?></td>
                        <td><?php echo e($child_agent_count, false); ?></td>
                        <td><?php echo e($directly_member_count, false); ?></td>
                        <td><?php echo e($directly_agent_count, false); ?></td>
                        <td><?php echo e($add_member_count, false); ?></td>
                        <td><?php echo e($all_recharge, false); ?></td>
                        <td><?php echo e($all_withdraw, false); ?></td>
                        <td><?php echo e($all_bet, false); ?></td>
                        <td><?php echo e($all_valid_bet, false); ?></td>
                        <td><?php echo e($win_loss, false); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    $('#collapseThree').addClass('show');
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('agent.layouts.agent_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\bob\admin\resources\views/agent/report/today_data.blade.php ENDPATH**/ ?>