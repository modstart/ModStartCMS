@extends('module::Vendor.View.mail.frame')

@section('pageTitle','验证码')

@section('bodyContent')
    <p>尊敬的 {{$username or '用户'}} 您好：</p>
    <p>&nbsp;</p>
    <p>您的验证码为{{$code or '{code}'}}，有效期1小时。</p>
@endsection
