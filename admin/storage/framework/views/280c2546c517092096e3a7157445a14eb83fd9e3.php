<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
    <?php if(!empty($app_logo)): ?>
      <img src="<?php echo e($app_logo, false); ?>" alt="logo" style="height:32px;max-height:40px;">
    <?php else: ?>
      <h4>*</h4>
    <?php endif; ?>
    <div class="sidebar-brand-text mx-3">代理后台管理</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="/">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>代理首页</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Nav Item - Pages Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
      <i class="fas fa-fw fa-cog">
      </i>
      <span>
        首页内容
      </span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="<?php echo e(url('/notice'), false); ?>">
          公告信息
        </a>
        <a class="collapse-item" href="<?php echo e(url('/message'), false); ?>">
          站内信
        </a>
      </div>
    </div>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
      <i class="fas fa-fw fa-cog">
      </i>
      <span>
        代理概况
      </span>
    </a>
    <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="<?php echo e(url('/chart'), false); ?>">
          图表概况
        </a>
        <a class="collapse-item" href="<?php echo e(url('/today'), false); ?>">
          今日概况
        </a>
        <a class="collapse-item" href="<?php echo e(url('/profit'), false); ?>">
          盈亏报表
        </a>
          <a class="collapse-item" href="<?php echo e(url('/commission'), false); ?>">
              佣金报表
          </a>
          <a class="collapse-item" href="<?php echo e(url('/subordinate'), false); ?>">
              下级代理佣金报表
          </a>

      </div>
    </div>
  </li>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
      <i class="fas fa-fw fa-cog">
      </i>
      <span>
        代理页面
      </span>
    </a>
    <div id="collapseFour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="<?php echo e(url('/add-member'), false); ?>">
          添加下级会员
        </a>
        <a class="collapse-item" href="<?php echo e(url('/editPassword'), false); ?>">
          修改密码
        </a>
        <a class="collapse-item" href="<?php echo e(url('/memberlist'), false); ?>">
          下级会员列表
        </a>
        <a class="collapse-item" href="<?php echo e(url('/bet-log'), false); ?>">
          下级会员下注记录
        </a>
        <a href="<?php echo e(url('/recharge-log'), false); ?>" class="collapse-item">
          下级会员充值记录
        </a>
        <a href="<?php echo e(url('/withdraw-log'), false); ?>" class="collapse-item">
          下级会员提现记录
        </a>
        <a href="<?php echo e(url('/transfer-log'), false); ?>" class="collapse-item">
          下级会员转账记录
        </a>
          <a href="<?php echo e(url('/rebate'), false); ?>" class="collapse-item">
              下级会员返水记录
          </a>


      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>
<?php /**PATH /www/wwwroot/admin/resources/views/agent/layouts/aside.blade.php ENDPATH**/ ?>