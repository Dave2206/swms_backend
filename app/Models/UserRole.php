<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $casts = [
        'id',
        'user_role',
        'access_level' => 'array',  // Cast to array from JSON string
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }
}
