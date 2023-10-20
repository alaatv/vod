<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ExceptionInterpreter
{
    public const DUPLICATE_ENTRY = '1062';

    private const  CODES = [
        '23000' => [
            '1062 Duplicate entry' => [
                'message' => 'Duplicate entry',
                'code' => Response::HTTP_BAD_REQUEST
            ]
        ],
        '0' => [
            'Unauthenticated' => [
                'message' => 'Unauthenticated',
                'code' => Response::HTTP_UNAUTHORIZED
            ],
        ],
        '42S22' => [
            'Column not found' => [
                'message' => 'Wrong Field',
                'code' => Response::HTTP_BAD_REQUEST,
            ]
        ],
    ];

    public static function isInterpretable($exception): bool
    {
        return $exception instanceof QueryException;
//            $exception instanceof AuthenticationException;
    }

    public static function makeResponse($exception)
    {
        $code = $exception->getCode();
        $message = $exception->getMessage();
        [$code, $message] = self::interpret($code, $message);

        return myAbort($code, $message);
    }

    private static function interpret($code, $message)
    {
        foreach (Arr::get(self::CODES, $code, []) as $key => $value) {
            if (Str::contains($message, $key)) {
                return [$value['code'], $value['message']];
            }
        }

        return [Response::HTTP_SERVICE_UNAVAILABLE, 'Service Not Available'];
    }
}
