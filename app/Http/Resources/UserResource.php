<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'locale' => $this->locale,
            'current_workspace_id' => $this->current_workspace_id,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'workspaces' => WorkspaceResource::collection($this->whenLoaded('workspaces')),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
