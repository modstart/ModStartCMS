<?php


namespace App\Console\Commands;


use Module\Vendor\Command\BaseDumpDemoDataCommand;

class DumpDemoDataCommand extends BaseDumpDemoDataCommand
{
    public function handle()
    {
        $data = [
            'inserts' => $this->buildInsert(
                ['config', ['key', 'value'], function ($item) {
                    return !in_array($item['key'], ['moduleEnableList']);
                }],
                'article',
                'cms_job',
                'nav',
                'partner',
                ['case', ['title', 'content', 'cover']], 'case_category',
                ['news', ['title', 'content', 'cover', 'summary']], 'news_category',
                'product', 'product_category',
                'landing_page_item'
            ),
            'updates' => $this->buildUpdate(
                ['landing_page', ['url' => ''], ['title']]
            ),
        ];
        $this->buildDump($data);
    }
}