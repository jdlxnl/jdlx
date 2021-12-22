<?php

namespace App\Models;

use App\Generated\User\UserFields;
use App\Generated\User\WithUserCrudFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

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
