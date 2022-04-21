<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <title>[{{modstart_config('siteName')}}] @yield('pageTitle')</title>
    <style type="text/css">
        *{padding:0;margin:0;font-family:"Segoe UI","Lucida Grande",Helvetica,Arial,"Microsoft YaHei",FreeSans,Arimo,"Droid Sans","wenquanyi micro hei","Hiragino Sans GB","Hiragino Sans GB W3",sans-serif;color:#666;box-sizing:border-box;}
        body{font-size:13px;background:#F8F8F8;}
        #wrap{margin:0 auto;max-width:800px;padding:0;border-radius:10px;border:1px solid #EEE;}
        #head,#content,#foot{background:#FFF;}
        #head{background:{{modstart_config('sitePrimaryColor','#2f4cb9')}};padding:10px 20px;text-align:center;border-radius:10px 10px 0 0;}
        #head .logo{line-height:40px;color:#FFF;font-size:20px;text-decoration:none;}
        #content{padding:30px 10px;}
        #content p{line-height:2em;}
        #signature{padding:10px;color:#999;}
        #foot{text-align:center;line-height:50px;color:#999;padding:10px 20px;border-radius:0 0 10px 10px;}
        .ub-email-table {border-collapse: collapse;width:100%;}
        .ub-email-table td{border: 1px solid #ddd;padding:5px;}
        .ub-email-table tr:nth-child(even){background-color: #f2f2f2;}
    </style>
</head>
<body>
<div id="wrap">
    <div id="head">
        <a class="logo" href="http://{{modstart_config('siteDomain')}}" target="_blank">
            {{modstart_config('siteName')}}
        </a>
    </div>
    @section('body')
        <div id="content">
            @section('bodyContent')
            @show
        </div>
        <div id="foot">
            {{modstart_config('siteName')}} &copy; {{modstart_config('siteDomain')}}
        </div>
    @show
</div>
</body>
</html>
