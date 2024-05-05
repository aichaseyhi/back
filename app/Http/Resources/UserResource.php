<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'status' => $this->status,
            'image' => $this->image,
            'street' => $this->street,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'CIN' => $this->CIN,
            'TAXNumber' => $this->TAXNumber,
            'companyName' => $this->companyName,
            'companyUnderConstruction' => $this->companyUnderConstruction,


        ];
    }
}
