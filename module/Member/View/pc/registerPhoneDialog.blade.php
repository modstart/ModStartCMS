@extends($_viewFrame)

@section('pageTitleMain')注册@endsection
@section('pageKeywords')注册@endsection
@section('pageDescription')注册@endsection

@section('headAppend')
    @parent
    <link rel="canonical" href="{{modstart_web_url('register/phone')}}"/>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('MemberRegisterPageHeadAppend'); !!}
@endsection

@section('bodyAppend')
    @include('module::Member.View.pc.inc.registerPhoneScript')
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('MemberRegisterPageBodyAppend'); !!}
@endsection

{!! \ModStart\ModStart::style('html,body{background:var(--color-content-bg);}') !!}
@section('body')
    @include('module::Member.View.pc.inc.registerPhonePanel')
@endsection
