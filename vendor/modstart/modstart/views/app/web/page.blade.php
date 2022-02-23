@extends('modstart::layout.frame')

@section('pageTitle'){{!empty($pageTitle)?$pageTitle:''}}@endsection
@section('pageKeywords'){{!empty($pageKeywords)?$pageKeywords:''}}@endsection
@section('pageDescription'){{!empty($pageDescription)?$pageDescription:''}}@endsection

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
