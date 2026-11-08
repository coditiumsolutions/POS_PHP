<?php

namespace App\Services;

/**
 * Verifies ASP.NET Core Identity password hashes (V2 / V3).
 * Format: https://github.com/dotnet/aspnetcore/blob/main/src/Identity/Extensions.Core/src/PasswordHasher.cs
 */
class AspNetIdentityPasswordHasher
{
    public static function isAspNetHash(?string $hashedValue): bool
    {
        return is_string($hashedValue) && str_starts_with($hashedValue, 'AQAAAA');
    }

    public static function isLegacySha256Hash(?string $hashedValue): bool
    {
        return is_string($hashedValue) && (bool) preg_match('/^[0-9A-Fa-f]{64}$/', trim($hashedValue));
    }

    public static function verifyLegacySha256(string $password, string $hashedValue): bool
    {
        $expected = strtoupper(hash('sha256', $password));

        return hash_equals($expected, strtoupper(trim($hashedValue)));
    }

    public static function verify(string $password, string $hashedValue): bool
    {
        $decoded = base64_decode($hashedValue, true);
        if ($decoded === false || $decoded === '') {
            return false;
        }

        return match (ord($decoded[0])) {
            0x00 => self::verifyV2($decoded, $password),
            0x01 => self::verifyV3($decoded, $password),
            default => false,
        };
    }

    private static function verifyV2(string $hashedPassword, string $password): bool
    {
        // { 0x00, salt(16), subkey(32) } — PBKDF2-HMAC-SHA1, 1000 iterations
        if (strlen($hashedPassword) !== 49) {
            return false;
        }

        $salt = substr($hashedPassword, 1, 16);
        $expected = substr($hashedPassword, 17, 32);
        $actual = hash_pbkdf2('sha1', $password, $salt, 1000, 32, true);

        return hash_equals($expected, $actual);
    }

    private static function verifyV3(string $hashedPassword, string $password): bool
    {
        // { 0x01, prf(UInt32 BE), iter(UInt32 BE), saltLen(UInt32 BE), salt, subkey }
        if (strlen($hashedPassword) < 13) {
            return false;
        }

        $prf = unpack('N', substr($hashedPassword, 1, 4))[1];
        $iter = unpack('N', substr($hashedPassword, 5, 4))[1];
        $saltLen = unpack('N', substr($hashedPassword, 9, 4))[1];

        if ($iter < 1 || $saltLen < 1 || strlen($hashedPassword) < 13 + $saltLen) {
            return false;
        }

        $salt = substr($hashedPassword, 13, $saltLen);
        $expected = substr($hashedPassword, 13 + $saltLen);
        if ($expected === '') {
            return false;
        }

        $algo = match ($prf) {
            0 => 'sha1',
            1 => 'sha256',
            2 => 'sha512',
            default => null,
        };

        if ($algo === null) {
            return false;
        }

        $actual = hash_pbkdf2($algo, $password, $salt, $iter, strlen($expected), true);

        return hash_equals($expected, $actual);
    }
}
