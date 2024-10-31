<?php

namespace App\Http\Resources;

use App\Models\User;
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

        /** @var User $this */

        return [
            'user' => $this->load('githubAccount'),
            'access_token' => $this->additional['token'],
            'token_type' => 'Bearer'
        ];
    }
}
