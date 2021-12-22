<?php

namespace App\Generated\User;

use App\Generated\User\UserFields;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return UserFields::resourceFields($this);
    }
}



