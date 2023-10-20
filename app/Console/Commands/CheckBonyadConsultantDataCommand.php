<?php

namespace App\Console\Commands;

use App\Models\BonyadEhsanConsultant;
use App\Models\User;
use App\Notifications\BonyadConsultantCheckerNotification;
use App\Services\BonyadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckBonyadConsultantDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:bonyad:consultant:check {manager : The ID of the manager} {--user= : filter users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check consultant numbers in bonyad';

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
        $managerId = $this->argument('manager');
        $bonyadUser = User::find($managerId);
        $bonyadData = User::whereHas('roles', function ($query) {
            return $query->whereIn('id', [130, 131, 124]);
        })->whereHas('parents', function ($query) use ($managerId) {
            return $query->where('id', $managerId);
        })->with('consultant')->get();
        $bonyadData->push($bonyadUser);

        Log::channel('bonyadConsultantCheck')->info('running consultant check for bonyad');
        $flag = false;

        //moshavers
        $moshavers = BonyadService::users($managerId, 'show-moshavers', false);
        foreach ($moshavers as $moshaver) {
            $consultant = $moshaver->consultant->student_register_number;
            $students = User::whereHas('roles', function ($q) {
                return $q->where('id', 123);
            })->where('inserted_by', $moshaver->id)->get();
            $studentsCount = $students->count();
            if ($consultant != $studentsCount) {
                Log::channel('bonyadConsultantCheck')->error("id={$moshaver->id},studentCount={$studentsCount},registerNumber={$consultant} has wrong data");
                $this->error("id={$moshaver->id},studentCount={$studentsCount},registerNumber={$consultant} has wrong data");
                $flag = true;
            }
        }

        //subnetworks
        $subnetworks = BonyadService::users($managerId, 'show-subnetworks', false);
        foreach ($subnetworks as $subnetwork) {
            $consultant = $subnetwork->consultant->student_register_number;
            $students = User::whereHas('roles', function ($q) {
                return $q->where('id', 123);
            })->where('inserted_by', $subnetwork->id)->get();
            $studentsCount = $students->count();
            $givenRegister = 0;
            $moshavers = User::whereHas('roles', function ($q) {
                return $q->where('id', 124);
            })->where('inserted_by', $subnetwork->id)->get();
            foreach ($moshavers as $moshaver) {
                $givenRegister += $moshaver->consultant->student_register_limit;
            }
            if ($consultant != $studentsCount + $givenRegister) {
                Log::channel('bonyadConsultantCheck')->error("id={$subnetwork->id},studentCount={$studentsCount},givenRegisterM={$givenRegister},registerNumber={$consultant} has wrong data");
                $this->error("id={$subnetwork->id},studentCount={$studentsCount},givenRegisterM={$givenRegister},registerNumber={$consultant} has wrong data");
                $flag = true;
            }
        }

        //networks
        $networks = BonyadService::users($managerId, 'show-networks', false);
        foreach ($networks as $network) {
            $consultant = $network->consultant->student_register_number;
            $students = User::whereHas('roles', function ($q) {
                return $q->where('id', 123);
            })->where('inserted_by', $network->id)->get();
            $studentsCount = $students->count();
            $givenRegisterMoshaver = 0;
            $moshavers = User::whereHas('roles', function ($q) {
                return $q->where('id', 124);
            })->where('inserted_by', $network->id)->get();
            foreach ($moshavers as $moshaver) {
                $givenRegisterMoshaver += $moshaver->consultant->student_register_limit;
            }

            $givenRegisterSubNetwork = 0;
            $subnetworks = User::whereHas('roles', function ($q) {
                return $q->where('id', 131);
            })->where('inserted_by', $network->id)->get();
            foreach ($subnetworks as $subnetwork) {
                $givenRegisterSubNetwork += $subnetwork->consultant->student_register_limit;
            }
            if ($consultant != $studentsCount + $givenRegisterMoshaver + $givenRegisterSubNetwork) {
                Log::channel('bonyadConsultantCheck')->error("id={$network->id},studentCount={$studentsCount},givenRegisterM={$givenRegisterMoshaver},givenRegisterS={$givenRegisterSubNetwork},registerNumber={$consultant} has wrong data");
                $this->error("id={$network->id},studentCount={$studentsCount},givenRegisterM={$givenRegisterMoshaver},givenRegisterS={$givenRegisterSubNetwork},registerNumber={$consultant} has wrong data");
                $flag = true;
            }
        }


        //bonyad
        $consultant = $bonyadUser->consultant->student_register_number;
        $students = User::whereHas('roles', function ($q) {
            return $q->where('id', 123);
        })->where('inserted_by', $managerId)->get();
        $studentsCount = $students->count();
        $givenRegisterMoshaver = 0;
        $moshavers = User::whereHas('roles', function ($q) {
            return $q->where('id', 124);
        })->where('inserted_by', $managerId)->get();
        foreach ($moshavers as $moshaver) {
            $givenRegisterMoshaver += $moshaver->consultant->student_register_limit;
        }

        $givenRegisterSubNetwork = 0;
        $subnetworks = User::whereHas('roles', function ($q) {
            return $q->where('id', 131);
        })->where('inserted_by', $managerId)->get();
        foreach ($subnetworks as $subnetwork) {
            $givenRegisterSubNetwork += $subnetwork->consultant->student_register_limit;
        }

        $givenRegisterNetwork = 0;
        $networks = User::whereHas('roles', function ($q) {
            return $q->where('id', 130);
        })->where('inserted_by', $managerId)->get();
        foreach ($networks as $network) {
            $givenRegisterNetwork += $network->consultant->student_register_limit;
        }
        if ($consultant != $studentsCount + $givenRegisterMoshaver + $givenRegisterSubNetwork + $givenRegisterNetwork) {
            Log::channel('bonyadConsultantCheck')->error("id={$managerId},studentCount={$studentsCount},givenRegisterM={$givenRegisterMoshaver},givenRegisterS={$givenRegisterSubNetwork},givenRegisterN={$givenRegisterNetwork},registerNumber={$consultant} has wrong data");
            $this->error("id={$managerId},studentCount={$studentsCount},givenRegisterM={$givenRegisterMoshaver},givenRegisterS={$givenRegisterSubNetwork},givenRegisterN={$givenRegisterNetwork},registerNumber={$consultant} has wrong data");
            $flag = true;
        }


        $freeSpace = 0;
        $total = 0;
        foreach ($bonyadData->pluck('consultant') as $data) {
            if ($data->student_register_limit < $data->student_register_number) {
                $this->error("user with id {$data->user_id} has wrong data");
                Log::channel('bonyadConsultantCheck')->error("user with id {$data->user_id} has wrong data");
                $total += $data->student_register_number - $data->student_register_limit;
                $flag = true;
            } else {
                $freeSpace += $data->student_register_limit - $data->student_register_number;
            }
        }
        $this->info("free space for registration= = $freeSpace");

        $total += $freeSpace + User::whereHas('roles', function ($query) {
                return $query->where('id', 123);
            })->count();

        $managerInfo = BonyadEhsanConsultant::find($managerId);
        $this->info("manager limit is = {$managerInfo->student_register_limit} and calculating data is = {$total}");

        if ($flag) {
            $systemUser = User::find(1);
            $systemUser->notify(new BonyadConsultantCheckerNotification('اختلافی در ظرفیت های ثبت نام بنیاد احسان'));
        }
        Log::channel('bonyadConsultantCheck')->info('done');
        return 0;
    }
}
