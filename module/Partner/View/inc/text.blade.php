<?php
$records = \Module\Partner\Util\PartnerUtil::listByPositionWithCache($position);
\ModStart\ModStart::js('asset/common/lazyLoad.js');
?>
<div class="ub-panel">
    <div class="head">
        <div class="title">
            <i class="iconfont icon-users"></i>
            {!! modstart_config('Partner_Title','合作伙伴') !!}
        </div>
    </div>
    <div class="body">
            <?php $linkDisable = modstart_config('Partner_LinkDisable',false); ?>
            @foreach($records as $r)
                @if($linkDisable)
                    <div class="ub-text-default">{{$r['title']}}</div>
                @else
                    <a href="{{$r['link']}}" class="ub-text-default"
                       target="_blank">{{$r['title']}}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
