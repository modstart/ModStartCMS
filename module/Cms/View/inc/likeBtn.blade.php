{!! \ModStart\ModStart::js('vendor/Vendor/asset/toggle.js') !!}
{!! \ModStart\ModStart::css('vendor/Vendor/asset/toggle.css') !!}
<div class="tw-inline-block"
     data-toggle-group="{{modstart_api_url('cms/operate/like')}}"
     data-status="{{ \Module\Cms\Util\CmsOperateUtil::isLiked($id)? 'is_toggle' : 'not_toggle' }}"
     data-id="{{$id}}">
    <a href="javascript:;" class="btn btn-round" data-action="toggle">
        <i class="iconfont icon-heart-alt"></i>
        喜欢
        @if(isset($param['count']))
            (<span data-like-count>{{intval($param['count'])}}</span>)
        @endif
    </a>
    <a href="javascript:;" class="btn btn-round btn-primary" data-action="untoggle">
        <i class="iconfont icon-heart-alt"></i>
        已喜欢
        @if(isset($param['count']))
            (<span data-like-count>{{intval($param['count'])}}</span>)
        @endif
    </a>
</div>
