<?php

namespace PanicControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PanicControl\Facades\PanicControl as PanicControlFacade;

class PanicControl extends Model
{
    protected $fillable = [
        'service',
        'description',
        'status',
    ];

    use HasFactory;

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (PanicControl $panic) {
            PanicControlFacade::clear();
        });
    }
}
