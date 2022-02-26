# 如何实现一个内容审核提供者

---

## 第一步，实现一个内容审核提供者

```php
class XxxPostContentVerifyProvider extends AbstractContentVerifyProvider
{
    const NAME = 'Xxx_Post';
    const TITLE = '内容发布审核';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return self::TITLE;
    }

    public function verifyAutoProcess($param)
    {
        $record = ModelUtil::get('post', intval($param['id']));
        $pass = $this->censorRichHtmlSuccess('Discuss_Censor', HtmlUtil::text2html($record['title']) . '' . $record['content']);
        if ($pass) {
            ModelUtil::update('post', $record['id'], ['status' => PostStatus::VERIFY_PASS]);
        }
        return $pass;
    }

    public function buildForm(Form $form, $param)
    {
        $record = ModelUtil::get('post', intval($param['id']));
        $form->display('title', '标题')->addable(true);
        $form->html('content', '内容')->html($record['content']);
        if ($record['status'] == PostStatus::WAIT_VERIFY) {
            $record['status'] = PostStatus::VERIFY_PASS;
            $form->radio('status', '状态')->options([
                PostStatus::VERIFY_PASS => '通过',
                PostStatus::VERIFY_REJECT => '拒绝',
            ]);
            if (Request::isPost()) {
                return $form->formRequest(function (Form $form) use ($record) {
                    $data = ArrayUtil::keepKeys($form->dataForming(), ['status']);
                    ModelUtil::update('post', $record['id'], $data);
                    return Response::redirect('[reload]');
                });
            }
        } else {
            $form->type('status', '状态')->type(PostStatus::class)->addable(true)->readonly(true);
            $form->showSubmit(false);
        }
        $form->showReset(false);
        $form->item($record)->fillFields();
    }

    public function verifyCount()
    {
        return ModelUtil::count('post', ['status' => PostStatus::WAIT_VERIFY]);
    }

    public function verifyRule()
    {
        return '\\Module\\Xxx\\Admin\\Controller@PostController@verifyList';
    }

}
```

## 第二步，注册内容提供者

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
