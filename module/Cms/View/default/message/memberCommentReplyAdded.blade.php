<p>
    <a href="{{$__msRoot}}note_member/{{$reply['_memberUser']['id']}}" target="_blank">{{$reply['_memberUser']['username']}}</a>
    在文章
    <a href="{{$__msRoot}}n/{{$note['alias']}}" target="_blank">{{$note['title']}}</a>
    回复了你的评论
</p>
<div class="ub-html">
    {!! $reply['content'] !!}
</div>
