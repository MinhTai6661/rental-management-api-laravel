<?php

namespace App\Traits;

trait ApiBaseResource
{
    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'pagination' => [
                'total' => $paginated['total'],
                'per_page' => $paginated['per_page'],
                'current_page' => $paginated['current_page'],
                'last_page' => $paginated['last_page'],
                'has_more' => $paginated['current_page'] < $paginated['last_page'],
                'has_previous' => $paginated['current_page'] > 1,
            ],
        ];
    }

    public function toResponse($request)
    {
        $response = parent::toResponse($request);

        $data = $response->getData(true);

        $data['status'] = $this->additional['status'] ?? 200;
        $data['success'] = $this->additional['success'] ?? true;
        $data['message'] = $this->additional['message'] ?? 'OK';

        return $response->setData($data);
    }
}
