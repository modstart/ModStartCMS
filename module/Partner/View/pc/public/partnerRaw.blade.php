<?php
$partners = \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position);
\ModStart\ModStart::js('asset/common/lazyLoad.js');
?>
<div class="ub-list-items padding-bottom-remove">
    <div class="row">
        <?php $linkDisable = modstart_config('Partner_LinkDisable',false); ?>
        @foreach($partners as $partner)
            <div class="col-md-2 col-4">
                <div class="item-n">
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
                                <span class="cover ub-cover-3-1">
                                <span class="content">{{$partner['title']}}</span>
                                </span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
