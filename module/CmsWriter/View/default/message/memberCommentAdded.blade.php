<p>
    <a href="{{$__msRoot}}note_member/{{$comment['_memberUser']['id']}}" target="_blank">{{$comment['_memberUser']['username']}}</a>
    评论了你的文章
    <a href="{{$__msRoot}}n/{{$note['alias']}}" target="_blank">{{$note['title']}}</a>
</p>
<div class="ub-html">
    {!! $comment['content'] !!}
</div>
