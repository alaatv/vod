<?php


namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

trait MorphCascadeDelete
{
    protected static function bootMorphCascadeDelete()
    {
        static::deleting(function ($model) {
            $model->softDeleteWithPivots();
        });
    }

    protected function softDeleteWithPivots()
    {
        DB::beginTransaction();
        try {
            $cascadingDeletes = $this->getCascadeArray();
            foreach ($cascadingDeletes as $relationship) {
                $objectRelation = $this->{$relationship}();
                if (!($objectRelation instanceof Relation && method_exists($objectRelation, 'getMorphType'))) {
                    continue;
                }
                $qualifiedForeignKeyName = snake_case(class_basename(static::class.'_id'));
                DB::table($relationship)
                    ->where($qualifiedForeignKeyName, $this->id)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => DB::raw('NOW()')]);

            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();
        }
    }

    /**
     * Fetch the defined cascading soft deletes for this model.
     *
     * @return array
     */
    protected function getCascadeArray()
    {
        return isset($this->cascadeDeletes) ? (array) $this->cascadeDeletes : [];
    }
}
