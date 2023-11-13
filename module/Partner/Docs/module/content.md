## 模块介绍

「友情链接管理」提供了一个基于位置的友情链接管理模块

```mind
功能特性
    多位置配置
    独立页面友情链接展示
    支持文字和图片
    一行代码在系统中引入
```

## 快速调用

**常规版本**

```
{!! \Module\Partner\View\PartnerView::simple('位置') !!}
```

**透明版本**

```
{!! \Module\Partner\View\PartnerView::transparent('位置') !!}
```

**文字版**

```
{!! \Module\Partner\View\PartnerView::text('位置') !!}
```

## 循环显示友情链接

```php
// 循环特定位置友情链接
@foreach(\MPartner::all('Xxx') as $partner)
  <a href="{{ $partner['link'] }}">{{ $partner['title'] }}</a>
@endforeach
```

## 不同位置友情链接

```php
// 位置 Blog
\MPartner::all('Blog')
// 位置 Cms
\MPartner::all('Cms')
```

## 位置注册

**使用界面方式**

![](https://ms-assets.modstart.com/data/image/2022/03/21/13461_n5p9_9856.png)

**使用代码方式**

```php
\Module\Partner\Biz\PartnerPositionBiz::registerQuick('位置', '描述');
```


{ADMIN_MENUS}
