<?php

namespace App\Exceptions;

use App\CodeReponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BusinessException::class  //添加自定义业务异常
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception){
        if($exception instanceof ValidationException){
            return response()->json([
                'errno'  => CodeReponse::PARAM_ILLEGAL[0],
                'errmsg' => CodeReponse::PARAM_ILLEGAL[1],
            ]);
        }

        //如果异常是我们自定义的业务异常,throw出的异常会走到这里
        if($exception instanceof BusinessException){
            return response()->json([
                'errno'  => $exception->getCode(),
                'errmsg' => $exception->getMessage()
            ]);
        }
        return parent::render($request, $exception);
    }
}
