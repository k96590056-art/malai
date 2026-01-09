@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>代理概况</h5>

</div>

<!-- Content Row -->
<div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">下级会员充值总额</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$all_recharge}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">下级会员提现总额</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$all_withdraw}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">下级会员总投注</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{$all_valid_bet}}</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">下级会员返水总额</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$all_win_loss}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->

<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">下级会员资金存取数据汇总</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">存取投注记录</div>
                        <a class="dropdown-item" href="#">下级会员充值记录</a>
                        <a class="dropdown-item" href="#">下级会员提现记录</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">下级会员下注记录</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="mt-4a text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> 存款
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> 提款
                    </span>
                    <span class="mr-2">刷新时间：2020-08-18 00:00:00</span>
                </div>
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">下级会员存取总金额比例</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">统计报表</div>
                        <a class="dropdown-item" href="#">盈亏报表</a>
                        <a class="dropdown-item" href="#">佣金报表</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">下级代理佣金报表</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="mt-4a text-center small">
                    <span class="mr-2">刷新时间：2020-08-18 00:00:00</span>
                </div>
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> 利润
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> 总支出
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> 总收入
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Content Column -->
    <div class="col-lg-6 mb-4">

        <!-- Project Card Example -->
        <!-- Approach -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">最新公告</h6>
            </div>
            <div class="card-body">
                @foreach($list as $item)
                    <div style="padding-bottom:12px; padding-top:3px;">
                        <div class="text-truncate"><i class="fas fa-info-circle"></i>
                            <a href="/notice_detail/{{$item->id}}" rel="nofollow" target="_blank" title="{{ $item->title ?? $item->name }}">{{ $item->title ?? $item->name }}</a></div>
                        <div class="small text-gray-500" style="text-align:left; padding-left:20px;"> {{$item->created_at}}</div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>

    <div class="col-lg-6 mb-4">

        <!-- Illustrations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">代理推广</h6>

            </div>
            <div class="card-body">
                <p>尊敬的代理 <font color="#f00">{{$user->username}}</font>，欢迎您的加入！</p>
                <p>您的推荐码：<font color="burlywood"><b>{{$user->id}}</b></font>
                </p>
                <p>电脑端推广链接：<font color="burlywood"><b>{{env('AGENT_URL')}}/promotion?pid={{$user->id}}&type=pc</b></font>
                    <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('{{env('AGENT_URL')}}/promotion?pid={{$user->id}}&type=pc')">
                        <i class="fas fa-copy"></i> 复制
                    </button>
                    <small class="text-muted d-block mt-1">（手机端打开将自动跳转到手机端链接）</small>
                </p>
                <p>手机端推广链接：<font color="burlywood"><b>{{env('AGENT_URL')}}/promotion?pid={{$user->id}}&type=wap</b></font>
                    <button class="btn btn-sm btn-outline-success ml-2" onclick="copyToClipboard('{{env('AGENT_URL')}}/promotion?pid={{$user->id}}&type=wap')">
                        <i class="fas fa-copy"></i> 复制
                    </button>
                    <small class="text-muted d-block mt-1">（电脑端打开将自动跳转到电脑端链接）</small>
                </p>
                <div class="text-center">
                    <div id="qrcode-container" style="display: flex; justify-content: center; align-items: center; min-height: 200px;">
                        <img id="qrcode-img" src="/agent/qrcode/{{$user->id}}" alt="推广二维码" style="width: 200px; height: 200px; border: 1px solid #ddd;">
                    </div>
                    <a href="/download-qrcode" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mt-3"><i class="fas fa-download fa-sm text-white-50"></i> 保存我的推广二维码</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
// 简单的二维码加载处理
document.addEventListener('DOMContentLoaded', function() {
    const qrcodeImg = document.getElementById('qrcode-img');
    
    if (qrcodeImg) {
        qrcodeImg.onload = function() {
            console.log('二维码加载成功');
        };
        
        qrcodeImg.onerror = function() {
            console.log('二维码加载失败');
        };
    }
});

function copyToClipboard(text) {
    // 创建临时输入框
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        // 尝试使用现代浏览器的clipboard API
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showCopySuccess();
            }).catch(() => {
                fallbackCopy();
            });
        } else {
            fallbackCopy();
        }
    } catch (err) {
        fallbackCopy();
    }
    
    document.body.removeChild(textArea);
    
    function fallbackCopy() {
        try {
            document.execCommand('copy');
            showCopySuccess();
        } catch (err) {
            alert('复制失败，请手动复制：' + text);
        }
    }
    
    function showCopySuccess() {
        // 显示成功提示
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        toast.textContent = '链接已复制到剪贴板！';
        document.body.appendChild(toast);
        
        // 3秒后自动消失
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }
}
</script>
