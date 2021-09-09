<?php $partners = \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position); ?>
{!! ModStart::style('.pb-partner-item{filter:grayscale(100%);opacity:0.5;}.pb-partner-item:hover{filter:grayscale(0%);opacity:1;}') !!}
<div class="ub-panel ub-list">
    <div class="head">
        <div class="title">
            合作伙伴
        </div>
    </div>
    <div class="body">
        <div class="row">
            @foreach($partners as $partner)
                <div class="col-md-2 col-6">
                    <div class="item-n pb-partner-item">
                        @if(!empty($partner['logo']))
                            <a class="image" href="{{$partner['link']}}" target="_blank">
                                <div class="cover contain ub-cover-3-1" style="background-image: url({{\ModStart\Core\Assets\AssetsUtil::fix($partner['logo'])}});"></div>
                            </a>
                        @else
                            <a class="text" href="{{$partner['link']}}" target="_blank">
                            <span class="cover ub-cover-3-1">
                                <span class="content">{{$partner['title']}}</span>
                            </span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
