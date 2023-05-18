<?php

namespace PanicControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanicControl extends Model
{
    protected $fillable = [
        'service',
        'description',
        'status',
    ];

    use HasFactory;
}
