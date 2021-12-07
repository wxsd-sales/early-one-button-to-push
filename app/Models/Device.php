<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'user_email',
        'place_id',
        'product',
        'mac',
        'serial',
        'primary_sip_url',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'user_email');
    }
}
