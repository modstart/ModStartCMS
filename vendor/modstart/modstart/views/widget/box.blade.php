<div class="ub-panel {{$classList}}" {!! $attributes !!}>
    @if(empty($title) && empty($tools))
        <div class="head"></div>
    @else
        <div class="head">
            <div class="more">
                @foreach($tools as $tool)
                    {!! $tool !!}
                @endforeach
            </div>
            <div class="title">
                @if(is_array($title))
                    @foreach($title as $i=>$item)
                        @if(count($item)>1)
                            <a href="{{$item[1]}}">{{$item[0]}}</a>
                        @else
                            <span>{{$item[0]}}</span>
                        @endif
                        @if($i+1<count($title))
                            <i class="iconfont icon-angle-right ub-text-muted"></i>
                        @endif
                    @endforeach
                @else
                    {!! empty($title)?' ':$title !!}
                @endif
            </div>
        </div>
    @endif
    <div class="body">
        {!! $content !!}
    </div>
</div>
