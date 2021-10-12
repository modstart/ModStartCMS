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
@include('module::Partner.View.pc.public.partner',['position'=>'位置'])
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
