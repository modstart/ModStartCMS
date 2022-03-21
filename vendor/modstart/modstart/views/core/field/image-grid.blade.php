@if($value)
    <a href="{{\ModStart\Core\Assets\AssetsUtil::fix($value)}}" class="ms-field-image-grid" data-image-preview>
        <img src="{{\ModStart\Core\Assets\AssetsUtil::fix($value)}}"/>
    </a>
@endif
