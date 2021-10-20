<?php



namespace Overtrue\EasySms\Exceptions;

use Throwable;


class NoGatewayAvailableException extends Exception
{
    
    public $results = [];

    
    public $exceptions = [];

    
    public function __construct(array $results = [], $code = 0, Throwable $previous = null)
    {
        $this->results = $results;
        $this->exceptions = \array_column($results, 'exception', 'gateway');

        parent::__construct('All the gateways have failed. You can get error details by `$exception->getExceptions()`', $code, $previous);
    }

    
    public function getResults()
    {
        return $this->results;
    }

    
    public function getException($gateway)
    {
        return isset($this->exceptions[$gateway]) ? $this->exceptions[$gateway] : null;
    }

    
    public function getExceptions()
    {
        return $this->exceptions;
    }

    
    public function getLastException()
    {
        return end($this->exceptions);
    }
}
