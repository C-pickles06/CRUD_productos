<?php

namespace Core;

/**
 * Generación y verificación de tokens CSRF.
 */
class Csrf
{
    public const FIELD = '_token';
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function verify(?string $token): bool
    {
        $expected = Session::get(self::SESSION_KEY);
        if (!is_string($expected) || !is_string($token)) {
            return false;
        }
        return hash_equals($expected, $token);
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        $name = self::FIELD;
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$token}\">";
    }
}
