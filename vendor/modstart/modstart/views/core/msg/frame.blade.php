<!doctype html>
<html class="no-js @yield('pageClass','')">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <meta name="description" content="@yield('pageDescription')">
    <meta name="keywords" content="@yield('pageKeywords')">
    <title>@yield('pageTitle')</title>
    @section('headAppend')@show
</head>
<body>
@section('body')@show
@section('bodyAppend')@show
</body>
</html>