## 模块介绍

「通用导航配置」提供多位置的导航配置工具

## 功能特性

- 多位置管理
- 窗口打开方式设置
- 文字、链接灵活配置


## 调用方式


```php
@foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('footer') as $nav)
    <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue($nav)}}>{{$nav['name']}}</a>
@endforeach
```

