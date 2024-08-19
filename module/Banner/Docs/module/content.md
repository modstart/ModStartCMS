提供多位置轮播图片基础管理功能

## 模块介绍

「通用轮播管理」是一个轮播管理模块，可以管理多组图片、图文组合、视频的轮播。

## 功能特性

支持以下几种图片轮播方式：

- 图片
- 图片+标题+描述+链接
- 视频

## 使用方式

```php
// 循环特定位置轮播
@foreach(\MBanner::all('Xxx') as $banner)
    @if($banner['type']===\Module\Banner\Type\BannerType::IMAGE)
        图片
        <a href="{{ $banner['link'] }}">{{ $banner['image'] }}</a>
    @elseif($banner['type']===\Module\Banner\Type\BannerType::IMAGE_TITLE_SLOGAN_LINK)
        图片+标题+描述+链接
        <a href="{{ $banner['link'] }}">{{ $banner['image'] }}</a>
    @elseif($banner['type']===\Module\Banner\Type\BannerType::VIDEO)
        视频
        <a href="{{ $banner['link'] }}">{{ $banner['video'] }}</a>
    @endif
@endforeach

// 循环特定位置轮播（仅包含图片）
@foreach(\MBanner::allImage('Xxx') as $banner)
  <a href="{{ $banner['link'] }}">{{ $banner['image'] }}</a>
@endforeach
```

### 不同位置轮播

```php
// 位置 Blog
\MBanner::all('Blog')
// 位置 Cms
\MBanner::all('Cms')
```

### 快速渲染

```php
{!! \Module\Banner\View\BannerView::basic('位置') !!}
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


## 模块位置信息注册

通过以下方式可以实现一个轮播位置，其中name为位置（position），title为标题。

```php
class XxxBannerPositionBiz extends \Module\Banner\Biz\AbstractBannerPositionBiz
{
    public function name() {
        return 'Xxx';
    }

    public function title() {
        return '特定位置';
    }
    
    public function remark() {
        return '特定位置的轮播';
    }
}
```

同时在 `XxxBannerPositionBiz` 中注册轮播位置

```php
\Module\Banner\Biz\BannerPositionBiz::register(XxxBannerPositionBiz::class);
```

如此便可在通用轮播管理的页面进行轮播的管理

{ADMIN_MENUS}
