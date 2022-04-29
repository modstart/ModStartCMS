@extends('modstart::admin.frame')

@section('pageTitle'){{'标签云构建'}}@endsection

@section('bodyAppend')
    @parent
    <script>
        $(function () {
            $('[data-refresh]').on('click', function () {
                var $biz = $(this).closest('[data-biz]');
                $biz.find('[data-status]').html('<span class="ub-text-muted">正在刷新</span>');
                var action = $(this).attr('data-refresh')
                MS.api.post(window.location.href, {
                    action: 'refresh',
                    biz: $biz.attr('data-biz')
                }, function (res) {
                    $biz.find('[data-status]').html('');
                    MS.api.defaultCallback(res, {
                        success: function (res) {
                            $biz.find('[data-count]').html(res.data.count);
                        },
                        error: function (res) {
                            $biz.find('[data-status]').html('<span class="ub-text-danger">' + res.msg + '</span>');
                        }
                    });
                });
            });
            var sync = function ($biz, biz, nextId) {
                $biz.find('[data-status]').show().html('<span class="ub-text-muted">正在同步（' + biz + ',' + nextId + '）</span>');
                MS.api.post(window.location.href, {action: 'sync', biz: biz, nextId: nextId}, function (res) {
                    MS.api.defaultCallback(res, {
                        success: function (res) {
                            if (res.data.finish) {
                                $biz.find('[data-refresh]').click();
                                MS.dialog.alertSuccess('同步完成（' + biz + ',' + nextId + '）');
                                $biz.find('[data-status]').html('<span class="ub-text-success">同步完成（' + biz + ',' + nextId + '）</span>');
                            } else {
                                setTimeout(function () {
                                    sync($biz, biz, res.data.nextId);
                                }, 0);
                            }
                        },
                        error: function (res) {
                            $biz.find('[data-status]').html('<span class="ub-text-danger">' + res.msg + '</span>');
                        }
                    });
                });
            };
            $('[data-sync]').on('click', function () {
                var $biz = $(this).closest('[data-biz]');
                sync($biz, $biz.attr('data-biz'), 0);
            });
        });
    </script>
@endsection

@section('bodyContent')

    @if(empty($bizList))
        <div class="ub-alert ub-alert-danger">
            <i class="iconfont icon-warning"></i>
            没有支持标签云的模块
        </div>
    @endif

    <div class="ub-panel">
        <div class="head">
            <div class="title">标签云构建</div>
        </div>
        <div class="body">
            <table class="ub-table">
                <thead>
                <tr>
                    <th>业务</th>
                    <th>标识</th>
                    <th width="200">数量</th>
                    <th width="400">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bizList as $biz)
                    <tr data-biz="{{$biz->name()}}">
                        <td>{{$biz->title()}}</td>
                        <td>{{$biz->name()}}</td>
                        <td>
                            <span data-count>-</span>
                        </td>
                        <td>
                            <a href="javascript:;" class="action-btn" data-refresh>
                                刷新
                            </a>
                            <a href="javascript:;" class="action-btn" data-sync>
                                全量同步
                            </a>
                            <span class="margin-left" data-status></span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
