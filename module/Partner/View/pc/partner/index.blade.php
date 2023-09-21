@extends($_viewFrame)

@section('pageTitleMain'){{modstart_config('Partner_Title','友情链接')}}@endsection
@section('pageKeywords'){{modstart_config('Partner_Title','友情链接')}}@endsection
@section('pageDescription'){{modstart_config('Partner_Title','友情链接')}}@endsection

{!! \ModStart\ModStart::js('asset/common/lazyLoad.js') !!}

@section('bodyContent')

    <div class="ub-content">
        <div class="panel-a"
             style="background-image:var(--color-primary-gradient-bg);">
            <div class="box">
                <h1 class="title">
                    <i class="iconfont icon-users"></i>
                    友情链接
                </h1>
            </div>
        </div>
    </div>

    <div class="ub-container margin-bottom">
        @if(empty($records))
            <div class="ub-empty">
                <div class="icon">
                    <i class="iconfont icon-empty-box"></i>
                </div>
                <div class="text">暂无记录</div>
            </div>
        @else
            <div class="ub-content-box" style="padding-top:1.5rem;">
                <div class="ub-list-items">
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
        @endif
    </div>

@endsection
