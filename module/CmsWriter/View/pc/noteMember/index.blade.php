@extends('module::CmsWriter.View.pc.noteMember.frame')

@section('pageTitleMain')「{{$user['username']}}」的个人中心@endsection

@section('noteMemberContent')
    <div class="ub-nav-tab margin-top">
        <a href="{{modstart_web_url('note_member/'.$user['id'])}}" class="active">
            <i class="iconfont icon-list-alt"></i>
            文章列表
        </a>
    </div>
    <div class="margin-top">
        @include('module::CmsWriter.View.pc.part.notes',['notes'=>$notes])
    </div>
    <div class="ub-page">
        {!! $pageHtml !!}
    </div>
@endsection





