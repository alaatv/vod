<?php

namespace App\Jobs;

use App\Models\_3aExam;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use App\Traits\APIRequestCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AttachExamsToAbsrishamProUsersJob implements ShouldQueue
{
    use APIRequestCommon;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $examProductIds =
            ProductRepository::_3aExamsProductsIds()->isChild()->whereHas('children')->idFrom(803)->idTo(952)->pluck('id')->toArray();
        $orderProductData = [];
        $userPurchasedProductIds = $this->user->getUserProductsId();
        foreach ($examProductIds as $productId) {
            if (in_array($productId, $userPurchasedProductIds)) {
                continue;
            }
            $orderProductData[] = $productId;
        }
        if (empty($orderProductData)) {
            return null;
        }

        $order = OrderRepo::createBasicCompletedOrder($this->user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0);
        foreach ($orderProductData as $orderProductDatum) {
            OrderproductRepo::createGiftOrderproduct($order->id, $orderProductDatum, 0);
        }
        foreach ($examProductIds as $productId) {
            $exams = _3aExam::productId($productId)->get();
            foreach ($exams as $exam) {
                $result = $this->register3ARequest($this->user, $exam->id);
                if (!$result) {
                    Log::channel('register3AParticipantsErrors')->error('Product '.$productId.', Exam '.$exam->id.' was not registered for user '.$this->user->id);
                }
            }
        }
    }
}
