<?php
$partners = \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position);
\ModStart\ModStart::js('asset/common/lazyLoad.js');
?>
<div class="ub-content margin-top">
    <div class="head">
        <div class="title" data-scroll-animate="animated fadeInUp">{{modstart_config('Partner_Title','我们的合作伙伴')}}</div>
    </div>
    <div class="body ub-list-items padding-bottom-remove">
        <div class="row">
            <?php $linkDisable = modstart_config('Partner_LinkDisable',false); ?>
            @foreach($partners as $partner)
                <div class="col-md-2 col-4">
                    <div class="item-n" data-scroll-animate="animated fadeInUp">
                        @if($linkDisable)
                            @if(!empty($partner['logo']))
                                <div class="image">
                                    <div class="cover contain ub-cover-3-1" data-src="{{$partner['logo']}}"></div>
                                </div>
                            @else
                                <div class="text">
                                    <div class="cover ub-cover-3-1">
                                        <span class="content">{{$partner['title']}}</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            @if(!empty($partner['logo']))
                                <a class="image" href="{{$partner['link']}}" target="_blank">
                                    <div class="cover contain ub-cover-3-1" data-src="{{$partner['logo']}}"></div>
                                </a>
                            @else
                                <a class="text" href="{{$partner['link']}}" target="_blank">
                                    <div class="cover ub-cover-3-1">
                                        <span class="content">{{$partner['title']}}</span>
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
