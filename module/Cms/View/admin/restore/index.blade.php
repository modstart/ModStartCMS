@extends('modstart::admin.frame')

@section('pageTitle')数据库恢复@endsection

@section($_tabSectionName)

    <div class="ub-alert warning">
        <i class="iconfont icon-warning"></i>
        说明：系统提供了CMS小数据量的恢复功能，如果数据库比较大，请使用专业的数据库操作软件。
    </div>

    <div class="ub-panel">
        <div class="head">
            <div class="title">
                数据库恢复
            </div>
        </div>
        <div class="body">
            <table class="ub-table">
                <thead>
                <tr>
                    <th class="ub-text-truncate">备份</th>
                    <th>
                        数据表
                        <a href="javascript:;" class="ub-text-muted"
                           data-tip-popover="恢复后这些表会被覆盖，记得提前备份">
                            <i class="iconfont icon-warning"></i>
                        </a>
                    </th>
                    <th>
                        配置
                        <a href="javascript:;" class="ub-text-muted"
                           data-tip-popover="恢复后如果存在同名配置，这些配置会被覆盖，记得提前备份">
                            <i class="iconfont icon-warning"></i>
                        </a>
                    </th>
                    <th>大小</th>
                    <th width="150">操作</th>
                </tr>
                </thead>
                <tbody>
                @if(empty($backups))
                    <tr>
                        <td colspan="5">
                            <div class="ub-empty">
                                <div class="icon">
                                    <div class="iconfont icon-empty-box"></div>
                                </div>
                                <div class="text">暂无备份</div>
                            </div>
                        </td>
                    </tr>
                @endif
                @foreach($backups as $backup)
                    <tr>
                        <td class="tw-font-mono ub-text-truncate">
                            <div class="ub-text-bold">
                                <span>
                                    {{$backup['moduleInfo']['title']}}
                                </span>
                                <span class="ub-text-sm">
                                    {{$backup['module']}}
                                </span>
                            </div>
                            <div>
                                名称：{{$backup['filename']}}
                            </div>
                        </td>
                        <td class="tw-font-mono">
                            @foreach($backup['tables'] as $table)
                                <span class="ub-tag tw-mb-1 ub-text-sm">{{$table}}</span>
                            @endforeach
                        </td>
                        <td class="tw-font-mono">
                            @foreach($backup['config'] as $k=>$v)
                                <span class="ub-tag tw-mb-1 ub-text-sm">{{$k}}</span>
                            @endforeach
                        </td>
                        <td class="tw-font-mono ub-text-truncate">{{\ModStart\Core\Util\FileUtil::formatByte($backup['size'])}}</td>
                        <td>
                            <a class="btn btn-warning btn-sm tw-mb-1" href="javascript:;"
                               data-confirm="确定恢复？"
                               data-method="post"
                               data-ajax-request-loading
                               data-ajax-request="{{modstart_admin_url('cms/restore/submit',['module'=>$backup['module'],'filename'=>$backup['filename']])}}">
                                <i class="iconfont icon-credit"></i>
                                恢复
                            </a>
                            <a class="btn btn-danger btn-sm tw-mb-1" href="javascript:;"
                               data-confirm="确定删除？"
                               data-method="post"
                               data-ajax-request-loading
                               data-ajax-request="{{modstart_admin_url('cms/restore/delete',['module'=>$backup['module'],'filename'=>$backup['filename']])}}">
                                <i class="iconfont icon-trash"></i>
                                删除
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



@endsection
