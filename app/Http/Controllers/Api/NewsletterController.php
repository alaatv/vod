<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\newsletter\CreateNewsletterRequest;
use App\Models\Newsletter;
use App\Models\Newsletter;
use App\Notifications\KonkurishoNotification;
use App\Repositories\NewsletterRepo;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use function response;

class NewsletterController extends Controller
{
    public function store(CreateNewsletterRequest $request)
    {
        $message = 'ثبت نام با موفقیت انجام شد';
        $data = $request->validated();
        $eventId = Arr::get($data, 'event_id');
        if (Newsletter::whereMobile(Arr::get($data, 'mobile'))->whereEventId($eventId)->exists()) {
            $message =
                'ممنون از ثبت درخواست مجدد. به دلیل درخواست بالای شما عزیزان به زودی همکاران آلاء با شما تماس می‌گیرند';
            return myAbort(Response::HTTP_BAD_REQUEST, $message);
        }
        if ($eventId == 22) {
            $message = 'ثبت نام انجام شد و برای فعالسازی با شما تماس میگیرم';
            Notification::route('mobile',
                $data['mobile'])->notify(new KonkurishoNotification('http://alaatv.com/product/1104'));
        }
        NewsletterRepo::createNewsletter($data);
        return response()->json([['data' => ['is_active' => true, 'verified' => true], 'message' => $message]]);
    }
}
