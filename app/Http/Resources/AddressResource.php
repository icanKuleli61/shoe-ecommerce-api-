<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [

            'id' => $this->id,

            'full_name' => $this->full_name,

            'phone' => $this->phone,

            'city' => [
                'id' => $this->city_id,
                'name' => $this->city->name
            ],

            'district' => [
                'id' => $this->district_id,
                'name' => $this->district->name
            ],

            'neighborhood' => [
                'id' => $this->neighborhood_id,
                'name' => $this->neighborhood->name
            ],

            'address' => $this->address,

            'title' => $this->title,

            'is_default' => $this->is_default,
        ];
    }
}
