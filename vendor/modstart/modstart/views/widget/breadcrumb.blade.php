<div class="ub-breadcrumb">
    @foreach($items as $iIndex=>$i)
        @if($iIndex===count($items)-1)
            <span class="active">{{$i['title']}}</span>
        @else
            <a href="{{$i['url']}}">{{$i['title']}}</a>
        @endif
    @endforeach
</div>
