@extends('modstart::layout.frame')

@section('pageTitle'){{ empty($pageTitle) ? '' : $pageTitle }}@endsection
@section('pageKeywords'){{ empty($pageKeywords) ? '' : $pageKeywords }}@endsection
@section('pageDescription'){{ empty($pageDescription) ? '' : $pageDescription }}@endsection

@section('bodyAppend')
    @parent
    <script>
        $(function () {
            var $dialog = $('.ub-panel-dialog');
            var $body = $dialog.find('.panel-dialog-body');
            var $foot = $dialog.find('.panel-dialog-foot');
            if ($body.find('[data-ajax-form]').length) {
                $foot.find('[data-submit]').show().on('click', function () {
                    $dialog.find('[data-ajax-form]').submit();
                });
            }else{
                $foot.hide();
                $dialog.addClass('no-foot');
            }
            $foot.find('[data-close]').on('click', function () {
                parent.layer.closeAll();
            });
        });
    </script>
@endsection

@section('body')
    <div class="ub-panel-dialog">
        <div class="panel-dialog-body">
            @section('bodyContent')@show
        </div>
        <div class="panel-dialog-foot">
            <a href="javascript:;" data-close class="close btn">关闭</a>
            <a href="javascript:;" data-submit class="btn btn-primary" style="display:none;">确定</a>
        </div>
    </div>
@endsection
