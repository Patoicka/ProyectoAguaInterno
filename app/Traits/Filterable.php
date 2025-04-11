<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait Filterable
{
    public function getFilters(array $filters): array
    {
        return [
            'direction' => $filters['direction'] ?? 'desc',
            'order' => $filters['order'] ?? 'created_at',
            'rows' => $filters['rows'] ?? 5,
            'search' => $filters['search'] ?? null,
        ];
    }
}
