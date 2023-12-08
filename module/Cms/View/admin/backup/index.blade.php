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
                        <div class="label">备份数据表</div>
                        <div class="field">
                            @foreach(\Module\Cms\Util\CmsBackupUtil::listBackupTables() as $table)
                                <label class="tw-font-mono tw-block">
                                    <input type="checkbox" style="vertical-align:middle;" name="table[]"
                                           value="{{$table['name']}}"
                                           @if($table['checked']) checked @endif
                                    />
                                    {{$table['name']}}
                                    @if(!empty($table['title']))
                                        <span class="ub-text-muted">
                                            {{$table['title']}}
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">
                            备份配置
                            <br>
                            <a href="javascript:;" class="ub-text-muted"
                               data-tip-popover="点击显示配置项的值"
                               onclick="$('[data-config-item]').next().toggle();">
                                <i class="iconfont icon-eye"></i>
                            </a>
                        </div>
                        <div class="field">
                            @foreach(\Module\Cms\Util\CmsBackupUtil::listBackupConfigs() as $c)
                                <label data-config-item class="tw-font-mono tw-inline-block tw-bg-white"
                                       style="min-width:20rem;">
                                    <input type="checkbox" style="vertical-align:middle;" name="config[]"
                                           value="{{$c['key']}}"
                                    />
                                    {{$c['key']}}
                                    @if(!empty($c['title']))
                                        <span class="ub-text-muted">
                                            {{$c['title']}}
                                        </span>
                                    @endif
                                </label>
                                <div class="tw--mt-12" style="display:none;">
                                    <pre class="tw-bg-gray-100 tw-pt-10">{{$c['value']}}</pre>
                                </div>
                            @endforeach
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
