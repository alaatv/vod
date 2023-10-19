<?php

namespace App\Classes\Payment;

use Illuminate\Support\Arr;
use Shetabit\Multipay\Contracts\ReceiptInterface;
use Shetabit\Multipay\Drivers\Parsian\Parsian;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Request;
use SoapClient;
use SoapFault;

class AlaaParsian extends Parsian
{
    private $statusMessage = [
        '0' => 'موفق',
        '1' => 'صادرکننده ی کارت از انجام تراکنش صرف نظر کرد',
        '2' => 'عملیات تاییدیه این تراکنش قبلا باموفقیت صورت پذیرفته است',
        '3' => 'پذیرنده ی فروشگاهی نامعتبر می باشد',
        '4' => 'کارت توسط دستگاه ضبط شود',
        '5' => 'از انجام تراکنش صرف نظر شد',
        '6' => 'بروز خطایی ناشناخته',
        '12' => 'تراکنش نامعتبر است',
        '13' => 'مبلغ تراکنش اصلاحیه نادرست است',
        '14' => 'شماره کارت ارسالی نامعتبر است (وجود ندارد)',
        '15' => 'صادرکننده ی کارت نامعتبراست (وجود ندارد)',
        '16' => 'تراکنش مورد تایید است و اطلاعات شیار سوم کارت به روز رسانی شود',
        '-138' => 'عملیات پرداخت توسط کاربر لغو شد',
        '-132' => 'مبلغ تراکنش کمتر از حداقل مجاز می باشد',
        '-131' => 'Invalid token',
        '-130' => 'توکن منقضی شده است',
        '-129' => 'قالب داده ورودی صحیح نمی باشد',
        '-128' => 'قالب آدرس IP معتبر نمی باشد',
        '-127' => 'آدرس اینترنتی معتبر نمی باشد',
        '-112' => 'شناسه سفارش تکراری است',
        '-111' => 'مبلغ تراکنش بیش از حد مجاز پذیرنده می باشد',
        '-107' => 'قابلیت ارسال تاییده تراکنش برای پذیرنده غیر فعال می باشد',
        '-101' => 'پذیرنده اهراز هویت نشد',
        '-102' => 'تراکنش با موفقیت برگشت داده شد',
        '-103' => 'قابلیت خرید برای پذیرنده غیر فعال می باشد',
        '-100' => 'پذیرنده غیرفعال می باشد',
        '-1528' => 'اطلاعات پرداخت یافت نشد',
        '-1533' => 'تراکنش قبلاً تایید شده است',
        '-1532' => 'تراکنش از سوی پذیرنده تایید شد',
        '-1531' => 'تایید تراکنش ناموفق امکان پذیر نمی باشد',

    ];

    /**
     * Verify payment
     *
     * @return ReceiptInterface
     *
     * @throws InvalidPaymentException
     * @throws SoapFault
     */
    public function verify(): ReceiptInterface
    {
        $status = Request::input('status');
        $token = Request::input('Token');

        if ($status != 0 || empty($token)) {
            if (is_null($status)) {
                throw new InvalidPaymentException('تراکنش توسط کاربر کنسل شده است.', -2);
            } else {
                $message = Arr::get($this->statusMessage, $status, 'تراکنش توسط کاربر کنسل شده است.');
                throw new InvalidPaymentException($message, $status);
            }
        }


        $data = $this->prepareVerificationData();
        $soap = new SoapClient($this->settings->apiVerificationUrl);

        $response = $soap->ConfirmPayment(['requestData' => $data]);
        if (empty($response->ConfirmPaymentResult)) {
            throw new InvalidPaymentException('از سمت بانک پاسخی دریافت نشد.');
        }
        $result = $response->ConfirmPaymentResult;

        $hasWrongStatus = (!isset($result->Status) || $result->Status != 0);
        $hasWrongRRN = (!isset($result->RRN) || $result->RRN <= 0);
        if ($hasWrongStatus || $hasWrongRRN) {
            if (is_null($result->Status)) {
                $message = 'خطا از سمت بانک با کد '.$result->Status.' رخ داده است.';
                throw new InvalidPaymentException($message, -3);
            } else {
                $message =
                    '-3.'.$result->Status.': '.Arr::get($this->statusMessage, $result->Status,
                        'تراکنش توسط کاربر کنسل شده است.');
                throw new InvalidPaymentException($message, $result->Status);
            }
        }

        return $this->createReceipt($result->RRN);
    }
}
