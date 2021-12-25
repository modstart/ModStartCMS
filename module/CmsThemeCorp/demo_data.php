<?php

use Module\Banner\Type\BannerType;

return [
    'tables' => [
        'banner' => [
            'where' => [
                'position' => 'home',
            ],
            'records' => [
                [
                    'type' => BannerType::IMAGE_TITLE_SLOGAN_LINK,
                    'image' => 'vendor/CmsThemeCorp/img/bg_image_1.jpg',
                    'title' => '我们是相信创意的工作室',
                    'slogan' => '有创意的设计',
                    'linkText' => '立即查看',
                ],
                [
                    'type' => BannerType::IMAGE_TITLE_SLOGAN_LINK,
                    'image' => 'vendor/CmsThemeCorp/img/bg_image_2.jpg',
                    'title' => '我们结合设计、思维和技术',
                    'slogan' => '科技创新，有我们就好',
                    'linkText' => '立即查看',
                ],
                [
                    'type' => BannerType::IMAGE_TITLE_SLOGAN_LINK,
                    'image' => 'vendor/CmsThemeCorp/img/bg_image_3.jpg',
                    'title' => '建立完美的网站',
                    'slogan' => '为您的网站制作模板',
                    'linkText' => '立即查看',
                ]
            ]
        ],
        'news' => [
            'records' => array_build([
                [
                    '亚马逊暂停恢复仓储中心禁令，可以在工作时保留个人手机',
                    'vendor/CmsThemeCorp/img/news-1.jpeg',
                    '据报道，亚马逊证实将放弃在仓库中禁用个人手机的新举措。该公司在 12 月 17 日告知员工，他们可以继续在工作时保留个人手机，“直至进一步通知”。'
                ],
                [
                    '三星开始为特斯拉全新车载电脑制造芯片：比真人司机安全 10 倍',
                    'vendor/CmsThemeCorp/img/news-2.jpeg',
                    '三星已经开始为特斯拉高级辅助驾驶系统 FSD 生产芯片，并着手为自动汽车和自动驾驶汽车市场开发全新车载电脑产品。'
                ],
                [
                    'YouTube TV 与迪士尼宣布续约，频道内容恢复',
                    'vendor/CmsThemeCorp/img/news-3.jpeg',
                    '据报道，今日，迪士尼和 YouTube TV 达成协议，将迪士尼拥有的十余个频道重新提供给谷歌互联网电视流媒体服务。'
                ],
                [
                    '阿迪达斯初尝 NFT 甜头：赚取超 2300 万美元',
                    'vendor/CmsThemeCorp/img/news-4.jpeg',
                    '北京时间 12 月 20 日早间消息，据报道，阿迪达斯首次对 NFT 的尝试似乎获得了成功。该公司最近一个季度的利润为 5.384 亿美元，2300 万美元在其中的占比并不算低。'
                ],
            ], function ($k, $v) {
                return [$k, [
                    'title' => $v[0],
                    'cover' => $v[1],
                    'summary' => $v[2],
                    '_data' => [
                        'content' => '<p>北京时间 12 月 20 日早间消息，据报道，亚马逊出于安全考虑，正放松对仓储中心员工活动的控制。</p><p>这家互联网巨头向媒体证实，该公司将放弃在仓库中禁用个人手机的新举措。该公司在 12 月 17 日告知员工，他们可以继续在工作时保留个人手机，“直至进一步通知”。</p><p>亚马逊多年以来一直在仓库中禁用个人手机，但在新冠疫情爆发后放松了这项规定。他们原计划在 2022 年 1 月重新启用这项禁令。</p>',
                    ]]
                ];
            }),
        ],
        'product' => [
            'records' => array_build([1, 2, 3, 4], function ($k, $v) {
                return [$k, [
                    'title' => '科技产品' . $k,
                    'cover' => 'vendor/CmsThemeCorp/img/product-' . $v . '.jpg',
                ]];
            })
        ],
        'cases' => [
            'records' => array_build([1, 2, 3, 4, 5, 6], function ($k, $v) {
                return [$k, [
                    'title' => '案例某某' . $k,
                    'cover' => 'vendor/CmsThemeCorp/img/work-' . $v . '.jpeg',
                ]];
            })
        ],
        'job' => [
            'records' => [
                [
                    'title' => '招聘IT工程师',
                    '_data' => [
                        'content' => '<p>岗位招聘说明</p>',
                    ]
                ]
            ]
        ],
        'nav' => [
            'where' => [
                'position' => 'head',
            ],
            'records' => [
                [
                    'name' => '首页',
                    'link' => modstart_web_url(''),
                ],
                [
                    'name' => '产品',
                    'link' => modstart_web_url('product'),
                ],
                [
                    'name' => '案例',
                    'link' => modstart_web_url('cases'),
                ],
                [
                    'name' => '新闻',
                    'link' => modstart_web_url('news'),
                ],
                [
                    'name' => '招聘',
                    'link' => modstart_web_url('job'),
                ],
                [
                    'name' => '关于',
                    'link' => modstart_web_url('about'),
                ],
                [
                    'name' => '留言',
                    'link' => modstart_web_url('message'),
                ],
            ],
        ],
        'info' => [
            'records' => [
                'Cms_HomeInfoImage' => 'vendor/CmsThemeCorp/img/about.jpg',
                'Cms_HomeInfoTitle' => '某某某科技有限公司',
                'Cms_HomeInfoContent' => '<p>我将给你一个完整的系统说明，但我必须向你解释这一切谴责快乐和赞美痛苦的错误观念是如何产生的，并阐述伟大的探险家的实际教义。</p>',
            ]
        ],
    ],
];