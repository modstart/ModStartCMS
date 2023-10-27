<?php


namespace Module\Vendor\Util;


use ModStart\Core\Assets\AssetsUtil;

class FileIconUtil
{
    public static function url($ext)
    {
        $base = 'asset/vendor/ueditor/themes/default/exts/';
        $map = [
            'ai' => 'ai',
            'apk' => 'apk',
            'chm' => 'chm',
            'css' => 'css',
            'doc' => 'doc',
            'docx' => 'docx',
            'dwg' => 'dwg',
            'gif' => 'gif',
            'html' => 'html',
            'jpeg' => 'jpeg',
            'jpg' => 'jpg',
            'log' => 'log',
            'mp3' => 'mp3',
            'mp4' => 'mp4',
            'pdf' => 'pdf',
            'png' => 'png',
            'ppt' => 'ppt',
            'pptx' => 'pptx',
            'psd' => 'psd',
            'rar' => 'rar',
            'svg' => 'svg',
            'torrent' => 'torrent',
            'txt' => 'txt',
            'xls' => 'xls',
            'xlsx' => 'xlsx',
            'zip' => 'zip',
            'default' => 'unknown',
            '_DIR_' => 'folder',
        ];
        $icon = (isset($map[$ext]) ? $map[$ext] : $map['default']);
        return AssetsUtil::fixFull($base . $icon . '.svg');
    }
}
