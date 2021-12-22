<?php

namespace App\Generated\User;

use App\Generated\User\UserFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Jdlx\Traits\FieldDescriptor;

/**
 *  @property integer id
 *  @property string name
 *  @property string email
 *  @property Carbon email_verified_at
 *  @property string password
 *  @property string remember_token
 *  @property Carbon created_at
 *  @property Carbon updated_at
 *
 *  @mixin Builder
 */
trait WithUserCrudFields
{
    use FieldDescriptor;

    protected static $fieldSetup = UserFields::ACCESS;
}
