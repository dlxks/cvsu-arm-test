<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[Fillable(['name', 'guard_name'])]
class Permission extends SpatiePermission
{
    use SoftDeletes;
}
