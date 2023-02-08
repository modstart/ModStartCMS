## 模块介绍

「通用用户系统」提供一个基础的用户管理功能。

## 功能特性

- 注册、登录
- 找回密码（通过手机或邮箱）
- 绑定手机
- 绑定邮箱
- 三方授权登录
- 用户钱包
- 用户积分
- 用户分组


## 常见问题

#### 如何保证用户登录后才可以访问方法？

当前 `Controller` 实现 `Module\Member\Support\MemberLoginCheck` 接口，同时在 Route 中使用中间件 `WebAuthMiddleware`

#### 如何获取到当前用户？


通过以下方法获取到当前用户

```php
\Module\Member\Auth\MemberUser::id()
\Module\Member\Auth\MemberUser::get()
```

在页面上判断用户分组和VIP等级

```php
@if(\Module\Member\Auth\MemberGroup::inGroupIds([1,2]))
    是用户组1、2、3
@endif

@if(\Module\Member\Auth\MemberVip::get('id')==1)
    是VIP1
@endif

@if(\Module\Member\Auth\MemberVip::isDefault())
    是默认VIP用户
@endif
```
