<?php


namespace Module\Vendor\QuickRun\Crawl;


trait HasCrawlerTools
{
    public function usePHPQuery()
    {
        require_once __DIR__ . '/libs/phpQuery-onefile.php';
    }

//    // 如 http://example.com/data/server/put
//    protected $DATA_CLIENT_SERVER = null;
//    // 如 xxxxxxxxxxx
//    protected $DATA_CLIENT_KEY = null;
//    // 如 http://cdn.example.com
//    protected $IMAGE_CDN = null;
//
//    protected function prepareRichContent($content, $pageUrl)
//    {
//
//        // 替换掉懒加载
    /*        preg_match_all('/(<img.*?)data-original="(.*?)"(.*?>)/i', $content, $mat);*/
//        if (!empty($mat[0])) {
//            foreach ($mat[0] as $index => $img) {
//                $content = str_replace($img, '<img src="' . $this->fixPath($mat[2][$index], $pageUrl) . '" />', $content);
//            }
//        }
//
    /*        preg_match_all('/(<img.*?)src="(.*?)"(.*?>)/i', $content, $mat);*/
//        if (!empty($mat[0])) {
//            foreach ($mat[0] as $index => $img) {
//                $content = str_replace($img, '<img src="' . $this->fixPath($mat[2][$index], $pageUrl) . '" />', $content);
//            }
//        }
    /*        preg_match_all('/(<img.*?)src=\'(.*?)\'(.*?>)/i', $content, $mat);*/
//        if (!empty($mat[0])) {
//            foreach ($mat[0] as $index => $img) {
//                $content = str_replace($img, '<img src="' . $this->fixPath($mat[2][$index], $pageUrl) . '" />', $content);
//            }
//        }
//
//        // 全局过滤
//        $content = HtmlHelper::filter($content);
//
//        // 移除a标签
//        $content = preg_replace("/<a[^>]*>(.*?)<\\/a>/is", "$1", $content);
//
//        $content = trim($content);
//
//        return $content;
//    }
//
//    protected function fixPath($path, $pageUrl)
//    {
//        if (empty($pageUrl)) {
//            return $path;
//        }
//        if (empty($path)) {
//            return null;
//        }
//        if (Str::startsWith($path, '//')) {
//            $path = 'http:' . $path;
//        }
//        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
//            return $path;
//        }
//        $urlParam = parse_url($pageUrl);
//        if (Str::startsWith($path, '/')) {
//            return $urlParam['scheme'] . '://' . $urlParam['host'] . $path;
//        }
//        $prefix = substr($pageUrl, 0, strrpos($pageUrl, '/') + 1);
//        return $prefix . $path;
//    }
//
//    /**
//     * 将 一个远程的图片 或 本地的图片 上传到主机
//     *
//     * @param $imageUrl string
//     * @param $isLocal boolean
//     * @return array
//     */
//    protected function localImage($imageUrl, $isLocal = false)
//    {
//        if ($isLocal) {
//            $image = file_get_contents($imageUrl);
//        } else {
//            if (Str::startsWith($imageUrl, '//')) {
//                $imageUrl = 'http:' . $imageUrl;
//            }
//
//            if (!(Str::startsWith($imageUrl, 'http://') || Str::startsWith($imageUrl, 'https://'))) {
//                return Response::generate(-1, '图片路径不是完整URL,忽略 -> ' . $imageUrl);
//            }
//
//            $image = CurlHelper::getContent($imageUrl);
//            if (empty($image)) {
//                return Response::generate(-1, '图片抓取失败 -> ' . $imageUrl);
//            }
//        }
//        $ext = FileHelper::extension($imageUrl);
//
//        // 随机文件存储,否则会全部存在一个文件夹
//        DataService::$UPLOAD_TIMESTAMP = time() - rand(1, 365) * 24 * 3600;
//
//        $category = 'image';
//        $ret = $this->dataService->uploadToData($category, 'image.' . $ext, $image);
//        if ($ret['code']) {
//            return Response::generate(-1, '图片存取失败 -> ' . $imageUrl);
//        }
//
//        $path = $ret['data']['data']['path'];
//        $fileSize = $ret['data']['data']['size'];
//
//        if ($this->DATA_CLIENT_SERVER && $this->DATA_CLIENT_KEY) {
//            $ret = $this->dataServerClient->clientPut(
//                $this->DATA_CLIENT_SERVER,
//                $this->DATA_CLIENT_KEY,
//                $category,
//                $path,
//                $image);
//            if ($ret['code']) {
//                return Response::generate(-1, '图片上传到远程失败 -> ' . $ret['msg']);
//            }
//        }
//
//        if ($this->IMAGE_CDN) {
//            $newImageUrl = $this->IMAGE_CDN . '/data/' . $category . '/' . $path;
//        } else {
//            $newImageUrl = '/data/' . $category . '/' . $path;
//        }
//
//
//        $this->info('图片抓取 -> ' . FileHelper::formatByte($fileSize) . ' -> ' . $imageUrl . ' -> ' . $newImageUrl);
//
//        return Response::generate(0, null, [
//            'url' => $newImageUrl,
//            'size' => $fileSize
//        ]);
//    }
//
//    protected function localContentImage($content)
//    {
//        $images = [];
    /*        preg_match_all('/(<img.*?)src="(.*?)"(.*?>)/i', $content, $mat);*/
//        if (!empty($mat[2])) {
//            $images = array_merge($images, $mat[2]);
//        }
    /*        preg_match_all('/(<img.*?)src=\'(.*?)\'(.*?>)/i', $content, $mat);*/
//        if (!empty($mat[2])) {
//            $images = array_merge($images, $mat[2]);
//        }
//        if (!empty($images)) {
//            $imageMap = [];
//            foreach ($images as $k => $oldImage) {
//                if (empty($imageMap[$oldImage])) {
//
//                    $imageMap[$oldImage] = $oldImage;
//
//                    $ret = $this->localImage($oldImage);
//                    if ($ret['code']) {
//                        $this->error($ret['msg']);
//                        continue;
//                    }
//
//                    $newImage = $ret['data']['url'];
//                    $imageMap[$oldImage] = $newImage;
//
//                }
//            }
//
//            foreach ($imageMap as $oldImage => $newImage) {
//                $content = str_replace($oldImage, $newImage, $content);
//            }
//
//        }
//
//        return $content;
//    }
}
