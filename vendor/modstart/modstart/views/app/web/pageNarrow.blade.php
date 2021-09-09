@extends('modstart::layout.frame')

@section('pageTitle',isset($pageTitle)?htmlspecialchars($pageTitle):'')
@section('pageKeywords',isset($pageKeywords)?htmlspecialchars($pageKeywords):'')
@section('pageDescription',isset($pageDescription)?htmlspecialchars($pageDescription):'')

@section('body')
    <div class="ub-header-mobile shadow">
        <div class="header-status"></div>
        <div class="header-container">
            <div class="body has-back">
                <span class="back iconfont" style="cursor:pointer;" onclick="window.history.back();">&#xe60b;</span>
                <span class="title">{{$pageTitle}}</span>
            </div>
        </div>
        <div class="header-container-placeholder"></div>
    </div>
    <div class="ub-mobile-container tw-py-2" style="min-height:calc( 100vh - 2.5rem );">
        {!! $content !!}
    </div>
@endsection
