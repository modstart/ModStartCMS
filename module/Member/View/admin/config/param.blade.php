@extends('modstart::admin.dialogFrame')

@section('pageTitle')用户可用变量@endsection

{!! \ModStart\ModStart::js('asset/common/clipboard.js') !!}

@section('bodyContent')

    <div class="tw-bg-white tw-rounded tw-p-4">
        <table class="ub-table border">
            <thead>
            <tr>
                <th width="200">变量</th>
                <th>说明</th>
            </tr>
            </thead>
            <tbody>
            @foreach(\Module\Member\Util\MemberParamUtil::param() as $k=>$v)
                <tr>
                    <td>
                        <a href="javascript:;" data-clipboard-text="{{$k}}">
                            <i class="iconfont icon-copy"></i>
                        </a>
                        <code>{{$k}}</code>
                    </td>
                    <td>{{$v}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
