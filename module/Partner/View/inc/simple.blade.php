<div class="ub-panel">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-users"></i>
            {!! modstart_config('Partner_Title','合作伙伴') !!}
        </div>
    </div>
    <div class="body ub-list-items padding-bottom-remove">
        <div class="row">
            @foreach($records as $r)
                <div class="col-md-2 col-4">
                    <div class="item-n">
                        @if(!empty($linkDisable))
                            @if(!empty($r['logo']))
                                <div class="image">
                                    <div class="cover contain ub-cover-3-1" data-src="{{$r['logo']}}"></div>
                                </div>
                            @else
                                <div class="text">
                                    <div class="cover ub-cover-3-1">
                                        <span class="content">{{$r['title']}}</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            @if(!empty($r['logo']))
                                <a class="image" href="{{$r['link']}}" target="_blank">
                                    <div class="cover contain ub-cover-3-1" data-src="{{$r['logo']}}"></div>
                                </a>
                            @else
                                <a class="text" href="{{$r['link']}}" target="_blank">
                                    <div class="cover ub-cover-3-1">
                                        <span class="content">{{$r['title']}}</span>
                                    </div>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
