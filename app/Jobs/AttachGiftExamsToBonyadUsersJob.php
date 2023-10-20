<?php

namespace App\Jobs;

use App\Models\Major;
use App\Repositories\OrderproductRepo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AttachGiftExamsToBonyadUsersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $bonyadOrder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bonyadOrder)
    {
        $this->bonyadOrder = $bonyadOrder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->bonyadOrder->user;
        $majorId = $user->major_id;
        if (!in_array($majorId, [Major::ENSANI, Major::TAJROBI, Major::RIYAZI])) {
            return null;
        }
        if ($majorId == Major::RIYAZI) {
            $productId = 804;
        } else {
            if ($majorId == Major::TAJROBI) {
                $productId = 819;
            } else {
                if ($majorId == Major::ENSANI) {
                    $productId = 834;
                }
            }
        }
        $hasExam = $user->userHasAnyOfTheseProducts([$productId]);
        if ($hasExam) {
            return null;
        }

        OrderproductRepo::createGiftOrderproduct($this->bonyadOrder->id, $productId, 0);
    }
}
