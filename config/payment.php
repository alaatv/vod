<?php

use App\Classes\Payment\AlaaParsian;
use Shetabit\Multipay\Drivers\Behpardakht\Behpardakht;
use Shetabit\Multipay\Drivers\Saman\Saman;
use Shetabit\Multipay\Drivers\Zarinpal\Zarinpal;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This value determines which of the following gateway to use.
    | You can switch to a different driver at runtime.
    |
    */
    'default' => 'mellat',

    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    |
    | These are the list of drivers to use for this package.
    | You can change the name. Then you'll have to change
    | it in the map array too.
    |
    */
    'drivers' => [
        'asanpardakht' => [
            'apiPurchaseUrl'     => 'https://ipgsoap.asanpardakht.ir/paygate/merchantservices.asmx?wsdl',
            'apiPaymentUrl'      => 'https://asan.shaparak.ir',
            'apiVerificationUrl' => 'https://ipgsoap.asanpardakht.ir/paygate/merchantservices.asmx?wsdl',
            'apiUtilsUrl'        => 'https://ipgsoap.asanpardakht.ir/paygate/internalutils.asmx?wsdl',
            'key'                => '',
            'iv'                 => '',
            'username'           => null,
            'password'           => null,
            'merchantId'         => null,
            'callbackUrl'        => null,
            'description'        => 'payment using asanpardakht',
            'currency'           => 'T',
        ],
        'mellat' => [
            'apiPurchaseUrl'             => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'apiPaymentUrl'              => 'https://bpm.shaparak.ir/pgwchannel/startpay.mellat',
            'apiVerificationUrl'         => 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl',
            'merchantId'                 => env('BEHPARDAKHT_TERMINAL_ID'),
            'terminalId'                 => env('BEHPARDAKHT_TERMINAL_ID'),
            'username'                   => env('BEHPARDAKHT_USERNAME'),
            'password'                   => env('BEHPARDAKHT_PASSWORD'),
            'callbackUrl'                => null,
            'description'                => 'payment using behpardakht',
            'verification_token'         => 'RefId',
            'currency'                   => 'T',
            'cumulativeDynamicPayStatus' => false,
        ],
        'idpay' => [
            'apiPurchaseUrl' => 'https://api.idpay.ir/v1.1/payment',
            'apiPaymentUrl' => 'https://idpay.ir/p/ws/',
            'apiSandboxPaymentUrl' => 'https://idpay.ir/p/ws-sandbox/',
            'apiVerificationUrl' => 'https://api.idpay.ir/v1.1/payment/verify',
            'merchantId' => '',
            'callbackUrl' => null,
            'description' => 'payment using idpay',
            'sandbox' => false, // set it to true for test environments
        ],
        'irankish' => [
            'apiPurchaseUrl'     => 'https://ikc.shaparak.ir/XToken/Tokens.xml',
            'apiPaymentUrl'      => 'https://ikc.shaparak.ir/TPayment/Payment/index/',
            'apiVerificationUrl' => 'https://ikc.shaparak.ir/XVerify/Verify.xml',
            'merchantId'         => '',
            'sha1Key'            => '',
            'callbackUrl'        => null,
            'description'        => 'payment using irankish',
            'currency'           => 'T',
        ],
        'nextpay' => [
            'apiPurchaseUrl'     => 'https://api.nextpay.org/gateway/token.http',
            'apiPaymentUrl'      => 'https://api.nextpay.org/gateway/payment/',
            'apiVerificationUrl' => 'https://api.nextpay.org/gateway/verify.http',
            'merchantId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using nextpay',
            'currency'           => 'T',
        ],
        'parsian' => [
            'apiPurchaseUrl'      => 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?wsdl',
            'apiPaymentUrl'       => 'https://pec.shaparak.ir/NewIPG/',
            'apiVerificationUrl'  => 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?wsdl',
            'apiGetSaleReportUrl' => 'https://pgwservices.pec.ir/api/PGWReport/GetSaleReport',
            'merchantId'          => env('PARSIAN_MERCHANT_ID'),
            'Terminal'            => env('PARSIAN_TERMINAL_ID'),
            'username'            => env('PARSIAN_USERNAME'),
            'password'            => env('PARSIAN_PASSWORD'),
            'callbackUrl'         => null,
            'description'         => 'payment using parsian',
            'verification_token'  => 'Token',
            'currency'            => 'T',
        ],
        'pasargad' => [
            'apiPaymentUrl'          => 'https://pep.shaparak.ir/payment.aspx',
            'apiGetToken'            => 'https://pep.shaparak.ir/Api/v1/Payment/GetToken',
            'apiCheckTransactionUrl' => 'https://pep.shaparak.ir/Api/v1/Payment/CheckTransactionResult',
            'apiVerificationUrl'     => 'https://pep.shaparak.ir/Api/v1/Payment/VerifyPayment',
            'merchantId'             => '',
            'terminalCode'           => '',
            'certificate'            => '', // can be string (and set certificateType to xml_string) or an xml file path (and set cetificateType to xml_file)
            'certificateType'        => 'xml_file', // can be: xml_file, xml_string
            'callbackUrl'            => null,
            'currency'               => 'T',
        ],
        'payir' => [
            'apiPurchaseUrl'     => 'https://pay.ir/pg/send',
            'apiPaymentUrl'      => 'https://pay.ir/pg/',
            'apiVerificationUrl' => 'https://pay.ir/pg/verify/',
            'merchantId'         => 'test', // set it to `test` for test environments
            'callbackUrl'        => null,
            'description'        => 'payment using payir',
            'currency'           => 'T',
        ],
        'paypal' => [
            /* normal api */
            'apiPurchaseUrl'            => 'https://www.paypal.com/cgi-bin/webscr',
            'apiPaymentUrl'             => 'https://www.zarinpal.com/pg/StartPay/',
            'apiVerificationUrl'        => 'https://ir.zarinpal.com/pg/services/WebGate/wsdl',

            /* sandbox api */
            'sandboxApiPurchaseUrl'     => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
            'sandboxApiPaymentUrl'      => 'https://sandbox.zarinpal.com/pg/StartPay/',
            'sandboxApiVerificationUrl' => 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl',

            'mode'        => 'normal', // can be normal, sandbox
            'currency'    => 'T',
            'id'          => '', // Specify the email of the PayPal Business account
            'callbackUrl' => null,
            'description' => 'payment using paypal',
        ],
        'payping' => [
            'apiPurchaseUrl'     => 'https://api.payping.ir/v1/pay/',
            'apiPaymentUrl'      => 'https://api.payping.ir/v1/pay/gotoipg/',
            'apiVerificationUrl' => 'https://api.payping.ir/v1/pay/verify/',
            'merchantId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using payping',
            'currency'           => 'T',
        ],
        'paystar' => [
            'apiPurchaseUrl'     => 'https://paystar.ir/api/create/',
            'apiPaymentUrl'      => 'https://paystar.ir/paying/',
            'apiVerificationUrl' => 'https://paystar.ir/api/verify/',
            'merchantId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using paystar',
            'currency'           => 'T',
        ],
        'poolam' => [
            'apiPurchaseUrl'     => 'https://poolam.ir/invoice/request/',
            'apiPaymentUrl'      => 'https://poolam.ir/invoice/pay/',
            'apiVerificationUrl' => 'https://poolam.ir/invoice/check/',
            'merchantId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using poolam',
            'currency'           => 'T',
        ],
        'sadad' => [
            'apiPurchaseUrl'     => 'https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest',
            'apiPaymentUrl'      => 'https://sadad.shaparak.ir/VPG/Purchase',
            'apiVerificationUrl' => 'https://sadad.shaparak.ir/VPG/api/v0/Advice/Verify',
            'key'                => '',
            'merchantId'         => '',
            'terminalId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using sadad',
            'currency'           => 'T',
        ],
        'saman' => [
            'apiPurchaseUrl'     => 'https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL',
            'apiPaymentUrl'      => 'https://sep.shaparak.ir/payment.aspx',
            'apiVerificationUrl' => 'https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL',
            'merchantId'         => env('SAMAN_MERCHANT_ID'),
            'callbackUrl'        => null,
            'description'        => 'payment using saman',
            'verification_token' => 'ResNum',
            'currency'           => 'T',
        ],
        'saman2' => [
            'apiPurchaseUrl'     => 'https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL',
            'apiPaymentUrl'      => 'https://sep.shaparak.ir/payment.aspx',
            'apiVerificationUrl' => 'https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL',
            'merchantId'         => env('SAMAN2_MERCHANT_ID'),
            'callbackUrl'        => null,
            'description'        => 'payment using saman',
            'verification_token' => 'ResNum',
            'currency'           => 'T',
        ],
        'sepehr' => [
            'apiGetToken'        => 'https://mabna.shaparak.ir:8081/V1/PeymentApi/GetToken',
            'apiPaymentUrl'      => 'https://mabna.shaparak.ir:8080/pay',
            'apiVerificationUrl' => 'https://mabna.shaparak.ir:8081/V1/PeymentApi/Advice',
            'terminalId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using sepehr(saderat)',
            'currency'           => 'T',
        ],
        'yekpay' => [
            'apiPurchaseUrl'     => 'https://gate.yekpay.com/api/payment/server?wsdl',
            'apiPaymentUrl'      => 'https://gate.yekpay.com/api/payment/start/',
            'apiVerificationUrl' => 'https://gate.yekpay.com/api/payment/server?wsdl',
            'fromCurrencyCode'   => 978,
            'toCurrencyCode'     => 364,
            'merchantId'         => '',
            'callbackUrl'        => null,
            'description'        => 'payment using yekpay',
            'currency'           => 'T',
        ],
        'zarinpal' => [
            /* normal api */
            'apiPurchaseUrl' => 'https://ir.zarinpal.com/pg/services/WebGate/wsdl',
            'apiPaymentUrl' => 'https://www.zarinpal.com/pg/StartPay/',
            'apiVerificationUrl' => 'https://ir.zarinpal.com/pg/services/WebGate/wsdl',

            /* sandbox api */
            'sandboxApiPurchaseUrl' => 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl',
            'sandboxApiPaymentUrl' => 'https://sandbox.zarinpal.com/pg/StartPay/',
            'sandboxApiVerificationUrl' => 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl',

            /* zarinGate api */
            'zaringateApiPurchaseUrl' => 'https://ir.zarinpal.com/pg/services/WebGate/wsdl',
            'zaringateApiPaymentUrl' => 'https://www.zarinpal.com/pg/StartPay/:authority/ZarinGate',
            'zaringateApiVerificationUrl' => 'https://ir.zarinpal.com/pg/services/WebGate/wsdl',

            'mode'               => 'sandbox', // can be normal, sandbox, zaringate
            'merchantId'         => env('ZARINPAL_MERCHANT_ID'),
            'callbackUrl'        => null,
            'description'        => 'payment using zarinpal',
            'verification_token' => 'Authority',
            'currency'           => 'T',
        ],
        'zibal' => [
            /* normal api */
            'apiPurchaseUrl'     => 'https://gateway.zibal.ir/v1/request',
            'apiPaymentUrl'      => 'https://gateway.zibal.ir/start/',
            'apiVerificationUrl' => 'https://gateway.zibal.ir/v1/verify',

            'mode' => 'normal', // can be normal, direct

            'merchantId'  => '',
            'callbackUrl' => null,
            'description' => 'payment using zibal',
            'currency'    => 'T',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Maps
    |--------------------------------------------------------------------------
    |
    | This is the array of Classes that maps to Drivers above.
    | You can create your own driver if you like and add the
    | config in the drivers array and the class to use for
    | here with the same name. You will have to extend
    | Shetabit\Multipay\Abstracts\Driver in your driver.
    |
    */
    'map' => [
        'mellat'   => Behpardakht::class,
        'zarinpal' => Zarinpal::class,
        'parsian'  => AlaaParsian::class,
        'saman'  => Saman::class,
        'saman2'  => Saman::class,
    ],

    'AVAILABLE_GATEWAYS' => [
        ['name' => 'mellat', 'url' => 'https://bpm.shaparak.ir/pgwchannel/result.mellat'],
        ['name' => 'zarinpal', 'url' => 'https://www.zarinpal.com/pg/pay/'],
    ],


    'logo' => [
        'zarinpal' => 'acm/extra/payment/gateway/zarinpal.png',
        'mellat' => '/acm/extra/payment/gateway/mellat-logo.gif'
    ]
];
