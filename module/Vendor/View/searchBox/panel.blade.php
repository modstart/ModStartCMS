<?php $searchBoxProviders = \Module\Vendor\Provider\SearchBox\SearchBoxProvider::all(); ?>
@if(count($searchBoxProviders)>0)
    <div class="ub-content-box margin-bottom search-panel">
        <div class="tw-p-2">
            @if(count($searchBoxProviders)>1)
                <div class="tw-flex tw-border-b tw-border-gray-200">
                    @foreach($searchBoxProviders as $index => $provider)
                        <div class="search-tab-item tw-cursor-pointer tw-font-bold tw-px-3 tw-py-1 tw-rounded-t-lg  {{$index===0?'tw-text-white ub-bg-primary':''}}"
                             onclick="$('.search-tab-item').removeClass('tw-text-white ub-bg-primary');$(this).addClass('tw-text-white ub-bg-primary');$(this).closest('.search-panel').find('form').attr('action','{{$provider->url()}}');$(this).closest('.search-panel').find('input[name=\'keywords\']').attr('placeholder','{{$provider->placeholder()}}')">
                            {{$provider->title()}}
                        </div>
                    @endforeach
                </div>
            @endif
            <form action="{{$searchBoxProviders[0]->url()}}" method="get" class="tw-relative">
                <div class="tw-flex">
                    <input type="text" name="keywords"
                           class="form tw-flex-grow" value="{{ empty($keywords) ? '' : $keywords }}" placeholder="{{$searchBoxProviders[0]->placeholder()}}">
                    <button class="btn btn-primary"
                            type="submit">
                        <i class="iconfont icon-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
