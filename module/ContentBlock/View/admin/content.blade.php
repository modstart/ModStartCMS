<div class="tw-shadow tw-rounded tw-p-1" style="width:10rem;overflow:auto;">
    <table class="ub-table bord er mini tw-table-fixed">
        <tbody>
        @if($item->type===\Module\ContentBlock\Type\ContentBlockType::BASIC)
            @if(!empty($item->texts))
                <tr>
                    <td width="40">文字</td>
                    <td>
                        @foreach($item->texts as $text)
                            <div class="tw-truncate">
                                <i class="iconfont icon-dot-sm"></i>
                                {{$text}}
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endif
            @if(!empty($item->images))
                <tr>
                    <td width="40">图片</td>
                    <td>
                        @foreach($item->images as $image)
                            <a href="javascript:;"
                               data-image-preview="{{\ModStart\Core\Assets\AssetsUtil::fix($image)}}">
                                <img style="height:1.5rem;"
                                     src="{{\ModStart\Core\Assets\AssetsUtil::fix($image)}}"/>
                            </a>
                        @endforeach
                    </td>
                </tr>
            @endif
        @elseif($item->type===\Module\ContentBlock\Type\ContentBlockType::IMAGE)
            <tr>
                <td width="40">图片</td>
                <td>
                    <a href="javascript:;" data-image-preview="{{\ModStart\Core\Assets\AssetsUtil::fix($item->image)}}">
                        <img style="max-width:2rem;max-height:2rem;"
                             src="{{\ModStart\Core\Assets\AssetsUtil::fix($item->image)}}"/>
                    </a>
                </td>
            </tr>
        @elseif($item->type===\Module\ContentBlock\Type\ContentBlockType::HTML)
            <tr>
                <td width="40">内容</td>
                <td>
                    <div class="ub-html">
                        {!! $item->content !!}
                    </div>
                </td>
            </tr>
        @endif
        </tbody>
    </table>

</div>
