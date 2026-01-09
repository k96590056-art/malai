@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    @php
      $currentAgentLevel = Auth::user()->agent_level ?? 0;
      $pageTitle = ($currentAgentLevel < 2) ? '添加下级代理' : '添加会员';
      $formTitle = ($currentAgentLevel < 2) ? '注册代理资料' : '注册会员资料';
    @endphp
    <h5>{{ $pageTitle }}</h5>
</div>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ $formTitle }}</h6>
    </div>
    <form class="user" action="/add-member" method="post" onsubmit="return checkForm()">
        @csrf
        <div class="modal-body" style="padding-top:35px;">
            <div class="form-group row">
                <div class="col-sm-6a">
                    账号：
                </div>
                <div class="col-sm-6b">
                    @if($currentAgentLevel >= 2 && isset($generatedUsername) && $generatedUsername)
                        <input type="text" name="username" class="form-control form-control-user" value="{{ $generatedUsername }}" readonly required>
                    @else
                    <input type="text" name="username" class="form-control form-control-user" placeholder="* 请输入长度6-9位,可输入英文字母 或数字" required minlength="6">
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    密码：
                </div>
                <div class="col-sm-6b">
                    <input type="password" id="password" name="password" class="form-control form-control-user" placeholder="* 密码规则:须为6-24位英 文或数字的字符" required minlength="6">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    确认密码：
                </div>
                <div class="col-sm-6b">
                    <input type="password" id="repassword" class="form-control form-control-user" placeholder="* 请再次输入密码" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    真实姓名：
                </div>
                <div class="col-sm-6b">
                    <input type="text" name="realname" class="form-control form-control-user" placeholder="* 请输入中文姓名" required>
                </div>
            </div>
            @if($currentAgentLevel < 2)
            <div class="form-group row">
                <div class="col-sm-6a">
                    AutoCode：
                </div>
                <div class="col-sm-6b">
                    <input type="text" name="autocode" class="form-control form-control-user" placeholder="请输入AutoCode">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-6a">
                    SecretKey：
                </div>
                <div class="col-sm-6b">
                    <input type="text" name="secretkey" class="form-control form-control-user" placeholder="请输入SecretKey">
                </div>
            </div>
            @endif
            <div class="form-group row">
                <div class="col-sm-6a">
                    取款密码：
                </div>
                <div class="col-sm-6b" style="padding-top:15px;">
                    @php
                      $userTypeText = ($currentAgentLevel < 2) ? '代理' : '会员';
                    @endphp
                    * 默认取款密码为<font color="#f00">【123456】</font>{{ $userTypeText }}登录后可自行更改，请务必记住！

                </div>
            </div>
            <div class="form-group row" style="padding-top:15px;">
                <div class="col-sm-6a"></div>
                <div class="col-sm-6b">
                    <button type="submit" class="btn btn-google btn-user btn-block">
                        立即注册
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@section('js')
<script src="/agent/js/laydate/laydate.js"></script>
<script>
    $('#collapseFour').addClass('show');
</script>
<script>
function checkForm() {
    var password = $('#password').val();
    var repassword = $('#repassword').val();
    if (password != repassword) {
        alert('两次密码输入不一致');
        return false;
    }
}
</script>
@endsection