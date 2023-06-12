<?php


namespace Module\Vendor\QuickRun\Verify\Field;


use ModStart\Core\Util\IdUtil;
use ModStart\Layout\LayoutHtml;

/**
 * 用户图片字段
 *
 * Class MemberImage
 * @package Module\Member\Widget\Field
 */
class LayoutHtmlVerifyFoot extends LayoutHtml
{
    protected $isLayoutField = true;

    public function __construct()
    {
        parent::__construct(null);
    }

    public function render()
    {
        $id = IdUtil::generate('LayoutHtmlVerifyFoot');
        $this->label = <<<HTML_FOOT
<script type="text/html" id="{$id}Content">
    <button type="button" class="btn btn-primary" data-verify-pass>审核通过</button>
    <button type="button" class="btn btn-danger" data-verify-reject>审核拒绝</button>
    <input class="form" placeholder="输入拒绝理由" data-verify-rejct-remark />
</script>
<script>
$(function(){
    $(".panel-dialog-foot").css('text-align','left').html($('#{$id}Content').html());
    var search = null;
    try {
        search = window.parent.__grids.get(0).lister.getParam().search;
    } catch(e) { }
    var request = function(data){
        MS.dialog.loadingOn();
        var formData = $('form').serializeJson();
        MS.api.post(window.location.href,Object.assign(data,{search:search},formData), function(res){
            MS.dialog.loadingOff();
            MS.api.defaultCallback(res);
        });
    };
    $(document).on('click','[data-verify-pass]',function(){
        request({verify:true});
    });
    $(document).on('click','[data-verify-reject]',function(){
        request({verify:false,remark:$('[data-verify-rejct-remark]').val()});
    });
});
</script>
HTML_FOOT;
        return parent::render();
    }


}
