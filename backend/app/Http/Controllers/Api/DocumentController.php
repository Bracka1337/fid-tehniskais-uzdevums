<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentIndexRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function index(DocumentIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();

        $query = Document::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('responsible_unit', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['importance'])) {
            $query->where('importance', $filters['importance']);
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where('is_active', $filters['is_active']);
        }

        $sort = $filters['sort'] ?? 'created_on';
        $direction = $filters['direction'] ?? 'desc';

        $documents = $query
            ->orderBy($sort, $direction)
            ->orderBy('id')
            ->get();

        return DocumentResource::collection($documents)->additional([
            'meta' => [
                'total' => $documents->count(),
            ],
        ]);
    }
}
