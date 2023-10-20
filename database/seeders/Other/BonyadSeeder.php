<?php

namespace Database\Seeders\Other;

use App\Models\User;
use App\Repositories\Loging\ActivityLogRepo;
use Illuminate\Database\Seeder;

class BonyadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bonyadUser = User::factory()->state([
            'firstName' => 'bonyad',
            'lastName' => 'manager',
        ])->create();
        $bonyadUser->roles()->attach([113]);
        $bonyadUser->consultant()->create(['student_register_limit' => 1000]);

        $network = $bonyadUser->factory()->state([
            'firstName' => 'bonyad',
            'lastName' => 'network',
            'inserted_by' => $bonyadUser->id,
        ])->create();
        ActivityLogRepo::logBonyadEhsanUserRegistration($bonyadUser, $network);
        $network->consultant()->create(['student_register_limit' => 500]);
        $bonyadUser->consultant->increaseRegistrationNumber(500);
        $network->roles()->attach([130]);


        $subnetwork = $network->factory()->state([
            'firstName' => 'bonyad',
            'lastName' => 'subnetwork',
            'inserted_by' => $network->id,
        ])->create();
        ActivityLogRepo::logBonyadEhsanUserRegistration($network, $subnetwork);
        $subnetwork->consultant()->create(['student_register_limit' => 250]);
        $network->consultant->increaseRegistrationNumber(250);
        $subnetwork->roles()->attach([131]);


        $moshaver = $subnetwork->factory()->state([
            'firstName' => 'bonyad',
            'lastName' => 'moshaver',
            'inserted_by' => $subnetwork->id,
        ])->create();
        ActivityLogRepo::logBonyadEhsanUserRegistration($subnetwork, $moshaver);
        $moshaver->consultant()->create(['student_register_limit' => 125]);
        $subnetwork->consultant->increaseRegistrationNumber(125);
        $moshaver->roles()->attach([124]);

        $student = $moshaver->factory()->state([
            'firstName' => 'bonyad',
            'lastName' => 'student',
            'inserted_by' => $moshaver->id,
        ])->create();
        ActivityLogRepo::logBonyadEhsanUserRegistration($moshaver, $student);
        $moshaver->consultant->increaseRegistrationNumber();
        $student->roles()->attach([123]);


    }
}
