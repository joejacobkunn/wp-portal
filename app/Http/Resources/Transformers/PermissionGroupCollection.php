<?php

namespace App\Http\Resources\Transformers;

use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionGroupCollection extends ResourceCollection
{
    protected $groupAggregated;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request = null)
    {
        return $this->collection->toArray();
    }

    /**
     * Aggregate permissions according to group
     *
     * @return \App\Http\Resources\Transformers\PermissionGroupCollection
     */
    public function aggregateGroup()
    {
        if (!$this->groupAggregated) {
            $data = [];
            foreach ($this->collection as $permission) {
                $groupSlug = Str::slug($permission->group_name);
                if (!isset($data[$groupSlug])) {
                    $data[$groupSlug] = [
                        "group" => $groupSlug,
                        "group_name" => $permission->group_name,
                        "permissions"=> [],
                    ];
                }

                $data[$groupSlug]["permissions"][] = $permission;     
            };

            $this->collection = collect($data);
            $this->groupAggregated = true;
        }
        
        return $this;
    }
}
