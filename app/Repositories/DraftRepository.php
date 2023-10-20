<?php


namespace App\Repositories;


use App\Models\Draft;

class DraftRepository
{
    public static function allDraftsOnDraftableExceptThis($draft)
    {
        return Draft::where('draftable_type', $draft->draftable_type)
            ->where('draftable_id', $draft->draftable_id)
            ->where('id', '!=', $draft->id);
    }
}
