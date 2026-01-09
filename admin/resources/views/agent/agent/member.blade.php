@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>下级会员列表</h5>
</div>
<div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="/memberlist">
        <div class="input-group">
            <input type="text" name="username" class="form-control bg-light border-0 small" placeholder="请输入账号..." aria-label="Search" aria-describedby="basic-addon2" value="{{request('username')}}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>
</div>
<!-- DataTales Example -->
<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">下级会员列表</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto; padding: 0 !important;">
            <table class="table table-bordered" id="dataTable" width="1800" cellspacing="0" style="text-align:center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>账号</th>
                        <th>姓名</th>
                        <th>上级代理</th>
                        <th>代理返点比例</th>
                        <th>系统余额</th>
                        <th>游戏余额</th>
                        <th>创建时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($list as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->username}}</td>
                        <td>{{$item->realname}}</td>
                        <td>{{$item->parent}}</td>
                        <td>{{$item->fanshuifee}}%</td>
                        <td>{{$item->balance}}</td>
                        <td>-</td>
                        <td>{{$item->created_at}}</td>
                        <td>
                            {{$item->isonline == 1 ? '在线' : '离线'}}
                        </td>
                        <td>
                            @php
                                $currentAgentLevel = Auth::user()->agent_level ?? 0;
                            @endphp
                            @if ($currentAgentLevel >= 2)
                            <a href="/recharge?user_id={{$item->id}}" class="btn btn-warning btn-icon-split btn-sm"><span class="text">充值</span></a>
                            @else
                            <span class="text-muted">仅查看</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        {{$list->appends(request()->query())->links()}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $('#collapseFour').addClass('show');
</script>
@endsection
