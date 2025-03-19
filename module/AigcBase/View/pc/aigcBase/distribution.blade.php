@extends('module::AigcBase.View.pc.aigcBase.frame')

@section('pageTitleMain')分享推广@endsection
@section('pageKeywords')分享推广@endsection
@section('pageDescription')分享推广@endsection

@section('aigcBody')
    <div class="tw-w-full tw-p-3 tw-overflow-auto">

        {!! \Module\MemberDistribution\View\MemberDistributionView::dashboard() !!}

    </div>
@endsection
