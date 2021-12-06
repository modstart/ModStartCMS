## 模块介绍

「友情链接管理」提供了一个基于位置的友情链接管理模块

## 功能特性

- 多位置配置
- 支持文字和图片
- 一行代码在系统中引入

## 使用说明

需要使用的 `blade` 模板中直接引入


**常规版本**

```
@include('module::Partner.View.pc.public.partner',['position'=>'home'])
``` 


**透明版本**

```
@include('module::Partner.View.pc.public.partnerTransparent',['position'=>'home'])
``` 
