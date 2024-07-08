<?php

namespace Module\DataAliyunOssFe\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Support\Concern\HasFields;

class ConfigController extends Controller
{
    public function index(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('阿里云OSS存储(前端直传)');
        /** @var HasFields $builder */
        $builder->layoutPanel('公共配置', function ($builder) {
            /** @var HasFields $builder */
            $builder->switch('DataAliyunOssFe_Enable', '启用');
            $builder->select('DataAliyunOssFe_Region', '地区')
                ->options([
                    'cn-hangzhou' => '华东1（杭州）',
                    'cn-shanghai' => '华东2（上海）',
                    'cn-qingdao' => '华北1（青岛）',
                    'cn-beijing' => '华北2（北京）',
                    'cn-zhangjiakou' => '华北3（张家口）',
                    'cn-huhehaote' => '华北5（呼和浩特）',
                    'cn-shenzhen' => '华南1（深圳）',
                    'cn-hongkong' => '中国（香港）',
                    'ap-southeast-1' => '亚太东南1（新加坡）',
                    'ap-southeast-2' => '亚太东南2（悉尼）',
                    'ap-southeast-3' => '亚太东南3（吉隆坡）',
                    'ap-southeast-5' => '亚太东南5（雅加达）',
                    'ap-south-1' => '亚太南部1（孟买）',
                    'ap-northeast-1' => '亚太东北1（东京）',
                    'ap-northeast-2' => '亚太东北2（首尔）',
                    'ap-south-1' => '亚太南部1（孟买）',
                    'eu-central-1' => '欧洲中部1（法兰克福）',
                    'eu-west-1' => '欧洲西部1（伦敦）',
                    'me-east-1' => '中东东部1（迪拜）',
                    'us-west-1' => '美国西部1（硅谷）',
                    'us-east-1' => '美国东部1（弗吉尼亚）',
                    'ap-northeast-1' => '亚太东北1（东京）',
                    'ap-southeast-1' => '亚太东南1（新加坡）',
                    'ap-southeast-2' => '亚太东南2（悉尼）',
                ]);
            $builder->text('DataAliyunOssFe_Bucket', 'Bucket');
            $builder->text('DataAliyunOssFe_Endpoint', 'Endpoint')
                ->help('格式为 oss-cn-xxx.aliyuncs.com');
            $builder->text('DataAliyunOssFe_Domain', 'Bucket域名')
                ->help('如果您的OSS开启了CDN加速，可直接配置CDN域名（如 http://xxx.oss-cn-xxx.aliyuncs.com）')
                ->ruleUrl();
        });

        $builder->layoutPanel('服务端配置', function ($builder) {
            /** @var HasFields $builder */

            $builder->html('', '配置说明')->htmlContentFromMarkdown(
                '
**用于 OSS 文件的增删改查管理**

需要开通 OSS 上传权限，权限策略参考内容如下：

```
{
    "Version": "1",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "oss:Get*",
                "oss:Put*",
                "oss:DeleteObject"
            ],
            "Resource": [
                "acs:oss:*:*:test-oss",
                "acs:oss:*:*:test-oss/*"
            ]
        }
    ]
}
```
'
            );
            $builder->text('DataAliyunOssFe_AccessKeyId', 'AccessKeyId');
            $builder->text('DataAliyunOssFe_AccessKeySecret', 'AccessKeySecret');
        });

        $builder->layoutPanel('前端配置', function ($builder) {
            /** @var HasFields $builder */
            $builder->html('', '账号说明')->htmlContentFromMarkdown(
                '
**用户前端受限的上传权限**

需要开通

① OSS 上传权限，权限策略参考内容如下：

```json
{
    "Version": "1",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": "oss:PutObject",
            "Resource": "acs:oss:*:*:test-oss/data_temp/*"
        }
    ]
}
```

② STS 服务权限

需要开通 AliyunSTSAssumeRoleAccess 系统策略。
'
            );
            $builder->text('DataAliyunOssFe_Front_AccessKeyId', 'AccessKeyId');
            $builder->text('DataAliyunOssFe_Front_AccessKeySecret', 'AccessKeySecret');
            $builder->text('DataAliyunOssFe_Front_StsEndpoint', 'StsEndpoint')
                ->help('例如 sts.cn-shanghai.aliyuncs.com ，查看对应地区 <a target="_blank" href="https://help.aliyun.com/zh/ram/developer-reference/api-sts-2015-04-01-endpoint">Endpoint</a>');
            $builder->text('DataAliyunOssFe_Front_RoleArn', 'RoleArn')
                ->help('例如 acs:ram::148316xxxxxxxxx:role/ms-oss-test-fe');
        });

        $builder->formClass('wide');
        return $builder->perform();
    }
}
