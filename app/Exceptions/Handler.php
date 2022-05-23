<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Exception\ExceptionReportHandleTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ExceptionReportHandleTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        BizException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $this->errorReportCheck($exception);

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }
        
        $t = $this->getExceptionResponse($e);
        if (null !== $t) {
            return $t;
        }

        return parent::render($request, $e);
    }

    /**
     * Convert the given exception into a Response instance.
     *
     * @param \Exception $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        $t = $this->getExceptionResponse($e);
        if (null !== $t) {
            return $t;
        }
        if (env('APP_DEBUG', true)) {
            return parent::convertExceptionToResponse($e);
        }
        return response()->view('errors.500', ['exception' => $e], 500);
    }
}
