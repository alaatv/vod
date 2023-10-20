<?php

namespace App\Console\Commands;

use App\Models\Major;
use App\Models\Plan;
use App\Models\Studyevent;
use Illuminate\Console\Command;

class CopyStudyPlanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:copyStudyPlan {study_event_id} {source_major} {destination_majors}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy plans of an study event from one major to other majors';

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
     * @return mixed
     */
    public function handle()
    {
        $validMajorIds = [Major::RIYAZI, Major::TAJROBI];

        $studyEventId = $this->argument('study_event_id');
        $sourceMajorId = $this->argument('source_major');
        $destinationMajorIds = explode(',', $this->argument('destination_majors'));

        if (!in_array($sourceMajorId, $validMajorIds)) {
            $this->error('Major id is invalid. Majors must be one of these values: '.implode(',', $validMajorIds).'!');
            return 0;
        }

        if (!searchArrayToAnotherArray($destinationMajorIds, $validMajorIds)) {
            $this->error('Major id is invalid. Majors must be one of these values: '.implode(',', $validMajorIds).'!');
            return 0;
        }

        // Check study event is exists.
        /** @var Studyevent $studyEvent */
        if (!($studyEvent = Studyevent::find($studyEventId))) {
            $this->error('The study event id is invalid!');
            return 0;
        }


        $studyPlans = $studyEvent->studyplans;

        $count = $studyPlans->count();
        if (!$this->confirm("$count dates found, Do you wish to continue?")) {
            $this->warn('Aborted');
            return 0;
        }
        $progressBar = $this->output->createProgressBar($count);
        foreach ($studyPlans as $studyPlan) {
            $plans = $studyPlan->plans->where('major_id', $sourceMajorId);
            foreach ($plans as $plan) {
                /** @var Plan $plan */
                foreach ($destinationMajorIds as $destinationMajorId) {
                    Plan::create([
                        'studyplan_id' => $studyPlan->id,
                        'major_id' => $destinationMajorId,
                        'title' => $plan->title,
                        'section_name' => $plan->section_name,
                        'description' => $plan->description,
                        'long_description' => $plan->long_description,
                        'link' => $plan->link,
                        'start' => $plan->start,
                        'end' => $plan->end,
                        'background_color' => $plan->background_color,
                        'border_color' => $plan->border_color,
                        'text_color' => $plan->text_color,
                        'voice' => $plan->voice,
                        'video' => $plan->video,
                    ]);
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->info('Done!');
        return 0;
    }
}
