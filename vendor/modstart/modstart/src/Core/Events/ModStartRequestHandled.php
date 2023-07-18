<?php


namespace ModStart\Core\Events;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ModStartRequestHandled
{
    public $url;
    public $method;
    public $time;
    public $statusCode;
    /** @var Response */
    public $response;

    public function isHtml()
    {
        try {
            if ($this->response->headers) {
                return Str::contains($this->response->headers->get('content-type'), 'text/html');
            }
        } catch (\Exception $e) {
            Log::info('MS.ModStartRequestHandled.Unknown - ' . get_class($this->response));
        }
        return false;
    }

    public function isGet()
    {
        return 'GET' == $this->method;
    }

}
