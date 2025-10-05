<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    protected $casts = ['message_value' => 'json', 'products' => 'json'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($customer) {
            $daysToAdd = (int) DB::table('config')->whereKey('days_after')->first()->value;
            $customer->send = Carbon::parse($customer->date)->addDays($daysToAdd);
            $def = Message::whereDefault(true)->first();
            $customer->message_id = $def->id;
            $customer->message_value = ['title' => $def->title, 'text' => $def->text];
            $date = Carbon::parse($customer->date);
            $prefix = 'ORD-'.$date->format('ymd'); // e.g. ORD-251005

            // Count how many orders already exist for that day
            $count = static::whereDate('date', $date->toDateString())->count() + 1;

            // Pad the count to 3 digits
            $suffix = str_pad($count, 3, '0', STR_PAD_LEFT);

            $customer->order_id = $prefix.'-'.$suffix;
        });
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id', 'id');
    }
}
