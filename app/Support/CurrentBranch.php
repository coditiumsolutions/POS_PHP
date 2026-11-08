<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CurrentBranch
{
    public static function resolve(?User $user = null): ?Branch
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return null;
        }

        $name = trim((string) ($user->branch ?? ''));
        if ($name === '') {
            return null;
        }

        return Branch::query()
            ->where('is_active', true)
            ->where('branch_name', $name)
            ->first()
            ?: Branch::query()
                ->where('is_active', true)
                ->where('branch_name', 'like', $name)
                ->first();
    }

    public static function uid(?User $user = null): ?int
    {
        $branch = self::resolve($user);
        return $branch?->uid;
    }

    public static function name(?User $user = null): ?string
    {
        return self::resolve($user)?->branch_name;
    }

    public static function requireUid(?User $user = null): int
    {
        $uid = self::uid($user);
        if (!$uid) {
            $label = trim((string) (($user ?: Auth::user())?->branch ?? ''));
            throw new \RuntimeException(
                $label !== ''
                    ? "Logged-in branch \"{$label}\" was not found in Branches. Fix the user branch name."
                    : 'Logged-in user has no branch assigned.'
            );
        }

        return $uid;
    }
}
