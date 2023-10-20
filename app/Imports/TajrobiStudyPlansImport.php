<?php

namespace App\Imports;

use App\Models\Major;
use App\Models\Studyplan;
use App\Traits\StudyPlanImportTrait;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class TajrobiStudyPlansImport extends StudyPlanImport implements ToModel
{
    protected $majorId;

    /**
     * RiyaziStudyPlansImport constructor.
     */
    public function __construct()
    {
        $this->majorId = Major::TAJROBI;
    }

    /**
     * @param  array  $row
     * @return Studyplan|bool|Model|Model[]|null
     */
    public function model(array $row)
    {
        if (!isset($row) || !count($row)) {
            return false;
        }

        return $this->import($row);
    }
}
