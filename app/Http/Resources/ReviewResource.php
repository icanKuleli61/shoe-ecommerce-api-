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
        if (!$this->user?->name) {

            return 'Silinmiş kullanıcı';
        }

        $parts = explode(
            ' ',
            trim($this->user->name)
        );

        if (count($parts) === 1) {

            return $parts[0];
        }

        return
            $parts[0] . ' ' .

            mb_substr(
                $parts[1],
                0,
                1
            ) . '.';
    }
}