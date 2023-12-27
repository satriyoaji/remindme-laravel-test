<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Reminder extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'remind_at', 'event_at', 'user_id'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function eventAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Carbon::parse($value)->timestamp
        );
    }

    protected function remindAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => Carbon::parse($value)->timestamp
        );
    }
}
