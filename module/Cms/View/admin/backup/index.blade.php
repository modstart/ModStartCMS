@extends('modstart::admin.frame')

@section('pageTitle')数据库备份@endsection

@section($_tabSectionName)

    <div class="ub-alert warning">
        <i class="iconfont icon-warning"></i>
        说明：系统提供了CMS小数据量的备份功能，如果数据库比较大，请使用专业的数据库操作软件。
    </div>

    <div class="ub-panel">
        <div class="head">
            <div class="title">
                数据库备份
            </div>
        </div>
        <div class="body">
            <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" data-ajax-form method="post">
                <div class="ub-form">
                    <div class="line">
                        <div class="label">备份CMS表</div>
                        <div class="field">
                            @foreach(\Module\Cms\Util\CmsBackupUtil::listBackupTables() as $table)
                                <label class="tw-font-mono tw-block">
                                    <input type="checkbox" style="vertical-align:middle;" name="table[]"
                                           value="{{$table['name']}}"
                                           @if($table['checked']) checked @endif
                                    />
                                    {{$table['name']}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">备份配置</div>
                        <div class="field">
                            @foreach($configs as $c)
                                <label class="tw-font-mono tw-inline-block" style="min-width:15rem;">
                                    <input type="checkbox" style="vertical-align:middle;" name="config[]"
                                           value="{{$c['key']}}"
                                    />
                                    {{$c['key']}}
                                </label>
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
                            <script>
                                $(function () {
                                    var change = function () {
                                        var val = $('[name="module"]').val();
                                        $('[name="config[]"]').each(function () {
                                            var $this = $(this);
                                            if ($this.val().indexOf(val) === 0) {
                                                $this.prop('checked', true);
                                            } else {
                                                $this.prop('checked', false);
                                            }
                                        });
                                    };
                                    $('[name="module"]').on('change', change);
                                });
                            </script>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">备份文件名称</div>
                        <div class="field">
                            <input name="filename" class="form tw-w-full" value="{{date('Ymd_His')}}"/>
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
