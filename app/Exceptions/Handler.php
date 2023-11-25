<?php

namespace App\Exceptions;


use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Log\LoggerInterface;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
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
     * @param  Throwable  $exception
     *
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        dd($exception);
// Kill reporting if this is an "access denied" (code 9) OAuthServerException.
        if ($exception instanceof OAuthServerException && $exception->getCode() == 9) {
            return;
        }

        $exception = $this->mapException($exception);

        if ($this->shouldntReport($exception)) {
            return;
        }

        if (Reflector::isCallable($reportCallable = [
                $exception, 'report'
            ]) && $this->container->call($reportCallable) !== false) {
            return;

        }

        foreach ($this->reportCallbacks as $reportCallback) {
            if ($reportCallback->handles($exception) && $reportCallback($exception) === false) {
                return;

            }
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $exceptionx) {
            throw $exception;
        }

        $userId = request()->user()?->id;

        $inputs = json_encode(request()->all());

        Log::debug(request()->url().' - '.request()->method().' - User '.$userId.' - inputs '.$inputs.' - IP '.request()->ip());
        $logger->error(
            $exception->getMessage(),
            array_merge(
                $this->exceptionContext($exception),
                $this->context(),
                ['exception' => $exception]
            )
        );
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     *
     * @return JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            Auth::logout();

            return redirect()->back();
        }

        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            if (Str::contains($request->path(), 'api/v2')) {
                return myAbort(Response::HTTP_NOT_FOUND, 'Resource not found');
            }
            return response()->json([
                'error' => 'Resource not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            if (Str::contains($request->path(), 'api/v2')) {
                return myAbort(Response::HTTP_NOT_FOUND, 'Not found.');
            }

            return response()->json([
                'error' => 'not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof UnauthorizedException && $request->wantsJson()) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_FORBIDDEN);
        }

        if ($exception instanceof ItemNotFoundException) {
            return response()->json([
                'error' => 'item not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($exception instanceof NotFoundHttpException && preg_match('/[A-Z]/m', $request->path())) {

            return redirect(strtolower($request->fullUrl()));

        }

        if ($exception instanceof AuthenticationException && $request->wantsJson() && Str::contains($request->path(),
                'v2')) {
            return myAbort(Response::HTTP_UNAUTHORIZED, 'Unauthorized.');
        }

        if ($exception instanceof AuthorizationException && $request->wantsJson() && Str::contains($request->path(),
                'v2')) {
            return myAbort($exception->getCode() ?? Response::HTTP_FORBIDDEN,
                $exception->getMessage() ?? 'This action is unauthorized.');
        }

        if ($exception instanceof HttpException && $request->wantsJson()) {
            if (Str::contains($request->path(), 'api/v2')) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => null,
                ], $exception->getStatusCode());
            }
            return response()->json([
                'error' => $exception->getMessage(),
            ], $exception->getStatusCode());

        }

        if (ExceptionInterpreter::isInterpretable($exception)) {
            return ExceptionInterpreter::makeResponse($exception);
        }

        if (!($exception instanceof Exception && !$exception instanceof ValidationException && $request->wantsJson() && Str::contains($request->path(),
                'api/v2'))) {
//            return \response('NOT OK');
            return parent::render($request, $exception);
        }

        //ToDO : temporarry soloution
        if (config('app.env') !== 'development') {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Service unavailable');
        }
        return response()->json([
            'message' => $exception->getMessage(),
            'errors' => [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request  $request
     * @param  AuthenticationException  $exception
     *
     * @return Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        return redirect()->guest('login');
    }
}
