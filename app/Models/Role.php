<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $casts = [
        'name' => 'string',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}