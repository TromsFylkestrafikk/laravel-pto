<?php

namespace TromsFylkestrafikk\Pto\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ret = $this->resource->attributesToArray();
        $child = $this->resource->type === 'bus' ? $this->resource->bus : $this->resource->watercraft;
        $ret = array_merge($ret, $child->attributesToArray());
        if ($this->resource->company) {
            $ret['company_name'] = $this->resource->company->name;
        }
        return $ret;
    }
}
