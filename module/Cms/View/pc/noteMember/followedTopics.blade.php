@extends('module::Cms.View.pc.noteMember.frame')

@section('pageTitleMain')关注的专题 -「{{$user['username']}}」的个人中心@endsection

@section('noteMemberContent')
    <div class="ub-nav-tab margin-top">
        <a href="{{modstart_web_url('note_member/'.$user['id'].'/like_notes')}}">
            <i class="iconfont icon-heart-o"></i>
            喜欢的文章
        </a>
        <a href="{{modstart_web_url('note_member/'.$user['id'].'/followed_topics')}}" class="active">
            <i class="iconfont icon-category"></i>
            关注的专题
        </a>
    </div>
    <div class="margin-top">
        @include('module::Cms.View.pc.part.topicsPage',['topics'=>$records])
    </div>
    <div class="ub-page">
        {!! $pageHtml !!}
    </div>
@endsection





