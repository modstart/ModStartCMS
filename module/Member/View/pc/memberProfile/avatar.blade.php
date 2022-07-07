@extends($_viewMemberFrame)

@section('pageTitleMain')修改头像@endsection
@section('pageKeywords')修改头像@endsection
@section('pageDescription')修改头像@endsection

@section('bodyAppend')
    @parent
    {{\ModStart\ModStart::js('asset/vendor/cropper/cropper.js')}}
    {{\ModStart\ModStart::css('asset/vendor/cropper/cropper.css')}}
    <script>
        layui.use('upload', function () {
            var cropperOptions = {
                aspectRatio: 1 / 1,
                crop: function (e) {
                }
            };
            var upload = layui.upload;
            var uploadInst = upload.render({
                elem: '#avatarImage',
                auto: false,
                choose: function (obj) {
                    if (!$cropper.data('cropper')) {
                        return;
                    }
                    obj.preview(function (index, file, result) {
                        $cropper.cropper('destroy').attr('src', result).cropper(cropperOptions);
                    });
                }
            });
            var $cropper = $('#image').cropper(cropperOptions);
            $('#avatarZoomOut').on('click', function () {
                $cropper.cropper('zoom', -0.1);
            });
            $('#avatarZoomIn').on('click', function () {
                $cropper.cropper('zoom', 0.1);
            });
            $('#avatarSave').on('click', function () {
                var image = $cropper.cropper('getCroppedCanvas').toDataURL('image/png');
                window.api.dialog.loadingOn();
                $.post('?', {type: 'cropper', avatar: image}, function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res);
                });
            });
        });
    </script>
@endsection

@section('memberBodyContent')

    @include('module::Member.View.pc.memberProfile.profileNav')

    <div class="ub-panel">
        <div class="head">
            <div class="title">{{$pageTitle}}</div>
        </div>
        <div class="body">
            <div style="padding:0.5rem;">
                <div>
                    <?php $avatar = 'asset/image/avatar.png'; ?>
                    @if(!empty($_memberUser['avatarBig']))
                        <?php $avatar = $_memberUser['avatarBig']; ?>
                    @endif
                    <img data-member-image-upload-preview="avatar"
                         style="height:200px;width:200px;border:1px solid #CCC;vertical-align:bottom;"
                         src="{{\ModStart\Core\Assets\AssetsUtil::fix($avatar)}}"/>
                </div>

                <div style="margin-top:10px;">
                    <div>
                        <a href="javascript:;" id="avatarZoomOut" class="btn btn-default"><i
                                    class="iconfont icon-zoom-out"></i></a>
                        <a href="javascript:;" id="avatarZoomIn" class="btn btn-default"><i
                                    class="iconfont icon-zoom-in"></i></a>
                        <button type="button" class="btn" id="avatarImage">
                            上传图片
                        </button>
                        <a href="javascript:;" id="avatarSave" class="btn btn-primary"><i class="uk-icon-save"></i> 保存头像</a>
                    </div>
                    <div style="margin-top:10px;">
                        <img id="image" style="max-width:80%;max-height:300px;"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
