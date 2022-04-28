<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'calender_id',
        'user_email',
        'start',
        'end',
        'link',
        'attendees',
        'organizer',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'attendees' => 'array',
        'organizer' => 'array',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'user_email');
    }
}
