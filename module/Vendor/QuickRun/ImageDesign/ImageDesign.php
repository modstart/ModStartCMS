<?php


namespace Module\Vendor\QuickRun\ImageDesign;


use ModStart\Core\Util\RenderUtil;
use ModStart\Layout\Buildable;

class ImageDesign implements Buildable
{
    private $variables = [];
    private $imageConfig = [];
    private $selectorDialogServer = null;

    public static function make()
    {
        $ins = new static();
        return $ins;
    }

    public function variables($variables)
    {
        $this->variables = $variables;
        return $this;
    }

    public function imageConfig($imageConfig)
    {
        $this->imageConfig = $imageConfig;
        return $this;
    }

    public function selectorDialogServer($selectorDialogServer)
    {
        $this->selectorDialogServer = $selectorDialogServer;
        return $this;
    }

    public static function imageConfigDefault()
    {
        $width = 600;
        $height = 800;
        $config = [
            "width" => $width,
            "height" => $height,
            "backgroundImage" => modstart_web_url('asset/image/none.png'),
            'font' => null,
            "blocks" => [
                [
                    "type" => "text",
                    "x" => $width / 2,
                    "y" => 100,
                    "data" => [
                        "text" => "文字内容",
                        "color" => "#000000",
                        "bold" => false,
                        "size" => 20,
                        "align" => "center",
                    ],
                ],
                [
                    "type" => "image",
                    "x" => $width / 2,
                    "y" => 200,
                    "data" => [
                        "opacity" => 50,
                        "width" => 100,
                        "height" => 100,
                        "image" => modstart_web_url('asset/image/none.png'),
                    ],
                ],
                [
                    "type" => "qrcode",
                    "x" => $width / 2,
                    "y" => 300,
                    "data" => [
                        "width" => 100,
                        "height" => 100,
                        "text" => '这里是二维码的内容',
                    ],
                ],
            ],
        ];
        return $config;
    }

    private function prepareDefault()
    {
        if (empty($this->imageConfig)) {
            $this->imageConfig = self::imageConfigDefault();
        }
        if (empty($this->selectorDialogServer)) {
            $this->selectorDialogServer = modstart_admin_url('data/file_manager');
        }
    }

    public function build()
    {
        $this->prepareDefault();
        $viewData = [
            'variables' => $this->variables,
            'imageConfig' => $this->imageConfig,
            'selectorDialogServer' => $this->selectorDialogServer,
        ];
        echo RenderUtil::view('module::Vendor.View.quickRun.imageDesign.index', $viewData);
    }


}
