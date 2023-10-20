<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Ordermanagercomment;
use App\Models\Userbon;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Transactiongateway;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
    use Illuminate\Support\Collection;

class FakeOrdersSeeder extends Seeder
{
    private $mohamad;
    private $mohamadWallet;
    private $zarinPal;
    private $testCoupon;
    private $products ;
    private $bonId;

    public function __construct()
    {
        $user = $this->insertFakeUser();
        $this->mohamad = $user;

        $wallet = $this->insertFakeWallet($user);
        $this->mohamadWallet = $wallet;
        $this->zarinPal = Transactiongateway::where('name', 'zarinpal')->first();
        $this->testCoupon = Coupon::where('code', 'aminirad')->first();
        $initialProducts = Product::whereIn('id', [
            293, 292, 290, 289, 287, 286, 283, 282, 281, 270, 291, 273, 285,
        ])->get();
        $this->products = [];
        if ($initialProducts->count() <= 0){$this->bonId = 1;
    return;}
            $this->products = [
                '1' => $initialProducts->where('id', 293)->values()->toArray(),
                '2' => $initialProducts->whereIn('id', [292, 291])->values()->toArray(),
                '3' => $initialProducts->where('id', 290)->values()->toArray(),
                '4' => $initialProducts->whereIn('id', [ 289, 273 ])->values()->toArray(),
                '8' => $initialProducts->where('id', 287)->values()->toArray(),
                '5' => $initialProducts->whereIn('id', [ 286, 285 ])->values()->toArray(),
                '10' => $initialProducts->where('id', 283)->values()->toArray(),
                '7' => $initialProducts->where('id', 282)->values()->toArray(),
                '6' => $initialProducts->where('id', 281)->values()->toArray(),
                '9' => $initialProducts->where('id', 270)->values()->toArray(),
            ];
        $this->bonId = 1;
    }


    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        if(!$this->isDataProvided())
            dd('Initial data not found');

