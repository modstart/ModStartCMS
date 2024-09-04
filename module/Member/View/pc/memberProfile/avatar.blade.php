@extends($_viewMemberFrame)

@section('pageTitleMain')修改头像@endsection
@section('pageKeywords')修改头像@endsection
@section('pageDescription')修改头像@endsection

{!! \ModStart\ModStart::style('.pb-page-hidden{overflow:hidden;height:0;width:0;margin-bottom:0!important;}.cropper-bg,.cropper-crop{border-radius:0.5rem;}') !!}

@section('bodyAppend')
    @parent
    {{\ModStart\ModStart::js('asset/vendor/cropper/cropper.js')}}
    {{\ModStart\ModStart::css('asset/vendor/cropper/cropper.css')}}
    <script>
        layui.use('upload', function () {
            var upload = layui.upload;
            var $box = {
                avatar: $('[data-box-avatar]'),
                tool: $('[data-box-tool]'),
                editingPreview: $('[data-box-editing-preview]'),
                editing: $('[data-box-editing]'),
                show:function($box){
                    $box.removeClass('pb-page-hidden');
                },
                hide:function($box){
                    $box.addClass('pb-page-hidden');
                }
            };
            var cropperOptions = {
                aspectRatio: 1 / 1,
                crop: function (e) {
                }
            };
            var $cropper = $('#editingPreview').cropper(cropperOptions);
            var fileChoose = function (obj) {
                obj.preview(function (index, file, result) {
                    $box.show($box.editingPreview);
                    $box.show($box.editing);
                    $box.hide($box.avatar);
                    $box.hide($box.tool);
                    $cropper.cropper('destroy').attr('src', result).cropper(cropperOptions);
                });
            };
            upload.render({
                elem: '#avatarImageUpload,#avatarImageReupload',
                auto: false,
                choose: fileChoose
            });
            $('#avatarZoomOut').on('click', function () {
                $cropper.cropper('zoom', -0.1);
            });
            $('#avatarZoomIn').on('click', function () {
                $cropper.cropper('zoom', 0.1);
            });
            $('#avatarBack').on('click', function () {
                $box.show($box.avatar);
                $box.show($box.tool);
                $box.hide($box.editingPreview);
                $box.hide($box.editing);
            });
            $('#avatarSave').on('click', function () {
                var cropper = $cropper.data('cropper');
                var imageData = cropper.getCroppedCanvas({width:480,height:480,imageSmoothingQuality:'high'}).toDataURL('image/png');
                MS.dialog.loadingOn();
                $.post('?', {type: 'cropper', avatar: imageData}, function (res) {
                    MS.dialog.loadingOff();
                    MS.api.defaultCallback(res);
                    $('[data-member-image-upload-preview]').attr('src',imageData);
                });
            });
        });
    </script>
@endsection

@section('memberBodyContent')

    @include('module::Member.View.pc.memberProfile.profileNav')

    <div class="tw-px-10 tw-py-20 tw-rounded-b-lg tw-bg-white">
        <div data-box-avatar class="margin-bottom">
            <div class="tw-p-2 tw-rounded-lg tw-bg-gray-100 tw-inline-block">
                <?php $avatar = 'asset/image/avatar.svg'; ?>
                @if(!empty($_memberUser['avatarBig']))
                    <?php $avatar = $_memberUser['avatarBig']; ?>
                @endif
                <img data-member-image-upload-preview="avatar"
                     class="tw-h-48 tw-rounded-lg"
                     src="{{\ModStart\Core\Assets\AssetsUtil::fix($avatar)}}"/>
            </div>
        </div>
        <div data-box-tool class="margin-bottom">
            <button type="button" class="btn btn-round" id="avatarImageUpload">
                <i class="iconfont icon-upload"></i>
                选择图片
            </button>
        </div>
        <div data-box-editing-preview class="margin-bottom pb-page-hidden">
            <img id="editingPreview" style="max-width:80%;max-height:300px;"/>
        </div>
        <div data-box-editing class="margin-bottom pb-page-hidden">
            <a href="javascript:;" class="btn btn-round" id="avatarBack">
                <i class="iconfont icon-direction-left"></i>
                返回
            </a>
            <a href="javascript:;" class="btn btn-round" id="avatarImageReupload">
                <i class="iconfont icon-upload"></i>
                重新选择
            </a>
            <a href="javascript:;" class="btn btn-round" id="avatarZoomOut">
                <i class="iconfont icon-zoom-out"></i>
            </a>
            <a href="javascript:;" class="btn btn-round" id="avatarZoomIn">
                <i class="iconfont icon-zoom-in"></i>
            </a>
            <a href="javascript:;" class="btn btn-round btn-primary" id="avatarSave">
                <i class="iconfont icon-save"></i> 保存头像
            </a>
        </div>
    </div>
@endsection
