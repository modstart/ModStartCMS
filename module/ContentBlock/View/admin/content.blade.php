<div class="tw-shadow tw-rounded tw-p-1" style="width:10rem;">
    <table class="ub-table border mini">
        <tbody>
        @if($item->type===\Module\ContentBlock\Type\ContentBlockType::IMAGE)
            <tr>
                <td width="50">图片</td>
                <td>
                    <a href="javascript:;" data-image-preview="{{\ModStart\Core\Assets\AssetsUtil::fix($item->image)}}">
                        <img style="max-width:2rem;max-height:2rem;" src="{{\ModStart\Core\Assets\AssetsUtil::fix($item->image)}}" />
                    </a>
                </td>
            </tr>
            <tr>
                <td>标题</td>
                <td>{{$item->title}}</td>
            </tr>
            <tr>
                <td>链接</td>
                <td>{{$item->link}}</td>
            </tr>
        @elseif($item->type===\Module\ContentBlock\Type\ContentBlockType::HTML)
            <tr>
                <td width="50">内容</td>
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
