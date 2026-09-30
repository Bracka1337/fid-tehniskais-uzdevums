<?php

namespace App\Models;

use App\Enums\AccessCategory;
use App\Enums\ImportanceLevel;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'external_id',
        'title',
        'description',
        'responsible_unit',
        'created_on',
        'url',
        'file_type',
        'reading_time_minutes',
        'importance',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'created_on' => 'date:Y-m-d',
            'reading_time_minutes' => 'integer',
            'importance' => ImportanceLevel::class,
            'category' => AccessCategory::class,
            'is_active' => 'boolean',
        ];
    }
}
