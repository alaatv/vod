<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\GodarGift1400Notification;
use App\Traits\GiveGift\GiveGiftsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GiveGodarJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use GiveGiftsHelper;

    private const MAP = [
        347 => [385],
        439 => [379, 381, 383],
        440 => [497],
        441 => [373],
        442 => [496],
        443 => [387],
        569 => [627],
        570 => [626],
        571 => [625],
        572 => [624],
    ];
    public array $gifts = [];

    public function __construct(private Order $order, private bool $shouldSendNotification)
    {
        $this->getGifts();
    }

    public function getGifts(): void
    {
        $giftsKeys = array_intersect(array_keys(self::MAP),
            $this->order->orderproducts->pluck('product_id')->toArray());

        foreach ($giftsKeys as $key) {
            $this->gifts = array_merge($this->gifts, self::MAP[$key]);
        }
    }

    public function handle()
    {
        $this->giveGiftProducts($this->order, $this->gifts);

        if ($this->shouldSendNotification) {
            $this->order?->user->notify(new GodarGift1400Notification(route('web.user.asset')));
        }
    }
}
