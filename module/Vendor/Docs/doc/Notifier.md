# Notifier 通知提供者使用教程

---

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
