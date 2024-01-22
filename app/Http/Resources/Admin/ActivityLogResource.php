<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\User as UserResource;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof Activity)) {
            return [];
        }

        $user = User::find($this->causer_id);

        return [
            'id' => $this->id,
            'log_name' => $this->when(isset($this->log_name), $this->log_name),
            'description' => $this->when(isset($this->description), $this->description),
            'subject_type' => $this->when(isset($this->subject_type), $this->subject_type),
            'subject_model_name' => $this->when(isset($this->subject_type), function () {
                $subjectType = str_replace('App\\\\', '', $this->subject_type);

                return str_replace('App\\', '', $subjectType);
            }),
            'subject_id' => $this->when(isset($this->subject_id), $this->subject_id),
            'subject_edit_link' => $this->when(isset($this->subject_type) && isset($this->subject_id), function () {
                $subject = $this->subject_type::find($this->subject_id);

                return $subject->edit_link ?? null;
            }),
            'causer_type' => $this->when(isset($this->causer_type), $this->causer_type),
            'causer_id' => $this->when(isset($this->causer_id), $this->causer_id),
            'causer' => $this->when(isset($this->causer_id), function () use ($user) {
                return $user ? new UserResource($user) : null;
            }),
            'causer_edit_link' => $this->when(isset($this->causer_id), function () use ($user) {
                return $user ? route('user.edit', $this->causer_id) : null;
            }),
            'properties' => $this->when(isset($this->properties), $this->properties),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}
