<?php

namespace App\Classes;

use DomainException;
use UnexpectedValueException;

class JWT
{
    public static function decode($jwt, $key = null, $verify = true)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headB64, $payloadB64, $cryptoB64) = $tks;
        if (null === ($header = JWT::jsonDecode(JWT::urlSafeB64Decode($headB64)))
        ) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        if (null === $payload = JWT::jsonDecode(JWT::urlSafeB64Decode($payloadB64))
        ) {
            throw new UnexpectedValueException('Invalid segment encoding');
        }
        $sig = JWT::urlSafeB64Decode($cryptoB64);
        if ($verify) {
            if (empty($header->alg)) {
                throw new DomainException('Empty algorithm');
            }
            if ($sig != JWT::sign("$headB64.$payloadB64", $key, $header->alg)) {
                throw new UnexpectedValueException('Signature verification failed');
            }
        }
        return $payload;
    }

    public static function jsonDecode($input)
    {
        $obj = json_decode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::handleJsonError($errno);
        } else {
            if ($obj === null && $input !== 'null') {
                throw new DomainException('Null result with non-null input');
            }
        }
        return $obj;
    }

    private static function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
        );
        throw new DomainException($messages[$errno] ?? 'Unknown JSON error: '.$errno
        );
    }

    public static function urlSafeB64Decode($input): bool|string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padLen = 4 - $remainder;
            $input .= str_repeat('=', $padLen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function sign($msg, $key, $method = 'HS256'): string
    {
        $methods = array(
            'HS256' => 'sha256',
            'HS384' => 'sha384',
            'HS512' => 'sha512',
        );
        if (empty($methods[$method])) {
            throw new DomainException('Algorithm not supported');
        }
        return hash_hmac($methods[$method], $msg, $key, true);
    }

    public static function encode($payload, $key, $algo = 'HS256'): string
    {
        $header = array('typ' => 'JWT', 'alg' => $algo);
        $segments = array();
        $segments[] = JWT::urlSafeB64Encode(JWT::jsonEncode($header));
        $segments[] = JWT::urlSafeB64Encode(JWT::jsonEncode($payload));
        $signing_input = implode('.', $segments);
        $signature = JWT::sign($signing_input, $key, $algo);
        $segments[] = JWT::urlSafeB64Encode($signature);
        return implode('.', $segments);
    }

    public static function urlSafeB64Encode($input): array|string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    public static function jsonEncode($input): bool|string
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            JWT::handleJsonError($errno);
        } else {
            if ($json === 'null' && $input !== null) {
                throw new DomainException('Null result with non-null input');
            }
        }
        return $json;
    }
}
