## 模块介绍

「ModStart基础包」提供公共的基础服务，几乎所有的模块都需要依赖该模块的方法和类。

## 提供者 Provider

提供者（Provider）提供了抽象的服务，可以在模块中实现具体的业务支持。一个简单的例子，系统提供一周抽象的人机验证方式，如果你提供了一个具体的人机验证方式，那么你就可以实现一个人机验证提供者。

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

{ADMIN_MENUS}

