<?php

namespace App\Http\Resources;

class ActionResource extends AlaaJsonResource
{
    public const CREATE = 'create_action';

    public const EDIT = 'edit_action';

    public const DELETE = 'delete_action';

    public const SHOW = 'show_action';

    public function toArray($request)
    {
        $actions = [
            self::CREATE => null,
            self::EDIT => null,
            self::DELETE => null,
            self::SHOW => null,
        ];

        if (!is_array($this->resource)) {
            return $actions;
        }

        foreach ($actions as $action => $value) {
            $actions[$action] = $this[$action] ?? null;
        }

        return $actions;
    }
}
