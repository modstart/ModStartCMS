## 模块介绍

「ModStart基础包」提供公共的基础服务，几乎所有的模块都需要依赖该模块的方法和类。

## 提供者 Provider

提供者提供了抽象的服务，可以在模块中实现具体的业务支持。

- Captcha 人机验证提供者
- CensorImage 图片智能审核提供者
- CensorText 文字智能审核提供者
- ContentVerify 内容审核提供者
- HomePage 首页提供者
- IDManager ID管理提供者
- LBS 地理位置服务提供者
- LiveStream 直播流提供者
- MailSender 邮件发送提供者
- Notifier 通知提供者
- RandomImage 随机图片提供者
- RichContent 富文本内容提供者
- Schedule 调度提供者
- SearchBox 多搜提供者
- SiteTemplate 网站模板提供者
- SiteUrl 网站链接提供者
- SmsSender 短信发送提供者
- SmsTemplate 短信模板提供者
- SuperSearch 超级搜索
- VideoStream 视频点播提供者
- OCR 图片文字识别

## 使用者 Biz

使用者提供了具体的应用服务，不同的使用者可以注册完成应用服务的使用。

## 使用教程

### ContentVerify 内容审核提供者

可以为网站提供一个审核系统，目前支持人工审核和智能审核，智能审核需要搭配 `CensorImage` 和 `CensorText` 可以实现智能审核

目前图片智能审核提供者支持：

- [魔众图片审核](/m/CensorImageTecmz)

目前文字智能审核提供者支持：

- [魔众文字审核](/m/CensorTextTecmz)

使用方式请参见 [如何实现一个内容审核提供者](/m/Vendor/doc/ContentVerifyManual)

### Notifier 通知提供者

可以为网站提供一个即时通知的功能，目前通知提供者支持：

- [邮件消息通知](/m/NotifierEmail)
- [钉钉消息通知](/m/NotifierDingTalk)
- [企业微信消息通知](/m/NotifierWorkWeixin)

第一步，注册通知类型

在 `ModuleServiceProvider::boot` 中注册业务

```php
NotifierBizWidget::register('Xxx_NewOrder', 'XXX-新订单');
```

第二步，在业务处调用通知

```php
// 使用单一内容
NotifierProvider::notify('Xxx_NewOrder', 'XXX-新订单', "订单号：xxx，支付金额：xxx");
// 或者数组内容
NotifierProvider::notify('Xxx_NewOrder', '有新的咨询', [
    '单号' => 'xxxxxxxxx',
    '时间' => date('Y-m-d H:i:s'),
    '内容' => '想请问一下消息通知靠谱吗？',
    'QQ' => '2131311518',
]);
```
