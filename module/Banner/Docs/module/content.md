提供多位置轮播图片基础管理功能

## 模块介绍

「通用轮播管理」是一个轮播管理模块，可以管理多组图片、图文组合、视频的轮播。

## 功能特性

支持以下几种图片轮播方式：

- 图片
- 图片+标题+描述+链接
- 视频

## 使用方式

在需要使用到的地方通过如下代码调用

```html
{!! \Module\Banner\View\BannerView::simple('位置') !!}
```

## 模块位置信息注册

通过以下方式可以实现一个轮播位置，其中name为位置（position），title为标题。

```php
class XxxBannerPositionProvider extends AbstractBannerPositionProvider
{
    public function name()
    {
        return 'wendaHome';
    }

    public function title()
    {
        return '问答首页';
    }
}
```

同时在 `ModuleServiceProvider` 中注册轮播位置

```php
if (class_exists(BannerPositionProvider::class)) {
    BannerPositionProvider::register(XxxBannerPositionProvider::class);
}
```

如此便可在通用轮播管理的页面进行轮播的管理

## 如何调整轮播的比例和大小

```html
{!! \Module\Banner\View\BannerView::render('位置',['bannerRatio'=>'5-2']) !!}
```

默认情况下，轮播使用了 `5-2` 的比例，还支持的内置比例有，调用时候只需要添加 `宽-高` 的 `bannerRatio` 变量即可。

- `3-2`
- `4-3`
- `2-1`
- `1-1`
- `3-1`
- `4-1`
- `5-1`
- `5-2`
- `10-1`

如果需要其他尺寸，可自行在 `module/Banner/View/pc/public/banner.blade.php` 模板文件中调整。


{ADMIN_MENUS}