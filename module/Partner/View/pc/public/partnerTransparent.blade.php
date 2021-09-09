<?php $partners = \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position); ?>
{!! ModStart::style('.pb-partner-item{filter:grayscale(100%);opacity:0.5;}.pb-partner-item:hover{filter:grayscale(0%);opacity:1;}') !!}
<div class="ub-content">
    <div class="head">
        <div class="title">友情链接</div>
        <div class="sub-title">Partners</div>
    </div>
    <div class="body">
        <div class="ub-list-items ub-text-center">
            @foreach($partners as $partner)
                <div class="item-n pb-partner-item" style="width:9rem;height:3rem;display:inline-block;vertical-align:top;margin:0.25rem;">
                    @if(!empty($partner['logo']))
                        <a class="image" href="{{$partner['link']}}" target="_blank">
                            <div class="cover contain ub-cover-3-1" style="background-image: url({{\ModStart\Core\Assets\AssetsUtil::fix($partner['logo'])}});"></div>
                        </a>
                    @else
                        <a class="text" href="{{$partner['link']}}" target="_blank">
                            <div class="cover ub-cover-3-1">
                                <span class="content">{{$partner['title']}}</span>
                            </div>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
