<?php

namespace App\Http\Resources\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiPaginationResponse extends JsonResource
{
    private $pagination;

    public function __construct($resource)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'per_page' => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'total_pages' => $resource->lastPage(),
        ];

        $resource = $resource->getCollection();

        parent::__construct($resource);
    }

    public function toArray($request = null)
    {
        return [
            'data' => parent::toArray($request),
            'pagination' => $this->pagination,
        ];
    }
}
