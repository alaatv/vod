<?php

namespace Database\Seeders;

use App\Transactiongateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TransactionGatewaysSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Transactiongateway::truncate();
        $data = [
            [
                'name' => 'zarinpal',
                'displayName' => 'زرین پال',
                'description' => 'درگاه پرداخت الکترونیک زرین پال',
                'merchantNumber' => '55eb1362-08d4-42ee-8c74-4c5f5bef37d4',
                'enable' => 1,
                'order' => 4,
                'url' => 'https://www.zarinpal.com/pg/pay/',
                'icon' => 'zarinpal-logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mellat',
                'displayName' => 'درگاه بانک ملت',
                'description' => 'به پرداخت بانک ملت',
                'enable' => 1,
                'order' => 1,
                'url' => 'https://bpm.shaparak.ir/pgwchannel/result.mellat',
                'icon' => 'mellat-logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'parsian',
                'displayName' => 'درگاه بانک پارسیان',
                'enable' => 1,
                'order' => 3,
                'url' => 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx',
                'icon' => 'parsian-logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'saman',
                'displayName' => 'درگاه بانک سامان آلاء',
                'enable' => 1,
                'order' => 2,
                'url' => 'https://sep.shaparak.ir/Payments/InitPayment.asmx',
                'icon' => 'saman-logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'saman2',
                'displayName' => 'درگاه بانک سامان سؤالا',
                'enable' => 0,
                'order' => 0,
                'url' => 'https://sep.shaparak.ir/Payments/InitPayment.asmx',
                'icon' => 'saman-logo.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'debitCardENB',
                'displayName' => 'کارت به کارت اقتصاد نوین',
                'enable' => 0,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'debitCardRQB',
                'displayName' => 'کارت به کارت رسالت',
                'enable' => 0,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($data as $arr) {
            Transactiongateway::create($arr);
        }
        Schema::enableForeignKeyConstraints();
    }
}
