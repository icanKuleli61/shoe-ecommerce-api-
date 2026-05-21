<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'rating' => $this->rating,

            'comment' => $this->comment,

            'user' => $this->formatUserName(),

            'created_at' =>
                $this->created_at
                    ->format('d.m.Y'),
        ];
    }

    private function formatUserName()
    {
        if (
            !$this->user?->first_name &&
            !$this->user?->last_name
        ) {

            return 'Silinmiş kullanıcı';
        }

        $firstName =
            $this->user->first_name ?? '';

        $lastName =
            $this->user->last_name ?? '';

        return

            $firstName . ' ' .

            mb_substr(
                $lastName,
                0,
                1
            ) . '.';
    }
}