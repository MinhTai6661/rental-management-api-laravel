<?php
namespace App\Traits;

use App\Enums\Common\Direction;
use App\Enums\Direction as EnumsDirection;
use Illuminate\Validation\Rules\Enum;

trait HasPaginationRules
{
    /**
     * Trả về các quy tắc phân trang và sắp xếp cơ bản.
     */
    protected function paginationRules(): array
    {
        return [
            'page'           => 'nullable|integer|min:1',
            'per_page'       => 'nullable|integer|min:1|max:100',
            'direction' => ['nullable', new Enum(EnumsDirection::class)],
        ];
    }
}