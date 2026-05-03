<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{

    public static function success($data = null, $message = 'Success', $status = 200)
    {
        [$data, $meta] = self::formatPagination($data);

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $status,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    private static function formatPagination($data)
    {
        if (
            $data instanceof \Illuminate\Http\Resources\Json\ResourceCollection &&
            $data->resource instanceof LengthAwarePaginator
        ) {
            return self::extractPaginatorData($data->resource, $data);
        }


        if (!$data instanceof LengthAwarePaginator) {
            return [$data, null];
        }

        return [
            $data->items(),
            [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'has_next' => $data->hasMorePages(),
                'has_prev' => $data->currentPage() > 1,
            ]
        ];
    }

    private static function extractPaginatorData($paginator, $actualData = null)
    {
        return [
            $actualData ?? $paginator->items(),
            [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'has_next'     => $paginator->hasMorePages(),
                'has_prev'     => $paginator->currentPage() > 1,
            ]
        ];
    }

    public static function error($message = 'Error', $status = 500)
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,

        ], $status);
    }
}
