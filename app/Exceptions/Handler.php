<?php

namespace App\Exceptions;

Use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\View\ViewException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {

        if ($request->is('api/*')) {
            return $this->processApiException($exception);
        }
        
        if($request->is('admin/*')){
            return $this->processWebApiException($exception);
        }

        return parent::render($request, $exception);
    }

    protected function processWebApiException($exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException)
        { 
            return response()->view('errors.405');
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) 
        {
            return response()->view('errors.400');
        }

        if($exception instanceof NotFoundHttpException)
        {
            return response()->view('errors.404');
        }

        if($exception instanceof ViewException)
        {
            return response()->view('errors.500');
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->view('errors.401');
        }
    }

    protected function processApiException($exception)
    {
        if ($exception instanceof MethodNotAllowedHttpException)
        { 
            return $this->sendError('Method Not Allowed', ['error'=>'This request is not supported by the resource.'],$code=405);
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) 
        {
           return $this->sendError('Bad Request.', ['error'=>'The request was invalid.'],$code=400);
        }

        if($exception instanceof NotFoundHttpException)
        {
            return $this->sendError('Not Found.', ['error'=>'The requested resource/page not found.'],$code=404);
        }

        if($exception instanceof ViewException)
        {
            return $this->sendError('Internal Server Error', ['error'=>'The request was not completed due to an internal error on the server side.'],$code=500);
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->sendError('Unauthorized.', ['error'=>'Please login again.'],$code=401);
        }
    }

    public function sendError($error, $errorMessages = [], $code)
    {
        $response = [
            'status_code' => $code,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages['error'];
        }


        return response()->json($response, $code);
    }
}
