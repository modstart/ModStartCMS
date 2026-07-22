## 模块介绍

「通用用户系统」提供一个基础的用户管理功能。

```mind
功能特性
    注册
        用户名注册
        手机注册
        邮箱注册
    登录
        用户名密码登录
        手机验证码登录
    找回密码
        邮箱找回密码
        手机找回密码
    绑定信息
        绑定手机
        绑定邮箱
    授权登录
        开放式授权登录接口
        微信公众号授权登录（MemberOauth）
        QQ
        支付宝
        抖音
        小程序扫码
        微信关注登录扫码（MemberWechatMpLogin)
    钱包
        钱包充值
        钱包提现
        钱包流水
    积分
        积分充值
        积分流程
        名称自定义
    分组
        用户分组
    VIP
        VIP特权
        VIP开放数据接入
```

## 常见问题

#### 如何获取到当前用户分组和VIP等级？

通过以下方法获取到当前用户

```php
\Module\Member\Auth\MemberUser::id()
\Module\Member\Auth\MemberUser::get()
```


## 模块入口

- `/login` 用户登录
- `/register` 用户注册
- `/member` 会员中心
- `/member_profile/profile` 个人资料
- `/member_message` 站内消息
- `/member_vip` 会员VIP
- `/member_money` 我的钱包
- `/member_credit` 我的积分
- `/member_address` 收货地址

{ADMIN_MENUS}
