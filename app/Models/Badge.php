<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'required_achievements'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(UserBadge::class)
            ->withTimestamps();
    }
}
