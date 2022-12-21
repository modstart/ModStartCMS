@extends('modstart::admin.frame')

@section('pageTitle')内容模板@endsection

@section($_tabSectionName)
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
                        <td>视图使用优先级</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\Module\Cms\Util\CmsTemplateUtil::allListTemplates() as $k=>$fs)
                        <tr>
                            <td><code>{{$k}}</code></td>
                            <td>
                                @foreach($fs as $i=>$f)
                                    <div class="tw-py-2">
                                        @if($i==0)
                                            <span><i class="iconfont icon-checked tw-text-lg ub-text-success"></i></span>
                                            <code>{{$f['_path']}}</code>
                                        @else
                                            <span><i class="iconfont icon-checked tw-text-lg ub-text-muted"></i></span>
                                            <code class="ub-text-muted">{{$f['_path']}}</code>
                                        @endif
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
                    <td>视图使用优先级</td>
                </tr>
                </thead>
                <tbody>
                @foreach(\Module\Cms\Util\CmsTemplateUtil::allDetailTemplates() as $k=>$fs)
                    <tr>
                        <td><code>{{$k}}</code></td>
                        <td>
                            @foreach($fs as $i=>$f)
                                <div class="tw-py-2">
                                    @if($i==0)
                                        <span><i class="iconfont icon-checked tw-text-lg ub-text-success"></i></span>
                                        <code>{{$f['_path']}}</code>
                                    @else
                                        <span><i class="iconfont icon-checked tw-text-lg ub-text-muted"></i></span>
                                        <code class="ub-text-muted">{{$f['_path']}}</code>
                                    @endif
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
            <div class="title">单页模板</div>
        </div>
        <div class="body">
            <table class="ub-table border">
                <thead>
                <tr>
                    <td width="200">模板</td>
                    <td>视图使用优先级</td>
                </tr>
                </thead>
                <tbody>
                @foreach(\Module\Cms\Util\CmsTemplateUtil::allPageTemplates() as $k=>$fs)
                    <tr>
                        <td><code>{{$k}}</code></td>
                        <td>
                            @foreach($fs as $i=>$f)
                                <div class="tw-py-2">
                                    @if($i==0)
                                        <span><i class="iconfont icon-checked tw-text-lg ub-text-success"></i></span>
                                        <code>{{$f['_path']}}</code>
                                    @else
                                        <span><i class="iconfont icon-checked tw-text-lg ub-text-muted"></i></span>
                                        <code class="ub-text-muted">{{$f['_path']}}</code>
                                    @endif
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
            <div class="title">表单模板</div>
        </div>
        <div class="body">
            <table class="ub-table border">
                <thead>
                <tr>
                    <td width="200">模板</td>
                    <td>视图使用优先级</td>
                </tr>
                </thead>
                <tbody>
                @foreach(\Module\Cms\Util\CmsTemplateUtil::allFormTemplates() as $k=>$fs)
                    <tr>
                        <td><code>{{$k}}</code></td>
                        <td>
                            @foreach($fs as $i=>$f)
                                <div class="tw-py-2">
                                    @if($i==0)
                                        <span><i class="iconfont icon-checked tw-text-lg ub-text-success"></i></span>
                                        <code>{{$f['_path']}}</code>
                                    @else
                                        <span><i class="iconfont icon-checked tw-text-lg ub-text-muted"></i></span>
                                        <code class="ub-text-muted">{{$f['_path']}}</code>
                                    @endif
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
