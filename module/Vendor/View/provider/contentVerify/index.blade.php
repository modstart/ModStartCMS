@extends('modstart::layout.frame')

@section('pageTitle',isset($pageTitle)?htmlspecialchars($pageTitle):'')
@section('pageKeywords',isset($pageKeywords)?htmlspecialchars($pageKeywords):'')
@section('pageDescription',isset($pageDescription)?htmlspecialchars($pageDescription):'')

@section('headAppend')
    @parent
    <style type="text/css">
        *{padding:0;margin:0;font-family:"Segoe UI","Lucida Grande",Helvetica,Arial,"Microsoft YaHei",FreeSans,Arimo,"Droid Sans","wenquanyi micro hei","Hiragino Sans GB","Hiragino Sans GB W3",sans-serif;color:#666;box-sizing:border-box;}
        body{font-size:13px;background:#F8F8F8;}
        #wrap{margin:0 auto;max-width:800px;padding:0;border:1px solid #EEE;}
        #head,#content,#foot{background:#FFF;}
        #head{background:{{modstart_config('sitePrimaryColor','#333')}};height:50px;padding:5px 10px;}
        #head .logo{line-height:40px;color:#FFF;font-size:20px;text-decoration:none;}
        #content{padding:30px 10px;min-height:calc(100vh - 60px);}
        #content p{line-height:2em;}
        #signature{padding:10px;color:#999;}
        #foot{text-align:center;line-height:50px;border-top:1px solid #EEE;color:#999;}
        .ub-email-table {border-collapse: collapse;width:100%;}
        .ub-email-table td{border: 1px solid #ddd;padding:5px;}
        .ub-email-table tr:nth-child(even){background-color: #f2f2f2;}
    </style>
@endsection

@section('body')
    <div id="wrap">
        <div id="head">
            <a class="logo" href="http://{{modstart_config('siteDomain')}}" target="_blank">
                {{$pageTitle}}
            </a>
        </div>
        <div id="content">
            {!! $content !!}
        </div>
    </div>
@endsection

