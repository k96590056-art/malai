<span class="dropdown" style="display:inline-block">
    <a id="" class="dropdown-toggle btn btn-sm btn-white waves-effect" data-toggle="dropdown" href="javascript:void(0)">
        <stub class="<?php echo e($value, false); ?>"><?php echo e($value, false); ?></stub> <span class="caret"></span>
    </a>
    <!--<span class="copyClick">复制</span>-->
    <span class="copyClick">
        <i class='fa fa-clone ' style="color:#3085d6;"></i>
        <input style="border: 0;cursor: pointer;outline: none;background-color: rgba(0, 0, 0, 0);color:#fff;opacity: 0;position: absolute;"
        class="copyValue"  value=<?php echo e($value, false); ?> type="text">
    </span>
    <ul class="dropdown-menu" style="left: 0px; right: inherit;">
        <li class="dropdown-item"><a href="/<?php echo e(env('ADMIN_ROUTE_PREFIX'), false); ?>/recharge?user_data%5Busername%5D=<?php echo e($value, false); ?>">充值记录</a></li>
        <li class="dropdown-item"><a href="/<?php echo e(env('ADMIN_ROUTE_PREFIX'), false); ?>/withdraws?user_data%5Busername%5D=<?php echo e($value, false); ?>">提现记录</a></li>
        <li class="dropdown-item"><a href="/<?php echo e(env('ADMIN_ROUTE_PREFIX'), false); ?>/transfer-logs?user_data%5Busername%5D=<?php echo e($value, false); ?>">额度转换记录</a></li>
        <li class="dropdown-item"><a href="/<?php echo e(env('ADMIN_ROUTE_PREFIX'), false); ?>/user-operate-logs?user_data%5Busername%5D=<?php echo e($value, false); ?>">登录日志</a></li>
        <li class="dropdown-item"><a href="/<?php echo e(env('ADMIN_ROUTE_PREFIX'), false); ?>/usercard?user_data%5Busername%5D=<?php echo e($value, false); ?>">银行卡管理</a></li>
    </ul>
</span><?php /**PATH B:\www\Galaxy\admin\resources\views/admin/field/user_username.blade.php ENDPATH**/ ?>