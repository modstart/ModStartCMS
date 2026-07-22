## 模块介绍

「通用导航配置」提供多位置的导航配置工具

## 功能特性

- 多位置管理
- 窗口打开方式设置
- 文字、链接灵活配置
- 支持二级菜单
- 菜单图标


## 调用方式

### 根据位置直接渲染菜单HTML

```html
<div>
    {!!  \Module\Nav\View\NavView::position('head') !!}
</div>
```

### 使用菜单数据自定义直接渲染

```html
@foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('footer') as $nav)
    <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue($nav)}}>
        {{$nav['name']}}
    </a>
@endforeach
```


## 使用场景

- 网站主导航菜单配置
- 底部导航链接管理
- 侧边栏导航自定义

{ADMIN_MENUS}

