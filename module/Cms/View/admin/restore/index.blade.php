@extends('modstart::admin.frame')

@section('pageTitle')数据恢复@endsection

@section($_tabSectionName)

    <div class="ub-alert warning">
        <i class="iconfont icon-warning"></i>
        说明：系统提供了CMS小数据量的恢复功能，如果数据库比较大，请使用专业的数据库操作软件。
    </div>

    <div class="ub-panel">
        <div class="head">
            <div class="title">
                数据恢复
            </div>
        </div>
        <div class="body">
            <table class="ub-table">
                <thead>
                <tr>
                    <th>目录</th>
                    <th>名称</th>
                    <th>大小</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                @if(empty($backups))
                    <tr>
                        <td colspan="4">
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
                        <td class="tw-font-mono">{{$backup['root']}}</td>
                        <td class="tw-font-mono">{{$backup['filename']}}</td>
                        <td class="tw-font-mono">{{\ModStart\Core\Util\FileUtil::formatByte($backup['size'])}}</td>
                        <td>
                            <a class="ub-lister-action" href="javascript:;"
                               data-confirm="确定恢复？"
                               data-method="post"
                               data-ajax-request-loading
                               data-ajax-request="{{modstart_admin_url('cms/restore/submit',['module'=>$backup['module'],'filename'=>$backup['filename']])}}">
                                <i class="iconfont icon-credit"></i>
                                恢复
                            </a>
                            <a class="ub-lister-action danger" href="javascript:;"
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
