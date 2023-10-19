<?php

namespace App\Classes;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class VerificationCode
{
    public const RESET_PASSWORD = 'resetPassword';
    public const RESEND = 'resend';
    public const RESEND_GUST = 'resendGuest';

    private const ACTIONS = [
        self::RESEND => 'resend_code',
        self::RESEND_GUST => 'resend_gust_code',
        self::RESET_PASSWORD => 'reset_password_code',
    ];

    public const TTLS = [
        self::RESEND => 60 * 5,
        self::RESEND_GUST => 60 * 5,
        self::RESET_PASSWORD => 60 * 5,
    ];
    private const OVERTIME = 10;

    public static function getCode(string $action, string $identifier): string
    {
        $code = self::makeCode();
        $key = self::getCacheKey($action, $identifier);
        $ttl = self::getTTL($action) + self::OVERTIME;
        self::cacheCode($key, $code, $ttl);

        return $code;
    }

    private static function makeCode(): string
    {
        $code = rand(100000, 999999);
        return "$code";
    }

    private static function getCacheKey(string $action, string $identifier): string
    {
        if (!in_array($action, array_keys(self::ACTIONS))) {
            throw new InvalidArgumentException('validation.action must be in VerificationCode ACTIONS list');
        }

        return self::ACTIONS[$action].'_'.$identifier;
    }

    private static function getTTL(string $action)
    {
        if (!in_array($action, array_keys(self::TTLS))) {
            throw new InvalidArgumentException('validation.action must be in VerificationCode ACTIONS list');
        }

        return self::TTLS[$action];
    }

    private static function cacheCode(string $key, string $code, int $ttl): bool
    {
        return Cache::put($key, [$code, now()], $ttl);
    }

    public static function hasCode(string $action, string $identifier): bool
    {
        $key = self::getCacheKey($action, $identifier);
        if (Cache::has($key)) {
            return true;
        }
        return false;
    }

    public static function checkCode(string $action, string $identifier, string $code): bool
    {
        $key = self::getCacheKey($action, $identifier);
        $cachedCode = Cache::get($key);
        return $code == Arr::get($cachedCode, 0);
    }

    public static function getRemainingTime(string $action, string $identifier): int
    {
        $key = self::getCacheKey($action, $identifier);

        if (Cache::has($key)) {
            $spent = now()->timestamp - self::getCachedTime($action, $identifier)->timestamp;
            $remain = self::TTLS[$action] - $spent;
            return ($remain >= 0) ? $remain : 0;
        }

        return 0;
    }

    private static function getCachedTime(string $action, string $identifier)
    {
        $key = self::getCacheKey($action, $identifier);
        $cachedCode = Cache::get($key);
        return Arr::get($cachedCode, 1);
    }

    public static function deleteCacheCode(string $action, string $identifier): bool
    {
        $key = self::getCacheKey($action, $identifier);
        return Cache::delete($key);
    }

    public static function getCachedCode(string $action, string $identifier): ?string
    {
        $key = self::getCacheKey($action, $identifier);
        $cachedCode = Cache::get($key);
        return Arr::get($cachedCode, 0);
    }
}

