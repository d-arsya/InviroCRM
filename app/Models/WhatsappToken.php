<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappToken extends Model
{
    use HasFactory;

    protected $visible = ['id', 'token', 'used', 'active'];

    protected $fillable = ['token'];

    protected $casts = [
        'used' => 'boolean',
        'active' => 'boolean',
    ];

    protected static function booted()
    {
        static::updated(function ($model) {
            $model->active = true;
            $model->saveQuietly();
        });
    }
}
