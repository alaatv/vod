<?php

namespace Database\Seeders\ReferralCode;

use App\Models\ReferralCode;
use App\Models\ReferralRequest;

use App\Models\User;
use Illuminate\Database\Seeder;

class MakeReferralCodeForAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::find(1);
        ReferralRequest::factory()->state([
            'owner_id' => $userId
        ])->has(
            ReferralCode::factory()->count(10)->state([
                'owner_id' => $userId
            ])
        )
            ->create();
    }
}
