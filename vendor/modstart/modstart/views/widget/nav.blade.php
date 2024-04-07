<div class="ub-nav {{$classList}}">
    @foreach($navs as $n)
        <a href="{!! $n['url'] !!}" class="item {{empty($n['active'])?'':'active'}}">
            {!! $n['title'] !!}
        </a>
    @endforeach
</div>
