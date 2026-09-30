<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'title' => $this->title,
            'description' => $this->description,
            'responsible_unit' => $this->responsible_unit,
            'created_on' => $this->created_on?->format('Y-m-d'),
            'url' => $this->url,
            'file_type' => $this->file_type,
            'reading_time_minutes' => $this->reading_time_minutes,
            'importance' => $this->importance?->value,
            'category' => $this->category?->value,
            'is_active' => $this->is_active,
        ];
    }
}
