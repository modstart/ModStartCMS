## 简介

`ModStart` 是一个基于 `Laravel` 模块化组织的后台系统框架，很少的代码即可快速构建出一个功能完善的后台系统。其中模块市场包含了丰富则模块，开箱即用，让开发者能够从冗长的代码中提效，对后端开发者非常友好。


- [官方网站](https://modstart.com)
- [Demo / 在线演示](https://cms.demo.tecmz.com)
- [模块市场](https://modstart.com/store)
- [源码地址 / 码云](https://gitee.com/modstart/ModStartCMS)
- [源码地址 / GitHub](https://github.com/modstart/ModStartCMS)

### 技术栈

- [Laravel](https://laravel.com/)
- [LayUI](https://www.layui.com/)
- [Vue](https://vuejs.org/)
- [Element UI](https://element.eleme.io/)
- [jQuery](http://jquery.com)
- ...

### 特性

- 简洁优雅、灵活可扩展
- 后台RBAC权限管理
- Ajax页面无刷新
- 组件按需加载静态资源
- 内置丰富的表格常用功能
- 内置文件上传，无需繁琐的开发
- 模块市场，只需在管理页面点击鼠标即可完成插件的安装、更新和卸载等操作

### 交流

- QQ群：467107293
- 模块开发者QQ群：361233906

### 加入我们

如果您对这个项目感兴趣，非常欢迎加入项目开发团队，参与这个项目的功能维护与开发。

欢迎任何形式的贡献（包括但不限于以下）：

- 贡献代码
- 完善文档
- 撰写教程
- 完善注释
- ...

### 版本策略

ModStart 的版本发行将会参考主流 web 框架的发行策略，尽量降低版本升级带来的影响，最大程度的考虑兼容性问题，小版本的升级将尽量不改动任何功能接口；同时我们也将会提供更新日志，详细说明新版本的改动以及可能造成的影响。

对于小版本的发行，开发者可以放心的升级，基本不用担心代码兼容性问题。只有像从 v2.0.0 到 v3.0.0 这样的大版本升级才可能会有兼容性问题，小版本则基本是完全兼容的（小版本升级也可能会有不兼容的情况，但几率很小）。

## 安装

### 环境要求

- `PHP` >= `5.6`
- `MySQL` >= `5.0`
- `PHP Extension`：`Fileinfo`
- `Apache/Nginx`

### 安装步骤

1. 配置 apache/nginx 服务器，请将网站的根目录配置到 <网站目录>/public
2. 访问 `http://www.xxx.com/install.php`
3. 使用安装引导向导进行安装

<p>
<img src="https://mz-assets.tecmz.com/data/image/2020/04/21/47617_sqcj_4993.jpg" />
</p>

**Nginx参考配置**

```
server {
    listen       80;
    server_name  xx.com;x
    charset utf-8;
    index index.php index.html;
    root /var/www/html/xxx.com/public;
    autoindex off;
    location ^~ /.git {
        deny all;
    }
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  PHP_VALUE  "open_basedir=/var/www/html/xxx.com/:/tmp/:/var/tmp/";
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    location ~ \.(gif|jpg|jpeg|png|bmp|ico|css|js)$ {
       expires max;
    }
    location ~* \.(eot|ttf|woff|woff2)$ {
        add_header Access-Control-Allow-Origin '*';
    }
}
```

**Apache参考配置**

```
<VirtualHost *:80>
    　　ServerName xxx.com
    　　DocumentRoot d:/wwwroot/xxx.com/public
</VirtualHost>
```

### 集成环境

- 宝塔一键安装教程：待完善
- PHPStudy意见安装教程：待完善

### 环境预检

为方便系统环境快速配置，我们提供了服务器端安装环境预检程序。使用方式如下：

1. 通过连接下载文件  <a href="https://modstart.com/env_check.zip" target="_blank">https://modstart.com/env_check.zip</a>  ，解压出 `env_check.php` 文件。
2. 将 `env_check.php` 文件上传到服务器空间，配置通过访问 `http://www.xxx.com/env_check.php` 来查看安装环境是否配置成功，如果环境预检成功，可以看到如下提示。

<p style="text-align:center;">
	<img src="https://www.ms.modstart.com/vendor/ModStart/images/guide/EnvCheck.jpg" style="max-width:300px;" />
</p>


### 升级指南

在升级前，请备份好系统的源代码、数据等信息，按照如下步骤进行操作。

1. 获取最新的 ModStart 源代码包
2. 全量覆盖所有的源代码
3. 使用命令行进入到系统的根路径，运行 `php artisan migrate`，重新构建 `public/asset` 目录和所有基础服务代码；
4. 使用命令行进入到系统的根路径，运行 `php artisan modstart:module-install-all`，重新构建 `public/vendor` 目录和所有扩展包代码。



## 快速开始



在日常开发中，最常见的即是增删改查代码，使用ModStart开发此类功能，会变得非常简单。

下面将会给大家介绍 `ModStart` 的使用方法，以及一个增删改查页面的基本构成。通过学习下面的内容将可以帮助大家快速理解这个系统的基本使用方法。



### 创建数据表

在Laravel的迁移目录创建数据库迁移文件

```php
class CreateNews extends Migration
{
    public function up()
    {
            Schema::create('news', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
                $table->string('title', 200)->nullable()->comment('');
                $table->string('cover', 200)->nullable()->comment('');
                $table->string('summary', 200)->nullable()->comment('');
                $table->text('content')->nullable()->comment('');
            });
        }
    }
    public function down()
    {
    }
}

```



### 创建控制器

增加路由控制器代码，同时按照

```php
class NewsController extends Controller
{
    use HasAdminQuickCRUD;
    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('news')
            ->field(function ($builder) {
                $builder->id('id','ID');
                $builder->text('title', '名称');
                $builder->image('cover', '封面');
                $builder->textarea('summary', '摘要');
                $builder->richHtml('content', '内容');
                $builder->display('created_at', '创建时间');
                $builder->display('updated_at', '更新时间');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', 'ID');
                $filter->like('title', '标题');
            })
            ->title('新闻管理');
    }
}
```



### 增加路由和导航

在 `routes.php` 增加路由信息

```php
$router->match(['get', 'post'], 'news/news', 'NewsController@index');
$router->match(['get', 'post'], 'news/news/add', 'NewsController@add');
$router->match(['get', 'post'], 'news/news/edit', 'NewsController@edit');
$router->match(['get', 'post'], 'news/news/delete', 'NewsController@delete');
$router->match(['get', 'post'], 'news/news/show', 'NewsController@show');
```



在 `ModuleServiceProvider.php` 中注册菜单信息

```php
AdminMenu::register(function () {
  return [
    [
      'title' => '新闻管理',
      'icon' => 'list',
      'sort' => 150,
      'url' => '\App\Admin\Controller\NewsController@index',
    ]
  ];
});
```



### 开发完成

这样一个简单的增删改查页面就开发完成了



## 开发前必读

### 开发前的配置

开发环境请打开 debug 模式（即在 `.env` 文件中设置 `APP_DEBUG=true` ）

### 公共样式

`ModStart` 使用了一些基础样式对页面进行布局，既简单又强大，开始开发前需要对此有所了解。

公共样式对编写页面组件非常有帮助，能显著提高开发效率，建议编写组件前先查阅一遍文档。


## LICENSE

Apache 2.0

