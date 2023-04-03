# 如何实现一个内容审核提供者

---

## 如何实现一个内容审核提供者

### 第一步，实现一个内容审核提供者

```php
class XxxPostContentVerifyProvider extends AbstractContentVerifyProvider
{
    const NAME = 'XxxPost';
    const TITLE = '内容发布审核';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return self::TITLE;
    }

    public function verifyCount()
    {
        return ModelUtil::count('post', ['status' => PostStatus::WAIT_VERIFY]);
    }

    public function verifyRule()
    {
        return '\\Module\\Xxx\\Admin\\Controller\\PostController@verifyList';
    }

}
```

### 第二步，注册内容提供者

在 ModuleServiceProvider 中注册内容审核提供者和通知提供者

```php
// 注册内容审核提供者
ContentVerifyProvider::register(XxxPostContentVerifyProvider::class);
// 注册通知的目的是为了发送通知链接
NotifierBizWidget::register(XxxPostContentVerifyProvider::NAME, XxxPostContentVerifyProvider::TITLE);
```

## 第三步，创建审核任务

```php
// 创建一个审核任务
ContentVerifyJob::create(XxxPostContentVerifyProvider::NAME, ['id' => $post['id']], $post['title']);
```
