## 模块介绍

「内容区块」是一个后台提供便捷的的内容块管理代码，方便前端调用展示


```mind
功能特性
    区块类型
        通用（多图片、多文字）
        单图
        富文本
    功能配置
        开始时间
        结束时间
        启用/禁用
```

## 区块类型

- `basic`: 通用
- `image`: 单图
- `html`: 富文本

## 使用说明

### 获取一个区块内容

```php
@foreach([MContentBlock::getCached('标识')] as $cb)
    <p>类型：{{$cb['type']}}</p>
    <p>名称：{{$cb['name']}}</p>
    <p>标题：{{$cb['title']}}</p>
    <p>图片：{{$cb['image']}}</p>
    <p>链接：{{$cb['link']}}</p>
    <p>HTML：{{$cb['content']}}</p>
@endforeach
```

### 获取多个区块内容

```php
@foreach(MContentBlock::allCached('标识',5) as $cb)
    <p>类型：{{$cb['type']}}</p>
    <p>名称：{{$cb['name']}}</p>
    <p>标题：{{$cb['title']}}</p>
    <p>图片：{{$cb['image']}}</p>
    <p>链接：{{$cb['link']}}</p>
    <p>HTML：{{$cb['content']}}</p>
@endsection
```

{ADMIN_MENUS}
