<?php


namespace Module\Vendor\Command;


use Illuminate\Console\Command;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\SerializeUtil;
use Module\DataImageCompressTecmz\Util\DataImageCompressTecmzUtil;
use Module\Vendor\Provider\ImageCompress\AbstractImageCompressProvider;
use Module\Vendor\Provider\ImageCompress\ImageCompressProvider;

class CompressDirImage extends Command
{
    protected $signature = 'CompressDirImage {statusFile} {file} {--provider=} {--minSizeKB=100}';

    private $statusFile = null;
    private $status = [];

    /**
     * @var AbstractImageCompressProvider
     */
    private $imageCompressProvider;

    public function handle()
    {
        $provider = $this->option('provider');
        if ($provider) {
            $this->imageCompressProvider = ImageCompressProvider::get($provider);
        } else {
            $this->imageCompressProvider = ImageCompressProvider::first();
        }
        BizException::throwsIf('图片压缩服务未配置', empty($this->imageCompressProvider));
        $this->info("图片压缩 Provider：" . $this->imageCompressProvider->name() . ', ' . $this->imageCompressProvider->title());
        $this->statusFile = $this->argument('statusFile');
        if (!file_exists($this->statusFile)) {
            $this->status = [
                'map' => [],
            ];
            $this->syncStatusFile();
        } else {
            $this->status = SerializeUtil::jsonDecode(file_get_contents($this->statusFile));
        }
        $file = $this->argument('file');
        BizException::throwsIf('目录或文件不存在', !file_exists($file));
        $minSizeKB = intval($this->option('minSizeKB'));
        $this->process($file . (is_dir($file) ? '/' : ''), [
            'minSizeKB' => $minSizeKB,
        ]);
    }

    private function syncStatusFile()
    {
        file_put_contents($this->statusFile, SerializeUtil::jsonEncode($this->status));
    }

    protected function process($file, $param)
    {
        $param = array_merge([
            // 超过多少KB才压缩
            'minSizeKB' => 100,
        ], $param);
        if (is_file($file)) {
            $file = realpath($file);
            if (isset($this->status['map'][$file])) {
                $this->warn("跳过已处理文件: $file");
                return;
            }
            $ext = FileUtil::extension($file);
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                return;
            }
            $content = file_get_contents($file);
            $contentSize = strlen($content);
            if ($contentSize < $param['minSizeKB'] * 1024) {
                $this->warn("跳过小于{$param['minSizeKB']}KB的文件: $file, 大小：" . FileUtil::formatByteSimple($contentSize));
                return;
            }
            $ret = $this->imageCompressProvider->process($ext, $content);
            if (Response::isSuccess($ret)) {
                if ($ret['data']['compressSize'] >= $ret['data']['originalSize']) {
                    $this->warn(join(', ', [
                        "图片：$file",
                        "未能压缩到更小",
                        "原大小：" . FileUtil::formatByteSimple($ret['data']['originalSize']),
                        "压缩后：" . FileUtil::formatByteSimple($ret['data']['compressSize']),
                    ]));
                } else {
                    $rate = sprintf('%.2f', $ret['data']['compressSize'] * 100 / $ret['data']['originalSize']);
                    $this->info(join(', ', [
                        "图片：$file",
                        "压缩比：$rate%",
                        "原大小：" . FileUtil::formatByteSimple($ret['data']['originalSize']),
                        "压缩后：" . FileUtil::formatByteSimple($ret['data']['compressSize']),
                    ]));
                    file_put_contents($file, $ret['data']['data']);
                }
                $this->status['map'][$file] = true;
                $this->syncStatusFile();
            } else {
                $this->warn("压缩图片: $file, 错误：{$ret['msg']}");
            }
        } else {
            $files = FileUtil::listFiles($file);
            foreach ($files as $file) {
                $this->process($file['pathname'], $param);
            }
        }
    }
}
