<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Generated\User\UserFields;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Generated\User\WithUserCrudFields;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @method static Builder where(string $column, string $operator, string $value)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use WithUserCrudFields, HasApiTokens, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = UserFields::FILLABLE;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = UserFields::HIDDEN;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = UserFields::CASTS;
}
