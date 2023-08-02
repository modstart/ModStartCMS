@extends('modstart::admin.frame')

@section('pageTitle')数据备份@endsection

@section($_tabSectionName)

    <div class="ub-alert warning">
        <i class="iconfont icon-warning"></i>
        说明：系统提供了CMS小数据量的备份功能，如果数据库比较大，请使用专业的数据库操作软件。
    </div>

    <div class="ub-panel">
        <div class="head">
            <div class="title">
                数据备份
            </div>
        </div>
        <div class="body">
            <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" data-ajax-form method="post">
                <div class="ub-form">
                    <div class="line">
                        <div class="label">备份CMS表</div>
                        <div class="field">
                            @foreach(\Module\Cms\Util\CmsBackupUtil::listBackupTables() as $table)
                                <div class="tw-font-mono">
                                    <input type="checkbox" style="vertical-align:middle;" name="table[]" checked value="{{$table}}" />
                                    {{$table}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">
                            备份保存目录
                        </div>
                        <div class="field">
                            <select name="module">
                                @foreach(\Module\Cms\Provider\Theme\CmsThemeProvider::all() as $theme)
                                    <option value="{{$theme->name()}}">module/{{$theme->name()}}/Backup</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">备份文件名称</div>
                        <div class="field">
                            <input name="filename" class="form tw-w-full" value="{{date('Ymd_His')}}" />
                            <div class="help">
                                规则：数字字母下划线组成
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">&nbsp;</div>
                        <div class="field">
                            <button class="btn btn-primary" type="submit">开始备份</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>



@endsection
