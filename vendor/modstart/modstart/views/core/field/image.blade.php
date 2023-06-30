@extends('modstart::core.field.file-common')

@section('fieldFilePreviewItem')
    <div class="ub-file-selector__item image has-value" data-file-item>
        <div class="tools">
            <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>
            <a href="javascript:;" class="action preview" data-image-preview><i class="iconfont icon-eye"></i></a>
        </div>
        <div class="cover ub-cover-1-1" data-value-background></div>
    </div>
@overwrite
