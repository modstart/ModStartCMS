## 模块介绍

「ModStart基础包」提供公共的基础服务，几乎所有的模块都需要依赖该模块的方法和类。

## 提供者和使用者 Provider 和 Biz

提供者（Provider）提供了抽象的服务，可以在模块中实现具体的业务支持。一个简单的例子，系统提供一周抽象的人机验证方式，如果你提供了一个具体的人机验证方式，那么你就可以实现一个人机验证提供者。

使用者（Biz）提供了具体的应用服务，不同的使用者可以注册完成应用服务的使用。 一个简单的例子，系统提供了一个评论使用者，如果你需要评论功能，那么你就可以使用评论使用者。

- Captcha 人机验证提供者
- CensorImage 图片智能审核提供者
- CensorText 文字智能审核提供者
- HomePage 首页提供者
- IDManager ID管理提供者
- LBS 地理位置服务提供者
- LiveStream 直播流提供者
- MailSender 邮件发送提供者
- Notifier 通知提供者
- RandomImage 随机图片提供者
- RichContent 富文本内容提供者
- SearchBox 多搜提供者
- SiteTemplate 网站模板提供者
- SiteUrl 网站链接提供者
- SmsSender 短信发送提供者
- SmsTemplate 短信模板提供者
- SuperSearch 超级搜索
- VideoStream 视频点播提供者
- OCR 图片文字识别


{ADMIN_MENUS}

