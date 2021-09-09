<?php

namespace Module\Vendor\SDK;

use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use Module\Vendor\Log\Logger;

class HttpRequest
{
    protected $LOG_FILE = 'base_request';
    protected $LOG_PREFIX = 'BaseRequest';
    protected $BASE_URL = '';
    protected $REQUEST_INTERVAL_US = 0;

    protected function info($label, $msg)
    {
        Logger::info($this->LOG_FILE, $this->LOG_PREFIX . '.' . $label, $msg);
    }

    protected function error($label, $msg)
    {
        Logger::error($this->LOG_FILE, $this->LOG_PREFIX . '.' . $label, $msg);
    }

    public function setBaseUrl($baseUrl)
    {
        $this->BASE_URL = $baseUrl;
    }

    protected function post($url, $param, $option = [])
    {
        if ($this->REQUEST_INTERVAL_US > 0) {
            usleep($this->REQUEST_INTERVAL_US);
        }
        $this->info('PostRequest', [$url, $param, $option]);
        $start = microtime(true);
        $ret = CurlUtil::post($this->BASE_URL . $url, $param, $option);
        $this->info('PostResponse', ['elapse ' . intval((microtime(true) - $start) * 1000) . 'ms', $url, $ret]);
        if (!isset($ret['body'])) {
            $this->error('PostError', [$url, $param, $option]);
            return Response::generate(-1, '请求失败(' . $url . ')');
        }
        return Response::generate(0, 'ok', $ret['body']);
    }

    protected function postJSON($url, $param, $option = [])
    {
        if ($this->REQUEST_INTERVAL_US > 0) {
            usleep($this->REQUEST_INTERVAL_US);
        }
        $this->info('PostJSONRequest', [$url, $param, $option]);
        $start = microtime(true);
        $ret = CurlUtil::postJSON($this->BASE_URL . $url, $param, $option);
        $this->info('PostJSONResponse', ['elapse ' . intval((microtime(true) - $start) * 1000) . 'ms', $url, $ret]);
        return $ret;
    }

    protected function get($url, $param, $option = [])
    {
        if ($this->REQUEST_INTERVAL_US > 0) {
            usleep($this->REQUEST_INTERVAL_US);
        }
        $this->info('GetRequest', [$url, $param, $option]);
        $start = microtime(true);
        $ret = CurlUtil::get($this->BASE_URL . $url, $param, $option);
        $this->info('GetRequest', ['elapse ' . intval((microtime(true) - $start) * 1000) . 'ms', $url, $ret]);
        return Response::generate(0, 'ok', $ret['body']);
    }

    protected function getJSON($url, $param, $option = [])
    {
        if ($this->REQUEST_INTERVAL_US > 0) {
            usleep($this->REQUEST_INTERVAL_US);
        }
        $this->info('GetJSONRequest', [$url, $param, $option]);
        $start = microtime(true);
        $ret = CurlUtil::getJSON($this->BASE_URL . $url, $param, $option);
        $this->info('GetJSONResponse', ['elapse ' . intval((microtime(true) - $start) * 1000) . 'ms', $url, $ret]);
        return $ret;
    }

}
