## 模块介绍

「ModStart基础包」提供公共的基础服务，几乎所有的模块都需要依赖该模块的方法和类。

## 功能特性

- 统一的提供者（Provider）机制：人机验证、内容审核、通知、短信、邮件、搜索、直播、LBS 等抽象服务
- 统一的使用者（Biz）机制：计划任务等常用应用服务
- 安装引导：安装检测、安装准备、执行安装、安装锁定
- 基础能力：验证码、会话管理、占位图、内容审核接口

```mind
功能特性
    提供者 Provider
        人机验证 CaptchaProvider
        内容审核 ContentVerify/CensorImage/CensorText
        消息通知 MailSender/Notifier
        短信 SmsSender/SmsTemplate
        搜索 SearchBox/SuperSearch
        多媒体 LiveStream/VideoStream
        位置与识别 LBS/Ocr
        其他能力 RandomImage/RichContent/SiteUrl
    使用者 Biz
        计划任务 ScheduleBiz
    基础入口
        安装引导
        验证码
        会话管理
```

## 使用场景

- 作为其他业务模块的基础依赖，为其提供统一的能力抽象
- 通过实现对应的 Provider，无缝替换系统的验证码、短信、邮件、搜索、存储等能力

## 提供者 Provider

提供者（Provider）提供了抽象的服务，可以在模块中实现具体的业务支持。一个简单的例子，系统提供了一周抽象的人机验证方式，如果你提供了一个具体的人机验证方式，那么你就可以实现一个人机验证提供者。

- `CaptchaProvider` 人机验证
- `CensorImageProvider` 图片智能审核
- `CensorTextProvider` 文字智能审核
- `ContentVerifyProvider` 内容审核
- `DataRefProvider` 上传文件引用引用
- `HomePageProvider` 首页
- `IDManagerProvider` ID管理
- `LBSProvider` 地理位置服务
- `LiveStreamProvider` 直播流
- `MailSenderProvider` 邮件发送
- `NotifierProvider` 通知
- `RandomImageProvider` 随机图片
- `RichContentProvider` 富文本内容
- `SearchBoxProvider` 多搜
- `SiteTemplateProvider` 网站模板
- `SiteUrlProvider` 网站链接
- `SmsSenderProvider` 短信发送
- `SmsTemplateProvider` 短信模板
- `SuperSearchProvider` 超级搜索
- `VideoStreamProvider` 视频点播
- `OcrProvider` 图片文字识别

## 使用者 Biz

使用者（Biz）提供了具体的应用服务，不同的使用者可以注册完成应用服务的使用。 一个简单的例子，系统提供了一个评论使用者，如果你需要评论功能，那么你就可以使用评论使用者。

- `ScheduleBiz` 计划任务

## 模块入口

- `/install/ping` 安装检测
- `/install/prepare` 安装准备
- `/install/execute` 执行安装
- `/install/lock` 安装锁定
- `/captcha/image` 验证码
- `/session` 会话管理
- `/placeholder/{width}x{height}` 占位图
- `/content_verify/{name}` 内容审核

{ADMIN_MENUS}
