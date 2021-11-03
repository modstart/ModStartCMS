@extends('modstart::admin.frame')

@section('pageTitle')模板管理@endsection

@section('bodyContent')
    <div class="ub-alert">
        <i class="iconfont icon-warning"></i>
        请使用专业IDE对模板进行操作。
    </div>
    <div class="ub-panel">
        <div class="head">
            <div class="title">列表模板</div>
        </div>
        <div class="body">
            <table class="ub-table border">
                <thead>
                    <tr>
                        <td width="200">模板</td>
                        <td>渲染优先级</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\Module\Cms\Util\CmsTemplateUtil::allListTemplates() as $k=>$fs)
                        <tr>
                            <td><code>{{$k}}</code></td>
                            <td>
                                @foreach($fs as $i=>$f)
                                    <div class="tw-py-2">
                                        <span class="tw-w-6 tw-h-6 tw-text-center tw-leading-6 tw-bg-gray-200 tw-inline-block tw-rounded-full">{{$i+1}}</span>
                                        <code>{{$f['_path']}}</code>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="ub-panel">
        <div class="head">
            <div class="title">详情模板</div>
        </div>
        <div class="body">
            <table class="ub-table border">
                <thead>
                <tr>
                    <td width="200">模板</td>
                    <td>渲染优先级</td>
                </tr>
                </thead>
                <tbody>
                @foreach(\Module\Cms\Util\CmsTemplateUtil::allDetailTemplates() as $k=>$fs)
                    <tr>
                        <td><code>{{$k}}</code></td>
                        <td>
                            @foreach($fs as $i=>$f)
                                <div class="tw-py-2">
                                    <span class="tw-w-6 tw-h-6 tw-text-center tw-leading-6 tw-bg-gray-200 tw-inline-block tw-rounded-full">{{$i+1}}</span>
                                    <code>{{$f['_path']}}</code>
                                </div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
