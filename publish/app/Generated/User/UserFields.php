<?php

namespace App\Generated\User;

use Faker\Generator;
use Illuminate\Support\Facades\Hash;;
use Jdlx\Traits\FieldDescriptor;

class UserFields
{
    use FieldDescriptor;

    protected static $fieldSetup = self::ACCESS;

    public const ACCESS = [
        'id' => ['readOnly', 'sortable', 'filterable'],
        'name' => ['editable', 'sortable', 'filterable'],
        'email' => ['editable', 'sortable', 'filterable'],
        'email_verified_at' => ['editable', 'sortable', 'filterable'],
        'password' => ['editable', 'writeOnly', 'sortable', 'filterable'],
        'remember_token' => ['editable', 'sortable', 'filterable'],
        'created_at' => ['readOnly', 'sortable', 'filterable'],
        'updated_at' => ['readOnly', 'sortable', 'filterable'],
    ];

    public const CASTS = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const FILLABLE = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
    ];

    public const HIDDEN = [
    ];

    public static function factory(Generator $faker)
    {
        return [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'email_verified_at' => $faker->dateTimeBetween('-1 years', '-1 hour'),
            'password' => Hash::make("password"),
            'remember_token' =>  $faker->words(1, true),
            'created_at' => $faker->dateTimeBetween('-1 years', '-1 hour'),
            'updated_at' => $faker->dateTimeBetween('-1 years', '-1 hour'),
        ];
    }

    public static function resourceFields($model): array
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'email_verified_at' => is_null($model->email_verified_at) ? null : $model->email_verified_at->toRfc3339String(),
            'remember_token' => $model->remember_token,
            'created_at' => is_null($model->created_at) ? null : $model->created_at->toRfc3339String(),
            'updated_at' => is_null($model->updated_at) ? null : $model->updated_at->toRfc3339String(),
        ];
    }

    public static function validation($for)
    {
        return [];
    }

}
