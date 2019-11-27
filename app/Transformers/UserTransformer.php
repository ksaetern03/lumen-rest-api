<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\URL;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id'                    => $user->id,
            'username'              => $user->username,
            'is_active'             => $user->is_active,
            'created_by'            => $user->created_by,
            'updated_by'            => $user->updated_by,
            'created_at'            => (string) $user->created_at,
            'updated_at'            => (string) $user->updated_at
        ];
    }
}