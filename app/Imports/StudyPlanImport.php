<?php


namespace App\Imports;

use App\Models\Studyevent;
use App\Models\Studyplan;
use App\Repositories\StudyplanRepo;
use Exception;

abstract class StudyPlanImport
{
    public const PLAN_START_END_TIMES = [
        [],                                             // 0
        ['start' => '07:30:00', 'end' => '08:45:00'],   // 1
        ['start' => '09:00:00', 'end' => '10:15:00'],   // 2
        ['start' => '10:30:00', 'end' => '11:45:00'],   // 3
        ['start' => '12:00:00', 'end' => '13:15:00'],   // 4
        ['start' => '14:45:00', 'end' => '16:00:00'],   // 5
        ['start' => '16:15:00', 'end' => '17:30:00'],   // 6
        ['start' => '17:45:00', 'end' => '19:00:00'],   // 7
        ['start' => '19:45:00', 'end' => '21:00:00'],   // 8
    ];

    public const FARVARDING_DAYS = [
        '2 فروردین' => '2021-03-22',
        '3 فروردین' => '2021-03-23',
        '4 فروردین' => '2021-03-24',
        '5 فروردین' => '2021-03-25',
        '6 فروردین' => '2021-03-26',
        '7 فروردین' => '2021-03-27',
        '8 فروردین' => '2021-03-28',
        '9 فروردین' => '2021-03-29',
        '10 فروردین' => '2021-03-30',
        '11 فروردین' => '2021-03-31',
        '12 فروردین' => '2021-04-01',
        '13 فروردین' => '2021-04-02',
        '14 فروردین' => '2021-04-03',
        '15 فروردین' => '2021-04-04',
        '16 فروردین' => '2021-04-05',
        '17 فروردین' => '2021-04-06',
        '18 فروردین' => '2021-04-07',
        '19 فروردین' => '2021-04-08',
        '20 فروردین' => '2021-04-09',
        '21 فروردین' => '2021-04-10',
        '22 فروردین' => '2021-04-11',
        '23 فروردین' => '2021-04-12',
        '24 فروردین' => '2021-04-13',
        '25 فروردین' => '2021-04-14',
        '26 فروردین' => '2021-04-15',
    ];

    /**
     * @param  array  $row
     * @param  int  $majorId
     * @return Studyplan|bool
     */
    public function import(array $row)
    {
        try {
            $studyPlan = StudyplanRepo::findByDateOrCreate(StudyPlanImport::FARVARDING_DAYS[$row[0]],
                Studyevent::TAFTAN1400_STUDY_EVENT_ID);

            $times = StudyPlanImport::PLAN_START_END_TIMES;

            $plans = [];
            for ($i = 1; $i < count($row); $i++) {
                $rowExists = isset($row[$i]) && !is_array($row[$i]) && !empty(trim($row[$i]));
                $plansCount = count($plans);
                if (!$rowExists && $plansCount <= 0) {
                    break;
                }
                if (!$rowExists) {
                    $plans[$plansCount - 1]['end'] = $times[isset($times[$i]) ? $i : 8]['end'];
                    continue;
                }

                $plans[] = [
                    'studyplane_id' => $studyPlan->id,
                    'major_id' => $this->majorId,
                    'title' => $row[$i],
                    'start' => $times[$i]['start'],
                    'end' => $times[$i]['end'],
                ];
            }

            if (count($plans)) {
                foreach ($plans as $plan) {
                    $studyPlan->plans()->create($plan);
                }
            }

            return $studyPlan;
        } catch (Exception $exception) {
            return false;
        }
    }
}
