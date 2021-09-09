@extends('modstart::layout.frame')

@section('pageTitle',isset($pageTitle)?htmlspecialchars($pageTitle):'')
@section('pageKeywords',isset($pageKeywords)?htmlspecialchars($pageKeywords):'')
@section('pageDescription',isset($pageDescription)?htmlspecialchars($pageDescription):'')

@section('body')
    {!! $content !!}
@endsection
