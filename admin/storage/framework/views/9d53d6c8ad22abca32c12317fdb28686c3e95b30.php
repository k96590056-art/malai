<?php $__env->startSection('content'); ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary mb-4">
        佣金报表
        </h6>
        <div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
            <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="<?php echo e(url('/profit'), false); ?>" method="get">
                <div class="input-group">
                    <input type="text" class="form-control border-0 small" placeholder="请选择开始时间" id="start1" name="start" value="<?php echo e($start, false); ?>">&nbsp;&nbsp;
                    <input type="text" class="form-control border-0 small" placeholder="请选择结束时间" id="end1" name="end" value="<?php echo e($end, false); ?>">&nbsp;&nbsp;
                    
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm">
                            </i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;">账号</th>
                    <th rowspan="2" style="vertical-align: middle;">姓名</th>
                    <th rowspan="2" style="vertical-align: middle;">模式</th>
                    <th colspan="2">下级人数</th>
                    <th colspan="2">下级金流</th>
                    <th colspan="3">下级红利</th>
                    <th colspan="2"  style="vertical-align: middle;">下级有效投注</th>
                    <th rowspan="2"  style="vertical-align: middle;">下级输赢总金额</th>
                    <th rowspan="2" style="vertical-align: middle;">已结算佣金</th>
                    <th rowspan="2" style="vertical-align: middle;">未结算佣金</th>
                </tr>
                <tr>
                    <th>总人数</th>
                    <th>总代理数</th>
                    <th>总存款次数</th>
                    <th>总提款次数</th>
                    <th>总充值金额</th>
                    <th>总红包</th>
                    <th>总反水</th>
                    <th>总金额</th>
                    <th>总数量</th>
                </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item->username, false); ?></td>
                        <td><?php echo e($item->realname, false); ?></td>
                        <td><?php echo e($item->isagent == 1 ? '代理' : '会员', false); ?></td>
                        <td><?php echo e($item->usersum, false); ?></td>
                        <td><?php echo e($item->agentsum, false); ?></td>
                        <td><?php echo e($item->rechage_times, false); ?></td>
                        <td><?php echo e($item->withdraw_times, false); ?></td>
                        <td><?php echo e($item->all_recharge, false); ?></td>
                        <td><?php echo e($item->all_redpacket, false); ?></td>
                        <td><?php echo e($item->all_fanshui, false); ?></td>
                        <td><?php echo e($item->all_valid_bet, false); ?></td>
                        <td><?php echo e($item->all_valid_betsum, false); ?></td>
                        <td><?php echo e($item->all_win_loss, false); ?></td>
                        <td><?php echo e($item->yongjinsum, false); ?></td>
                        <td><?php echo e($item->waityongjinsum, false); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<script>
    lay('#version').html('-v' + laydate.v);

    //执行一个laydate实例
    laydate.render({
        elem: '#start1' //指定元素
    });

    laydate.render({
        elem: '#end1' //指定元素
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('agent.layouts.agent_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\bob\admin\resources\views/agent/report/commission.blade.php ENDPATH**/ ?>