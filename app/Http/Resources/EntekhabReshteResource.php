<?php

namespace App\Http\Resources;

use App\Models\EntekhabReshte;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class EntekhabReshteResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'file' => $this->when(isset($this->file), $this->file),
            'comment' => $this->comment,
            'majors' => $this->majors,
            'user' => new User($this->user),
            'shahrha' => ShahrInEntekhabReshte::collection($this->shahrha->sortBy('pivot.order')),
            'university_types' => UniversityTypeResource::collection($this->universityTypes),
            'consultants' => $this->when($this->user->consultants->isNotEmpty(),
                ConsultantResource::collection($this->user->consultants)),
        ];
    }

    public function resolve($request = null): array
    {
        if (!($this->resource instanceof EntekhabReshte)) {
            return [];
        }
        return parent::resolve();
    }
}
