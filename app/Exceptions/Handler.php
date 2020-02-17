<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedException;


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
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        $debug = config('app.debug');
        $message = "";
        $statusCode = 500;

        if($exception instanceof ModelNotFoundException) {
            $message = "Resource not found";
            $statusCode = 404;
        } elseif($exception instanceof NotFoundHttpException) {
            $message = "Endpoint not found";
            $statusCode = 404;
        } elseif($exception instanceof MethodNotAllowedHttpException) {
            $message = "Method not allowed";
            $statusCode = 405;
        } elseif($exception instanceof ValidationException) {
            $validationErrors = $exception->validator->errors()->getMessages();
            $validationErrors = array_map(function($error) {
                return array_map(function($message) {
                    return $message;
                }, $error);
            }, $validationErrors);
            $message = $validationErrors;
            $statusCode = 405;
        } elseif($exception instanceof QueryException) {
            if($debug) {
                $message = $exception->getMessage();
            } else {
                $message = "Query failed to execute";
            }
            $statusCode = 500;
        }

        $rendered = parent::render($request, $exception);
        $statusCode = $rendered->getStatusCode();
        if(empty($message)) {
            $message = $exception->getMessage();
        }

        $errors = [];

        if($debug) {
            $errors['exception'] = get_class($exception);
            $errors['trace'] = explode("\n", $exception->getTraceAsString());
        }

        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], $statusCode);

    }

    protected function unauthentitaced($request, AuthenticationException $exception) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated',
            'data' => null
        ], 401);
    }
}
