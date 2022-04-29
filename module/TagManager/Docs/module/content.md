## 模块介绍

「标签云」是一个提供内容标签统一管理系统，同时提供一个标签聚合页面，方便搜索筛查。


## 功能特性

- 标签云 地址 /tag_manager
- 标签自动统计

## 如何使用

以 CMS 实现为例，三步即可实现。

第一步，实现一个 AbstractTagManagerBiz 类

```php
class CmsTagManagerBiz extends AbstractTagManagerBiz
{
    public function name()
    {
        return 'cms';
    }

    public function title()
    {
        return '通用CMS';
    }

    public function searchUrl($tag)
    {
        return modstart_web_url('tag/' . urlencode($tag));
    }
}
```

第二步，注册到标签云中

```php
TagManagerBiz::register(CmsTagManagerBiz::class);
```

第三步，在内容管理关键页面增加标签增加、修改、删除的功能

```php
// 增加标签
TagManager::putTags('cms', ['标签']);
// 修改标签
TagManager::updateTags('cms', ['旧标签'], ['新标签']);
// 删除标签
TagManager::deleteTags('cms', ['标签']);
```
