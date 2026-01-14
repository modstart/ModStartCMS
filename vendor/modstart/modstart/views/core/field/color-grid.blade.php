@if(!empty($value))
    <?php $isDark = \ModStart\Core\Util\ColorUtil::isDark($value); ?>
    <div class="ub-tag" style="background-color:{{$value}};color:{{ $isDark ? '#fff' : '#000' }};">
        {{$value}}
    </div>
@endif
