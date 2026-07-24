<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['department_code', 'department_name', 'hod_name'])]
class Department extends Model
{
    use HasFactory;

    public static function shared(): Builder
    {
        return static::query()->withoutGlobalScopes();
    }
}
