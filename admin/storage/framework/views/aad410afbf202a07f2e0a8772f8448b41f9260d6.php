<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

         

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
              <?php
                $noticeCountBadge = (isset($navNotices) ? $navNotices : \App\Models\Article::where('cateid',6)->orderBy('id','desc')->limit(3)->get())->count();
                $noticeBadgeText = $noticeCountBadge >= 3 ? '3+' : (string)$noticeCountBadge;
              ?>
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter"><?php echo e($noticeBadgeText, false); ?></span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  最新公告
                </h6>
                <?php
                  $navNotices = \App\Models\Article::where('cateid',6)->orderBy('id','desc')->limit(3)->get();
                ?>
                <?php $__currentLoopData = $navNotices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="dropdown-item d-flex align-items-center" href="/notice_detail/<?php echo e($n->id, false); ?>" title="<?php echo e($n->title ?? $n->name, false); ?>">
                  <div class="mr-3">
                    <div class="icon-circle bg-primary">
                      <i class="fas fa-file-alt text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500"><?php echo e($n->created_at, false); ?></div>
                    <span class="font-weight-bold"><?php echo e($n->title ?? $n->name, false); ?></span>
                  </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <a class="dropdown-item text-center small text-gray-500" href="/notice">查看所有公告</a>
              </div>
            </li>

            

            <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
              <?php
                $msgCountBadge = (isset($navMsgs) ? $navMsgs : \App\Models\Message::where(function($q){
                        $u = Illuminate\Support\Facades\Auth::user();
                        $q->where('user_id',$u->id)
                          ->orWhere(function($qq){ $qq->where('user_id',0)->where('isagent',1); });
                      })->orderBy('created_at','desc')->limit(4)->get())->count();
                $msgBadgeText = $msgCountBadge >= 4 ? '4+' : (string)$msgCountBadge;
              ?>
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter"><?php echo e($msgBadgeText, false); ?></span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  站内信
                </h6>
                <?php
                  $navUser = Illuminate\Support\Facades\Auth::user();
                  $navMsgs = \App\Models\Message::where(function($q) use ($navUser){
                        $q->where('user_id',$navUser->id)
                          ->orWhere(function($qq){ $qq->where('user_id',0)->where('isagent',1); });
                      })
                      ->orderBy('created_at','desc')
                      ->limit(4)
                      ->get();
                ?>
                <?php $__currentLoopData = $navMsgs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="dropdown-item d-flex align-items-center" href="/message">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="/agent/img/sms.png" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate"><?php echo e($m->title ?? $m->content, false); ?></div>
                    <div class="small text-gray-500"><?php echo e($m->created_at, false); ?></div>
                  </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <a class="dropdown-item text-center small text-gray-500" href="/message">查看更多站内信</a>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">欢迎您：<?php echo e(Auth::user()->username, false); ?>，余额：<?php echo e(Auth::user()->balance, false); ?> &nbsp; </span>
                <img class="img-profile rounded-circle" src="/agent/img/tx.png">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                
                <a class="dropdown-item" href="<?php echo e(url('/editPassword'), false); ?>">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  修改密码
                </a>
               
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  退出
                </a>
              </div>
            </li>

          </ul>

        </nav><?php /**PATH B:\www\aiyou\admin\resources\views/agent/layouts/nav.blade.php ENDPATH**/ ?>