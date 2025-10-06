<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        if (is_string($data['products'])) {

            $decoded = json_decode($data['products'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['products'] = $decoded;
            }
        }

        return $data;
    }
}
