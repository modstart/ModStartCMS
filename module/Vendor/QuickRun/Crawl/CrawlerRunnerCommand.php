<?php


namespace Module\Vendor\QuickRun\Crawl;


use Illuminate\Console\Command;
use ModStart\Core\Util\CodeUtil;
use ModStart\Core\Util\FileUtil;

class CrawlerRunnerCommand extends Command
{
    protected $signature = 'crawler-runner {path}';
    /**
     * @var AbstractCrawler[]
     */
    private $crawlers = [];

    public function handle()
    {
        $path = $this->argument('path');
        if (is_file($path)) {
            $cls = CodeUtil::getFullClassNameForContent(file_get_contents($path));
            $instance = app($cls);
            $this->crawlers[] = $instance;
        } else {
            $files = FileUtil::listFiles($path, '*.php');
            foreach ($files as $file) {
                $cls = CodeUtil::getFullClassNameForContent(file_get_contents($file['pathname']));
                if (preg_match('/\\\\Abstract[A-Za-z0-9_]+$/', $cls)) {
                    continue;
                }
                $instance = app($cls);
                if (!($instance instanceof AbstractCrawler) || preg_match('/\\Abstract[A-Za-z0-9_]+$/', $cls)) {
                    continue;
                }
                $this->crawlers[] = $instance;
            }
        }
        if (empty($this->crawlers)) {
            $this->info("CrawlerRunner: No Crawler Found");
            return;
        }

        $this->info('CrawlerRunner: ' . count($this->crawlers) . " Crawler(s) Found");
        foreach ($this->crawlers as $crawler) {
            $this->info("    > " . get_class($crawler));
        }

        $this->info('CrawlerRunner: Run Start');
        foreach ($this->crawlers as $crawler) {
            $crawler->init();
            app()->call([$crawler, 'setup']);
            $crawler->run();
        }
        $this->info('CrawlerRunner: Run End');
    }
}
