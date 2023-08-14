@extends('modstart::admin.frame')

@section('pageTitle'){{'超级搜索索引同步'}}@endsection

@section('bodyAppend')
    @parent
    <script>
        $(function () {
            $('[data-refresh]').on('click', function () {
                var $bucket = $(this).closest('[data-bucket]');
                $bucket.find('[data-status]').html('<span class="ub-text-muted">正在刷新</span>');
                var action = $(this).attr('data-refresh')
                MS.api.post(window.location.href, {
                    action: 'refresh',
                    bucket: $bucket.attr('data-bucket')
                }, function (res) {
                    $bucket.find('[data-status]').html('');
                    MS.api.defaultCallback(res, {
                        success: function (res) {
                            $bucket.find('[data-count]').html(res.data.count);
                        },
                        error: function (res) {
                            $bucket.find('[data-status]').html('<span class="ub-text-danger">' + res.msg + '</span>');
                        }
                    });
                });
            });
            var sync = function ($bucket, bucket, nextId) {
                $bucket.find('[data-status]').show().html('<span class="ub-text-muted">正在同步（' + bucket + ',' + nextId + '）</span>');
                MS.api.post(window.location.href, {action: 'sync', bucket: bucket, nextId: nextId}, function (res) {
                    MS.api.defaultCallback(res, {
                        success: function (res) {
                            if (res.data.count > 0) {
                                setTimeout(function () {
                                    sync($bucket, bucket, res.data.nextId);
                                }, 0);
                            } else {
                                $bucket.find('[data-refresh]').click();
                                MS.dialog.alertSuccess('同步完成（' + bucket + ',' + nextId + '）');
                                $bucket.find('[data-status]').html('<span class="ub-text-success">同步完成（' + bucket + ',' + nextId + '）</span>');
                            }
                        },
                        error: function (res) {
                            $bucket.find('[data-status]').html('<span class="ub-text-danger">' + res.msg + '</span>');
                        }
                    });
                });
            };
            $('[data-sync]').on('click', function () {
                var $bucket = $(this).closest('[data-bucket]');
                sync($bucket, $bucket.attr('data-bucket'), 0);
            });
        });
    </script>
@endsection

@section($_tabSectionName)

    @if(empty($provider))
        <div class="ub-alert danger">
            <i class="iconfont icon-warning"></i>
            没有配置超级搜索驱动，
            请安装 <a href="https://modstart.com/m/SuperSearchES" target="_blank">ElasticSearch</a> 超级搜索驱动。
        </div>
    @endif

    <div class="ub-panel">
        <div class="head">
            <div class="title">超级搜索索引同步</div>
        </div>
        <div class="body">
            <table class="ub-table">
                <thead>
                <tr>
                    <th>标识</th>
                    <th>标题</th>
                    <th width="200">数量</th>
                    <th width="400">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bizList as $biz)
                    <tr data-bucket="{{$biz->name()}}">
                        <td>{{$biz->name()}}</td>
                        <td>{{$biz->title()}}</td>
                        <td>
                            <span data-count>-</span>
                        </td>
                        <td>
                            @if(!empty($provider))
                                <a href="javascript:;" class="action-btn" data-refresh>
                                    刷新
                                </a>
                                <a href="javascript:;" class="action-btn" data-sync>
                                    全量同步
                                </a>
                                <span class="margin-left" data-status></span>
                            @else
                                <span class="ub-text-warning">不可用</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
