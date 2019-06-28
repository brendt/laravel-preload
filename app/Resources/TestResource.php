<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'test' => 'test',
        ];
    }
}
