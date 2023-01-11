<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'assign',
        'date'
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'activities_users',
            'activity_id',
            'user_id',
        );
    }
}
