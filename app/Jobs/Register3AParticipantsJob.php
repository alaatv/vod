<?php

namespace App\Jobs;

use App\Models\_3aExam;
use App\Traits\APIRequestCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Register3AParticipantsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use APIRequestCommon;

    private $user;
    private $myOrder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($myOrder, $user)
    {
        $this->myOrder = $myOrder;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $_3AOrderproducts = $this->myOrder->get3AOrderproducts();
        foreach ($_3AOrderproducts as $orderproduct) {
            $exams = _3aExam::productId($orderproduct->product_id)->get();

            foreach ($exams as $exam) {
                $result = $this->register3ARequest($this->user, $exam->id);
                if (!$result) {
                    Log::channel('register3AParticipantsErrors')->error('Product '.$orderproduct->product_id.', Exam '.$exam->id.' was not registered for user '.$this->user->id);
                }
            }
        }
    }
}
