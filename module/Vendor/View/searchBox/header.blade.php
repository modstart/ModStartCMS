<?php $searchBoxProviders = \Module\Vendor\Provider\SearchBox\SearchBoxProvider::all(); ?>
@if(count($searchBoxProviders)>0)
    <div class="search @if(count($searchBoxProviders)>1) has-drop @endif">
        <div class="box">
            <form action="{{$searchBoxProviders[0]->url()}}" method="get">
                <input type="text" name="keywords" value="{{ empty($keywords) ? '' : $keywords }}" placeholder="搜索内容"/>
                <button type="submit"><i class="iconfont icon-search"></i></button>
            </form>
            @if(count($searchBoxProviders)>1)
                <div class="search-select">
                    <div class="search-select-box">
                        <span class="text">{{$searchBoxProviders[0]->title()}}</span>
                        <i class="iconfont icon-angle-down"></i>
                    </div>
                    <div class="search-select-drop">
                        @foreach($searchBoxProviders as $provider)
                            <a class="search-select-item" href="javascript:;" data-search-url="{{$provider->url()}}">
                                {{$provider->title()}}
                            </a>
                        @endforeach
                    </div>
                </div>
                <script>
                    $(function(){
                        var $search = $('header .search.has-drop .box');
                        $search.on('click','.search-select-item',function(){
                            $search.find('form').attr('action',$(this).attr('data-search-url'));
                            $search.find('.search-select-box .text').html($(this).html());
                            $search.find('.search-select').removeClass('show');
                            return false;
                        });
                        $search.find('.search-select-box').on('click',function(){
                            $search.find('.search-select').addClass('show');
                        });
                    });
                </script>
            @endif
        </div>
    </div>
@endif
