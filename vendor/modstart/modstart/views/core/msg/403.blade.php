@extends('modstart::core.msg.frame')

@section('pageTitle',L('No Permission'))

@section('headAppend')
    <link rel="stylesheet" href="//at.alicdn.com/t/font_1133924_1o5fgl50876.css" />
    <style type="text/css">
        body, html {background: #EEE;font:14px/20px normal Helvetica, Arial, "微软雅黑", sans-serif;color:#999;padding:0;margin:0;}
        .icon{text-align:center;color:#CCC;font-size:100px;line-height:100px;padding:40px 0 0 0;}
        .icon i{font-size:100px;}
        h1{color:#CCC;font-size:50px;margin:0;text-align:center;line-height:40px;margin:20 0 0 0;padding:0;}
        .content{padding-top:2em;text-align:center;}
        .suggest{padding-top:2em;text-align:center;line-height:20px;}
        .suggest a{background:#3385ff;color:#FFF;border-radius:12px;height:24px;display:inline-block;padding:0 12px;line-height:24px;text-decoration:none;margin:0 12px;}
    </style>
@endsection

@section('body')
    <div class="icon">
        <i class="iconfont icon404"></i>
    </div>
    <h1>
        403
    </h1>
    <div class="content">
        {{ L('No Permission') }}
    </div>
    <div class="suggest">
        <p><a href="{{modstart_web_url()}}">{{ L('Visit Home')  }}</a></p>
    </div>
@endsection
