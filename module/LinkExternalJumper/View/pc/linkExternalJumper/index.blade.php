@extends('modstart::layout.frame')

@section('headAppend')
    @parent
    <style type="text/css">
        .pb-link-external-jumper{display:flex;position:fixed;top:0;left:0;right:0;bottom:20%;align-items:center;}
    </style>
@endsection

@section('body')
    <div class="pb-link-external-jumper">
        <div style="margin:0 auto;display:block;max-width:800px;" class="lg:tw-w-1/2 tw-w-full tw-px-4">
            <div>
                <a href="{{modstart_web_url('')}}">
                    <img style="height:60px;" src="{{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteLogo'))}}"/>
                </a>
            </div>
            <div class="tw-bg-white tw-rounded-2xl tw-shadow-lg tw-p-8">
                <h2>您即将离开{{modstart_config('siteName')}}，请注意您的帐号和财产安全</h2>
                <div class="tw-py-4">
                    <a class="tw-text-gray-400" href="{{$target}}">{{$target}}</a>
                </div>
                <div class="tw-text-right tw-pt-4 tw-border-0 tw-border-t tw-border-gray-200 tw-border-solid">
                    <a class="btn btn-primary btn-lg" href="{{$target}}">继续访问</a>
                </div>
            </div>
        </div>
    </div>
@endsection


