@extends('modstart::layout.frame')

@section('pageTitle',isset($pageTitle)?htmlspecialchars($pageTitle):'')
@section('pageKeywords',isset($pageKeywords)?htmlspecialchars($pageKeywords):'')
@section('pageDescription',isset($pageDescription)?htmlspecialchars($pageDescription):'')

@section('body')
    <div class="ub-container">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    {{$pageTitle}}
                </div>
            </div>
            <div class="body">
                {!! $content !!}
            </div>
        </div>
    </div>
@endsection