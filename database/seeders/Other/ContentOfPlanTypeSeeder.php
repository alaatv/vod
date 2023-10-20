<?php

namespace Database\Seeders\Other;

use App\Models\ContentOfPlanType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContentOfPlanTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        self::addTest();
    }

    public static function addTest()
    {
        $types = [
            [
                'id' => 1,
                'title' => 'moshavere_voice',
                'display_name' => 'ویس مشاوره',
            ],
            [
                'id' => 2,
                'title' => 'moshavere_video',
                'display_name' => 'فیلم مشاوره'
            ],
            [
                'id' => 3,
                'title' => 'moshavere_text',
                'display_name' => 'متن مشاوره'
            ],
            [
                'id' => ContentOfPlanType::TYPE_LESSON_VIDEO,
                'title' => 'lesson_video',
                'display_name' => 'فیلم درس'
            ],
            [
                'id' => 5,
                'title' => 'tests',
                'display_name' => 'تستها'
            ]
        ];

        $types = array_map(function ($item) {
            return array_merge($item, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }, $types);

        ContentOfPlanType::insert($types);

    }
}
