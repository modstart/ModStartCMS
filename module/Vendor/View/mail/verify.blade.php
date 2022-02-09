@extends('module::Vendor.View.mail.frame')

@section('pageTitle','验证码')

@section('bodyContent')
    <p>尊敬的 {{ empty($username) ? '用户' : $username }} 您好：</p>
    <p>&nbsp;</p>
    <p>您的验证码为{{ empty($code) ? '{code}' : $code }}，有效期1小时。</p>
@endsection
