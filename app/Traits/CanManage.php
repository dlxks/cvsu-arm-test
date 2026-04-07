<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CanManage
{
    public function canManage(string $ability): bool
    {
        return (bool) Auth::user()?->can($ability);
    }
}
