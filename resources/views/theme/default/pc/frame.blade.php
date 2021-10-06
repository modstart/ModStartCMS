@extends('modstart::layout.frame')

@section('pageFavIco'){{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteFavIco'))}}@endsection
@section('pageTitle')@yield('pageTitleMain','') - {{modstart_config('siteName')}}@endsection
@section('pageKeywords'){{modstart_config('siteKeywords')}}@endsection
@section('pageDescription'){{modstart_config('siteDescription')}}@endsection

@section('headAppend')
    @parent
    <link rel="stylesheet" href="{{\ModStart\Core\Assets\AssetsUtil::fix('theme/'.modstart_config('siteTemplate','default').'/css/style.css')}}"/>
    <style type="text/css">
        @if(modstart_config('sitePrimaryColor',null))
            :root{
                --theme-color-primary: {{modstart_config('sitePrimaryColor')}};
                --theme-color-primary-light: {{modstart_config('sitePrimaryColor')}};
                --theme-color-primary-dark: {{modstart_config('sitePrimaryColor')}};
            }
        @endif
    </style>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('PageHeadAppend',$this); !!}
@endsection

@section('bodyAppend')
    @parent
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('PageBodyAppend',$this); !!}
@endsection

@section('body')

    @include('theme.default.pc.share.header')

    @section('bodyContent')
    @show

    @include('theme.default.pc.share.footer')

@endsection