        $orders = $this->initiateOrdersData();
        $this->insertOrders($orders);
    }

    private function initiateOrdersData(){
        return collect([
            [
                'fakeId'    =>  1,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  24900,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CLOSED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_PAID'),
                'customerDescription'   => 'Test order: paid by wallet' ,
            ],
            [
                'fakeId'    =>  2,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  128100,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CLOSED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_PAID'),
                'customerDescription'   => 'Test order: paid online' ,
            ],
            [
                'fakeId'    =>  3,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  5880,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CLOSED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_PAID'),
                'customerDescription'   => 'Test order: paid online and by wallet' ,
            ],
            [
                'fakeId'    =>  4,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  11900,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CLOSED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_INDEBTED'),
                'customerDescription'   => 'Test order: ghesdi by wallet' ,
            ],
            [
                'fakeId'    =>  5,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  120000,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CLOSED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_INDEBTED'),
                'customerDescription'   => 'Test order: ghesdi by ATM' ,
            ],
            [
                'fakeId'    =>  6,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  0,
                'cost'  =>  330000,
                'coupon_id' =>  $this->testCoupon->id ,
                'couponDiscount' =>  $this->testCoupon->discount ,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_POSTED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_PAID'),
                'customerDescription'   => 'Test order: posted with coupon' ,
            ],
            [
                'fakeId'    =>  7,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  330000,
                'cost'  =>  0,
                'discount'  =>  100000,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_POSTED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_PAID'),
                'customerDescription'   => 'Test order:posted with having used bons' ,
            ],
            [
                'fakeId'    =>  8,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  18905,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CLOSED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_PAID'),
                'customerDescription'   => 'Test order: with having used bons' ,
            ],
            [
                'fakeId'    =>  9,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  79000,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_CANCELED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_UNPAID'),
                'customerDescription'   => 'Test order: canceled order' ,
            ],
            [
                'fakeId'    =>  10,
                'user_id'  =>   $this->mohamad->id,
                'costwithoutcoupon'  =>  330000,
                'cost'  =>  0,
                'completed_at'=>    Carbon::Now(),
                'orderstatus_id'    =>  config('constants.ORDER_STATUS_REFUNDED') ,
                'paymentstatus_id'  =>  config('constants.PAYMENT_STATUS_UNPAID'),
                'customerDescription'   => 'Test order: refunded order' ,
                'managerComment'    => 'Refunded'
            ],

        ]);
    }

    private function initiateOrderproductsData(){
        return collect([
            [
                'fakeOrder_id'  =>  1,
                'product_id'    =>  $this->products['1'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  24900,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ]
            ,[
                'fakeOrder_id'  =>  2,
                'product_id'    =>  $this->products['2'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  74500,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ]
            ,[
                'fakeOrder_id'  =>  2,
                'product_id'    =>  $this->products['2'][1]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  53600,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ]
            ,[
                'fakeOrder_id'  =>  3,
                'product_id'    =>  $this->products['3'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  14700,
                'discountPercentage'    =>  60,
                'discountAmount'        =>  0
            ]
            ,[
                'fakeOrder_id'  =>  4,
                'product_id'    =>  $this->products['4'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  11900,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ]
            ,[
                'fakeOrder_id'  =>  4,
                'product_id'    =>  $this->products['4'][1]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_GIFT'),
                'cost'  =>  45000,
                'discountPercentage'    => 0,
                'discountAmount'  => 0,
            ]
            ,[
                'fakeOrder_id'  =>  5,
                'product_id'    =>  $this->products['5'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  49000,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ]
            ,[
                'fakeOrder_id'  =>  5,
                'product_id'    =>  $this->products['5'][1]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  91000,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  20000,
            ]
            ,[
                'fakeOrder_id'  =>  6,
                'product_id'    =>  $this->products['6'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  330000,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
                'includedInCoupon'  =>  1,
            ]
            ,[
                'fakeOrder_id'  =>  7,
                'product_id'    =>  $this->products['7'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  330000,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
                'giveUserBon'   => true,
            ]
            ,[
                'fakeOrder_id'  =>  8,
                'product_id'    =>  $this->products['8'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  19900,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
                'userbonDiscount'   =>  5,
            ],
            [
                'fakeOrder_id'  =>  9,
                'product_id'    =>  $this->products['9'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  79000,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ],
            [
                'fakeOrder_id'  =>  10,
                'product_id'    =>  $this->products['10'][0]['id'],
                'orderproducttype_id'   =>  config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                'cost'  =>  330000,
                'discountPercentage'    =>  0,
                'discountAmount'  =>  0,
            ],
        ]);
    }

    private function initiateTransactionsData(){
        return collect([
            [
                'fakeOrder_id'  =>  1,
                'wallet_id'  =>  $this->mohamadWallet->id,
                'cost'  => 24900 ,
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_WALLET'),
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  2,
                'cost'  =>  128100,
                'authority' =>  '00000000000000000000000002'.Carbon::now()->timestamp,
                'transactionID' =>  Carbon::now()->timestamp.'orderTest2',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ONLINE'),
                'transactiongateway_id' =>  $this->zarinPal->id ,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'description'   =>  'شیمی و فیزیک تفتان - '.$this->mohamad->mobile,
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  3,
                'wallet_id'  =>  $this->mohamadWallet->id,
                'cost'  => 2500 ,
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_WALLET'),
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  3,
                'cost'  =>  3380,
                'authority' =>  '00000000000000000000000003'.Carbon::now()->timestamp,
                'transactionID' =>  Carbon::now()->timestamp.'orderTest3',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ONLINE'),
                'transactiongateway_id' =>  $this->zarinPal->id ,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'description'   =>  'زبان انگلیسی تفتان - '.$this->mohamad->mobile,
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  4,
                'wallet_id'  =>  $this->mohamadWallet->id,
                'cost'  => 10000 ,
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_WALLET'),
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  5,
                'cost'  =>  40000,
                'referenceNumber' =>  Carbon::now()->timestamp.'orderTest5',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ATM'),
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  5,
                'cost'  =>  40000,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_UNPAID'),
                'deadline_at'   =>  Carbon::createFromFormat('Y-m-d H:i:s', '2019-05-30 08:00:00')
            ],
            [
                'fakeOrder_id'  =>  5,
                'cost'  =>  40000,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_UNPAID'),
                'deadline_at'   =>  Carbon::createFromFormat('Y-m-d H:i:s', '2019-07-30 08:00:00')
            ],
            [
                'fakeOrder_id'  =>  6,
                'cost'  =>  240900,
                'traceNumber' =>  Carbon::now()->timestamp.'orderTest6',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ATM'),
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  7,
                'cost'  =>  230000,
                'authority' =>  '00000000000000000000000007'.Carbon::now()->timestamp,
                'transactionID' =>  Carbon::now()->timestamp.'orderTest7',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ONLINE'),
                'transactiongateway_id' =>  $this->zarinPal->id ,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'description'   =>  'تفتان دانلود ریاضی - '.$this->mohamad->mobile,
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  8,
                'cost'  =>  18905,
                'authority' =>  '00000000000000000000000008'.Carbon::now()->timestamp,
                'transactionID' =>  Carbon::now()->timestamp.'orderTest8',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ONLINE'),
                'transactiongateway_id' =>  $this->zarinPal->id ,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'description'   =>  'هندسه - '.$this->mohamad->mobile,
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  10,
                'cost'  =>  330000,
                'authority' =>  '00000000000000000000000010'.Carbon::now()->timestamp,
                'transactionID' =>  Carbon::now()->timestamp.'orderTest10',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ONLINE'),
                'transactiongateway_id' =>  $this->zarinPal->id ,
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'description'   =>  'تفتتان دانلود - '.$this->mohamad->mobile,
                'completed_at'  =>  Carbon::now(),
            ],
            [
                'fakeOrder_id'  =>  10,
                'cost'  =>  -330000,
                'referenceNumber' =>  Carbon::now()->timestamp.'orderTest10_refund',
                'paymentmethod_id'  =>  config('constants.PAYMENT_METHOD_ATM'),
                'transactionstatus_id'  =>  config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'managerComment'    =>  'refunded',
                'completed_at'  =>  Carbon::now(),
            ],

        ]);

    }

    private function getOrderproductsOfOrder($orderId){
        $orderproducts = $this->initiateOrderproductsData();
        return $orderproducts->where('fakeOrder_id' , $orderId);
    }

    private function getTransactionsOfOrder($orderId){
        $transactions = $this->initiateTransactionsData();
        return $transactions->where('fakeOrder_id' , $orderId);
    }

    private function isDataProvided():bool{
        if(!isset($this->zarinPal))
            return false;
        if(!isset($this->testCoupon))
            return false;
        if(count($this->products)<=0)
            return false;

        return true;
    }

    /**
     * @param  Collection  $orders
     */
    private function insertOrders(Collection $orders): void
    {
        foreach ($orders as $order) {
            $orderSeed = $this->insertOrder($order);

            $orderproducts = $this->getOrderproductsOfOrder($order['fakeId']);
            $this->insertOrderproducts($orderproducts,$orderSeed);

            $transactions = $this->getTransactionsOfOrder($order['fakeId']);
            $this->insertTransactions($transactions,$orderSeed);
        }
    }

    /**
     * @param $orderproducts
     * @param Order $orderSeed
     */
    private function insertOrderproducts($orderproducts, Order $orderSeed): void
    {
        foreach ($orderproducts as $orderproduct) {
            $this->insertOrderproduct( $orderproduct , $orderSeed);
        }
    }

    /**
     * @param $transactions
     * @param Order $orderSeed
     */
    private function insertTransactions($transactions,Order $orderSeed): void
    {

        foreach ($transactions as $transaction) {
            $this->insertTransaction($transaction,$orderSeed);
        }
    }

    /**
     * @param array $order
     * @return Order
     */
    private function insertOrder(array $order):Order
    {
        $orderSeed = new Order();
        $orderSeed->user_id = $order['user_id'];
        $orderSeed->costwithoutcoupon = $order['costwithoutcoupon'];
        $orderSeed->cost = $order['cost'];
        $orderSeed->completed_at = $order['completed_at'];
        $orderSeed->orderstatus_id = $order['orderstatus_id'];
        $orderSeed->paymentstatus_id = $order['paymentstatus_id'];
        $orderSeed->customerDescription = $order['customerDescription'];

        if (isset($order['coupon_id']))
            $orderSeed->coupon_id = $order['coupon_id'];
        if (isset($order['couponDiscount']))
            $orderSeed->couponDiscount = $order['couponDiscount'];
        if (isset($order['couponDiscountAmount']))
            $orderSeed->couponDiscountAmount = $order['couponDiscountAmount'];
        if (isset($order['discount']))
            $orderSeed->discount = $order['discount'];

        $orderSeed->save();

        if (!isset($order['managerComment'])){
        return $orderSeed;
    }
            $managerComment = new Ordermanagercomment();
            $managerComment->comment = $order['managerComment'];
            $managerComment->order_id = $orderSeed->id;
            $managerComment->user_id = $order['user_id'];
            $managerComment->save();

        return $orderSeed;
    }

    /**
     * @param $orderproduct
     * @param $orderSeed
     */
    private function insertOrderproduct( $orderproduct , $orderSeed): void
    {
        $orderproductSeed = new Orderproduct();
        $orderproductSeed->order_id = $orderSeed->id;
        $orderproductSeed->product_id = $orderproduct['product_id'];
        $orderproductSeed->orderproducttype_id = $orderproduct['orderproducttype_id'];
        $orderproductSeed->cost = $orderproduct['cost'];
        $orderproductSeed->discountPercentage = $orderproduct['discountPercentage'];
        $orderproductSeed->discountAmount = $orderproduct['discountAmount'];

        (isset($orderproduct['includedInCoupon'])) ? $orderproductSeed->includedInCoupon = 1 : $orderproductSeed->includedInCoupon = 0;

        $orderproductSeed->save();

        if (isset($orderproduct['userbonDiscount'])) {
            $userbon = new Userbon();
            $userbon->bon_id = $this->bonId;
            $userbon->user_id = $orderSeed->user_id;
            $userbon->totalNumber = 1;
            $userbon->usedNumber = 1;
            $userbon->userbonstatus_id = config('constants.USERBON_STATUS_USED');
            $userbon->save();

            $orderproductSeed->userbons()
                ->attach($userbon->id, [
                    'usageNumber' => $userbon->usedNumber,
                    'discount' => $orderproduct['userbonDiscount'],
                ]);

        }

        if (!isset($orderproduct['giveUserBon'])){
    return;}
            $userbon = new Userbon();
            $userbon->bon_id = $this->bonId;
            $userbon->user_id = $orderSeed->user_id;
            $userbon->totalNumber = 1;
            $userbon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
            $userbon->orderproduct_id = $orderproductSeed->id;
            $userbon->save();

    }

    /**
     * @param $transaction
     * @param $orderSeed
     */
    private function insertTransaction($transaction,$orderSeed): void
    {
        $transactionSeed = new Transaction();
        $transactionSeed->order_id = $orderSeed->id;
        $transactionSeed->cost = $transaction['cost'];
        $transactionSeed->transactionstatus_id = $transaction['transactionstatus_id'];

        if (isset($transaction['description']))
            $transactionSeed->description = $transaction['description'];
        if (isset($transaction['wallet_id']))
            $transactionSeed->wallet_id = $transaction['wallet_id'];
        if (isset($transaction['authority']))
            $transactionSeed->authority = $transaction['authority'];
        if (isset($transaction['transactionID']))
            $transactionSeed->transactionID = $transaction['transactionID'];
        if (isset($transaction['traceNumber']))
            $transactionSeed->traceNumber = $transaction['traceNumber'];
        if (isset($transaction['referenceNumber']))
            $transactionSeed->referenceNumber = $transaction['referenceNumber'];
        if (isset($transaction['transactiongateway_id']))
            $transactionSeed->transactiongateway_id = $transaction['transactiongateway_id'];
        if (isset($transaction['paymentmethod_id']))
            $transactionSeed->paymentmethod_id = $transaction['paymentmethod_id'];
        if (isset($transaction['managerComment']))
            $transactionSeed->managerComment = $transaction['managerComment'];
        if (isset($transaction['deadline_at']))
            $transactionSeed->deadline_at = $transaction['deadline_at'];
        if (isset($transaction['completed_at']))
            $transactionSeed->completed_at = $transaction['completed_at'];

        $transactionSeed->save();
    }

    /**
     * @return User|Builder|Model|object|null
     */
    private function insertFakeUser()
    {
        $fakeMobile = '09194251469';
        $fakeNationalCode = '0000000000';
        $user = User::where('mobile', $fakeMobile)->where('nationalCode', $fakeNationalCode)->first();
        if (!is_null($user)){return $user;
    }
            $user = User::create([
                'firstName' => 'محمد 2',
                'lastName' => 'شاهرخی 2',
                'mobile' => $fakeMobile,
                'nationalCode' => $fakeNationalCode,
                'password' => bcrypt($fakeNationalCode),
                'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
            ]);
        return $user;
    }

    /**
     * @param $user
     * @return Wallet|Model
     */
    private function insertFakeWallet($user)
    {
        $initialBalance = 100000;
        $wallet = $user->wallets->where('wallettype_id', config('constants.WALLET_TYPE_GIFT'))->first();
        if (!is_null($wallet)){return $wallet;
    }
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'wallettype_id' => config('constants.WALLET_TYPE_GIFT'),
                'balance' => $initialBalance
            ]);
        return $wallet;
    }

}
