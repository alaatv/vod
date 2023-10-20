<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Event;
use App\Models\User;
use App\Traits\DateTrait;
use Illuminate\Console\Command;

class Abrisham2StudyEventsReportCommand extends Command
{
    use DateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaa-tv:abrisham-2:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command will get all users that registered in abrisham2 study event and checks that if users had watched their plans contents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $yesterday = now()->setTimezone('Asia/Tehran')->subDay()->toDateString();
        $jalaliYesterday = $this->convertDate($yesterday, 'toJalali');
        $abrisham2Users = User::with([
            'studyEvents' => function ($query) {
                $query->whereHas('event', function ($query) {
                    $query->whereId(Event::ABRISHAM_2);
                });
            }
        ])->whereHas('studyEvents', function ($query) {
            $query->whereHas('event', function ($query) {
                $query->whereId(Event::ABRISHAM_2);
            });
        })->get();
        $todayContents = Content::where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
            ->whereHas('plans', function ($query) use ($yesterday) {
                $query->whereHas('studyplan', function ($query) use ($yesterday) {
                    $query->where('plan_date', $yesterday)->whereHas('event', function ($query) {
                        $query->whereHas('event', function ($query) {
                            $query->whereId(Event::ABRISHAM_2);
                        });
                    });
                });
            })->pluck('id')->toArray();
        foreach ($abrisham2Users as $abrisham2User) {
            $userWatched = $abrisham2User
                ->watchContents()
                ->wherePivotBetween('created_at', [$yesterday, now()->setTimezone('Asia/Tehran')->toDateString()])
                ->get()
                ->pluck('id')
                ->toArray();
            if (count(array_intersect($todayContents, $userWatched)) == count($todayContents)) {
                continue;
            }
            $abrisham2User->studyEventReports()->create([
                'study_event_id' => $abrisham2User->studyEvents->first()->id,
                'report' => [
                    'message' => "محتوای تاریخ $jalaliYesterday در برنامه مطالعاتی شما به طور کامل دیده نشده است",
                    'date' => $yesterday,
                ]
            ]);
        }
        return 0;
    }
}
