<?php

namespace PanicControl\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PanicControl\Facades\PanicControl as PanicControlFacade;

/**
 * @property string $name
 * @property string $description
 * @property bool $status
 */
class PanicControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'rules',
    ];

    protected $casts = [
        'status' => 'boolean',
        'rules' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('panic-control.database.table'));
    }

    protected static function booted(): void
    {
        static::saved(function (PanicControl $panic) {
            PanicControlFacade::clear();
        });
    }
}
