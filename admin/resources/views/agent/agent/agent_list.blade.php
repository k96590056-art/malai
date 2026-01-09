@extends('agent.layouts.agent_template')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h5>下级代理列表</h5>
</div>
<div class="input-group" style="z-index:999; padding-left:0; padding-bottom:20px;">
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="/agent-list">
        <div class="input-group">
            <input type="text" name="username" class="form-control bg-light border-0 small" placeholder="请输入账号..." aria-label="Search" aria-describedby="basic-addon2">
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
        
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto; padding: 0 !important;">
            <table class="table table-bordered" id="dataTable" width="1800" cellspacing="0" style="text-align:center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>账号</th>
                        <th>姓名</th>
                        <th>代理层级</th>
                        <th>上级代理</th>
                        <th>代理返点比例</th>
                        <th>系统余额</th>
                        <th>状态</th>
                        <th>操作</th>
                        <th>创建时间</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($list as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>
                            <div class="dropdown mb-4a">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{$item->username}}
                                </button>
                                <div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/recharge-log?username={{$item->username}}">代理充值记录</a>
                                    <a class="dropdown-item" href="/withdraw-log?username={{$item->username}}">代理提现记录</a>
                                </div>
                            </div>
                        </td>
                        <td>{{$item->realname}}</td>
                        <td>{{$item->level_text ?? '未知'}}</td>
                        <td>{{$item->parent}}</td>
                        <td>
                            {{$item->fanshuifee}}%
                            @if ($item->is_direct)
                            <a class="btn btn-danger btn-icon-split btn-sm" href="/changefanshui?uid={{$item->id}}">
                                <span class="text">设置比例</span></a>
                            @endif
                        </td>

                        <td>{{$item->balance}}
                        </td>
                        <td>
                            {{$item->isonline == 1 ? '在线' : '离线'}}
                        </td>
                        <td>
                            @if ($item->can_recharge ?? 0)
                            <a href="/recharge?user_id={{$item->id}}" class="btn btn-warning btn-icon-split btn-sm"><span class="text">充值</span></a>
                            @else
                            <span class="text-muted">无权越级</span>
                            @endif
                        </td>
                        <td>{{$item->created_at}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        {{$list->links()}}
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

