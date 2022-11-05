<?php

use Illuminate\Database\Migrations\Migration;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsModelUtil;

class InitCmsData extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        CmsModelUtil::build([
            'name' => 'news',
            'title' => '新闻资讯',
            'listTemplate' => 'news.blade.php',
            'detailTemplate' => 'news.blade.php',
        ]);
        CmsModelUtil::build([
            'name' => 'cases',
            'title' => '客户案例',
            'listTemplate' => 'cases.blade.php',
            'detailTemplate' => 'cases.blade.php',
        ]);
        CmsModelUtil::build([
            'name' => 'product',
            'title' => '产品中心',
            'listTemplate' => 'product.blade.php',
            'detailTemplate' => 'product.blade.php',
        ], [
            [
                'name' => 'price',
                'title' => '价格',
                'fieldType' => 'text',
                'isRequired' => false,
            ],
        ]);
        CmsModelUtil::build([
            'name' => 'job',
            'title' => '岗位招聘',
            'listTemplate' => 'job.blade.php',
            'detailTemplate' => 'job.blade.php',
        ], [
            [
                'name' => 'type',
                'title' => '职位类型',
                'fieldType' => 'select',
                'fieldData' => [
                    'options' => ['财务', '技术', '销售', '后勤']
                ]
            ],
            [
                'name' => 'amount',
                'title' => '招聘人数',
                'fieldType' => 'text',
            ],
        ]);
        CmsCatUtil::build('news', [
            'url' => 'news',
            'title' => '新闻资讯',
            'listTemplate' => 'news.blade.php',
            'detailTemplate' => 'news.blade.php',
        ]);
        CmsCatUtil::build('news', [
            'url' => 'news/kind1',
            'title' => '行业资讯',
            'listTemplate' => 'news.blade.php',
            'detailTemplate' => 'news.blade.php',
        ], 'news');
        CmsCatUtil::build('news', [
            'url' => 'news/kind2',
            'title' => '产品动态',
            'listTemplate' => 'news.blade.php',
            'detailTemplate' => 'news.blade.php',
        ], 'news');
        CmsCatUtil::build('news', [
            'url' => 'news/kind3',
            'title' => '融资投资',
            'listTemplate' => 'news.blade.php',
            'detailTemplate' => 'news.blade.php',
        ], 'news');
        CmsCatUtil::build('cases', [
            'url' => 'cases',
            'title' => '客户案例',
            'listTemplate' => 'cases.blade.php',
            'detailTemplate' => 'cases.blade.php',
        ]);
        CmsCatUtil::build('product', [
            'url' => 'product',
            'title' => '产品中心',
            'listTemplate' => 'product.blade.php',
            'detailTemplate' => 'product.blade.php',
        ]);
        CmsCatUtil::build('product', [
            'url' => 'product/kind1',
            'title' => '产品分类1',
            'listTemplate' => 'product.blade.php',
            'detailTemplate' => 'product.blade.php',
        ], 'product');
        CmsCatUtil::build('product', [
            'url' => 'product/kind2',
            'title' => '产品分类2',
            'listTemplate' => 'product.blade.php',
            'detailTemplate' => 'product.blade.php',
        ], 'product');
        CmsCatUtil::build('product', [
            'url' => 'product/kind3',
            'title' => '产品分类3',
            'listTemplate' => 'product.blade.php',
            'detailTemplate' => 'product.blade.php',
        ], 'product');
        CmsCatUtil::build('job', [
            'url' => 'job',
            'title' => '岗位招聘',
            'listTemplate' => 'job.blade.php',
            'detailTemplate' => 'job.blade.php',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
