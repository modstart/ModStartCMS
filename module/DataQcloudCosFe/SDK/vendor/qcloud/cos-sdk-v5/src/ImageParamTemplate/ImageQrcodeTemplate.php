<?php

namespace Qcloud\Cos\ImageParamTemplate;

class ImageQrcodeTemplate extends ImageTemplate
{
    private $cover;
    private $barType;
    private $segment;
    private $size;

    public function __construct() {
        parent::__construct();
        $this->cover = "";
        $this->barType = "";
        $this->segment = "";
        $this->size = "";
    }

    public function setCover($cover) {
        $this->cover = "/cover/" . $cover;
    }
    public function getCover() {
        return $this->cover;
    }

    public function setBarType($barType) {
        $this->barType = "/bar-type/" . $barType;
    }
    public function getBarType() {
        return $this->barType;
    }

    public function setSegment($segment) {
        $this->segment = "/segmente/" . $segment;
    }
    public function getSegment() {
        return $this->segment;
    }

    public function setSize($size) {
        $this->size = "/size/" . $size;
    }
    public function getSize() {
        return $this->size;
    }

    public function queryString() {
        $res = "QRcode";
        if($this->cover) {
            $res .= $this->cover;
        }
        if($this->barType) {
            $res .= $this->barType;
        }
        if($this->segment) {
            $res .= $this->segment;
        }
        if($this->size) {
            $res .= $this->size;
        }
        return $res;
    }

    public function resetRule() {
        $this->cover = "";
        $this->barType = "";
        $this->segment = "";
        $this->size = "";
    }
}
