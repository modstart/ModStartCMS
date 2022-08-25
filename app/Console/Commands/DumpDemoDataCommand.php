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
                    return in_array($item['key'], [
                        'siteName',
                        'siteDescription',
                        'siteKeywords',
                        'siteLogo',
                        'siteSlogan',
                        'siteDomain',
                        'Cms_HomeInfoTitle',
                        'Cms_HomeInfoImage',
                        'Cms_HomeInfoContent',
                        'Cms_CompanyName',
                        'Cms_ContactEmail',
                        'Cms_ContactPhone',
                        'Cms_ContactAddress',
                    ]);
                }],
                'cms_content',
                'cms_m_cases',
                'cms_m_job',
                'cms_m_news',
                'cms_m_product',
                'nav',
                'banner',
                'partner'
            ),
            'updates' => $this->buildUpdate(),
        ];
        $this->buildDump($data);
    }
}
