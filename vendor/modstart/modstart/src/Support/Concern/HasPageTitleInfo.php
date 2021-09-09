<?php


namespace ModStart\Support\Concern;


trait HasPageTitleInfo
{
    protected $pageTitle;
    protected $pageKeywords;
    protected $pageDescription;

    public function pageTitle($v = null)
    {
        if (null === $v) {
            return $this->pageTitle;
        }
        $this->pageTitle = $v;
        return $this;
    }

    public function pageKeywords($v = null)
    {
        if (null === $v) {
            return $this->pageKeywords;
        }
        $this->pageKeywords = $v;
        return $this;
    }

    public function pageDescription($v = null)
    {
        if (null === $v) {
            return $this->pageDescription;
        }
        $this->pageDescription = $v;
        return $this;
    }
}