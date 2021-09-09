@if(!empty($value))
    @foreach($value as $item)
        <a href="{{\ModStart\Core\Assets\AssetsUtil::fix($item)}}"
            style="display:inline-block;box-sizing:border-box;" data-image-preview>
            <img src="{{\ModStart\Core\Assets\AssetsUtil::fix($item)}}"
                 style="max-height:2rem;max-width:2rem;display:inline-block;box-shadow:0 0 1px #CCC;" />
        </a>
    @endforeach
@endif