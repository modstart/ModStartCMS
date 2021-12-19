@extends('modstart::layout.frame')

@section('pageFavIco'){{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteFavIco'))}}@endsection
@section('pageTitle')@yield('pageTitleMain','') - {{modstart_config('siteName')}}@endsection
@section('pageKeywords'){{modstart_config('siteKeywords')}}@endsection
@section('pageDescription'){{modstart_config('siteDescription')}}@endsection

@section('headAppend')
    @parent
    <link rel="stylesheet" href="{{\ModStart\Core\Assets\AssetsUtil::fix('theme/'.\Illuminate\Support\Facades\Session::get('msSiteTemplateUsing','default').'/css/style.css')}}"/>
    @if(modstart_config('sitePrimaryColor',null))
        <style type="text/css">
            :root{
                --theme-color-primary: {{modstart_config('sitePrimaryColor')}};
                --theme-color-primary-light: {{modstart_config('sitePrimaryColor')}};
                --theme-color-primary-dark: {{modstart_config('sitePrimaryColor')}};
            }
        </style>
    @endif
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
