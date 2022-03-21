## 模块介绍

「友情链接管理」提供了一个基于位置的友情链接管理模块

## 功能特性

- 多位置配置
- 支持文字和图片
- 一行代码在系统中引入

## 如何调用友情链接

需要使用的 `blade` 模板中直接引入

**常规版本**

```
@include('module::Partner.View.pc.public.partner',['position'=>'位置'])
```

**透明版本**

```
@include('module::Partner.View.pc.public.partnerTransparent',['position'=>'位置'])
```

## 快速实现自定义位置的友情链接

第一步，增加一组友情链接位置

![image-20220321114420076](https://ms-assets.modstart.com/data/image/2022/03/21/13461_n5p9_9856.png)

第二步，使用如下代码在blade页面调用

```html
@include('module::Partner.View.pc.public.partnerTransparent',['position'=>'位置'])
```
