<div class="ub-nav {{$classList}}" {!! $attributes !!}>
    @foreach($navs as $n)
        @if(!empty($n['dialog']))
            <a href="javascript:;"
               class="item {{empty($n['active'])?'':'active'}}"
               data-dialog-request="{!! $n['dialog']['url'] !!}"
               @if(!empty($n['dialog']['width'])) data-dialog-width="{!! $n['dialog']['width'] !!}" @endif
               @if(!empty($n['dialog']['height'])) data-dialog-height="{!! $n['dialog']['height'] !!}" @endif
            >
                {!! $n['title'] !!}
            </a>
        @else
            <a @if(empty($n['active'])) href="{!! $n['url'] !!}" @else href="javascript:;" @endif
               class="item {{empty($n['active'])?'':'active'}}">
                {!! $n['title'] !!}
            </a>
        @endif
    @endforeach
</div>
