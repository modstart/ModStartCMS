## 模块介绍

「友情链接管理」提供了一个基于位置的友情链接管理模块

```mind
功能特性
    多位置配置
    独立页面友情链接展示
    支持文字和图片
    一行代码在系统中引入
```

## 如何调用友情链接

需要使用的 `blade` 模板中直接引入

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

## 快速实现自定义位置的友情链接

### 第一步，增加一组友情链接位置

**使用界面方式**

![](https://ms-assets.modstart.com/data/image/2022/03/21/13461_n5p9_9856.png)

**使用代码方式**

```php
\Module\Partner\Biz\PartnerPositionBiz::registerQuick('位置', '描述');
```

### 第二步，使用如下代码在blade页面调用

```html
{!! \Module\Partner\View\PartnerView::transparent('位置') !!}
```

{ADMIN_MENUS}
