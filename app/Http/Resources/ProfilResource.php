<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'picture' => $this->picture,
        ];

        if ($request->user()) {
            $data['status'] = $this->status;
            $data['administrator'] = new AdministratorResource($this->whenLoaded('administrator'));

        }

        return $data;
    }
}
