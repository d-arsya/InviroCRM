<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $casts = ['default' => 'boolean'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['text', 'title', 'default'];

    protected static function booted()
    {
        static::deleting(function ($message) {
            if ($message->default) {
                throw new \Exception('Cannot delete a default message.');
            }
        });

        static::saved(function ($message) {
            if ($message->default) {
                static::where('id', '!=', $message->id)
                    ->where('default', true)
                    ->update(['default' => false]);
            }
        });
    }
}
