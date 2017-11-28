<?php

namespace App\Exceptions;

use App\Http\Controllers\Api\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
//use App\Models\DebugLog;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (strpos($request->getRequestUri(), '/api/') === 0) {
            $errors = explode("\n", $e->getTraceAsString());

            return new JsonResponse([
                'status' => 'exception',
                'error' => 1,
                'data' => [
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'trace' => config('api.DEBUG') ? $errors : ['Only available if api.DEBUG = true']
                ]
            ]);
        }

<<<<<<< HEAD
        if($e instanceof NotFoundHttpException){
            return response()->view('errors.notfound', [], 404);
        }elseif($e instanceof MethodNotAllowedHttpException){
            return response()->view('errors.503', [], 503);
        }
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            Auth::logout();
            \Session::flush();
            return redirect()->route('login')->with('errors', __('Your session has expired. Please try logging in again.'));
        }

        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            Auth::logout();
            \Session::flush();
            return redirect()->route('login')->with('errors', __('Your session has expired. Please try logging in again.'));
        }


=======
        if(config('app.debug') === false){
            if($e instanceof NotFoundHttpException){
                return response()->view('errors.notfound', [], 404);
            }elseif($e instanceof MethodNotAllowedHttpException){
                return response()->view('errors.503', [], 503);
            }
            if ($e instanceof ModelNotFoundException) {
                $e = new NotFoundHttpException($e->getMessage(), $e);
            }

            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                Auth::logout();
                \Session::flush();
                return redirect()->route('login')->with('errors', __('Your session has expired. Please try logging in again.'));
            }
        }

>>>>>>> drugorder_release20160328
        return parent::render($request, $e);
    }
}
