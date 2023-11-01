<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transactiongateway;
use App\PaymentModule\Money;
use App\Repositories\BillingRepo;
use Illuminate\Http\Request;


class BillingController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $pageName = null;
        $products = Product::query()->orderBy('created_at', 'desc')->get();
        $gateWays = Transactiongateway::whereIn('id', [4, 5, 6, 9])->get();
        $checkoutStatuses = null;
//        $checkoutStatuses[0]    = 'همه';
        $totalNumber = 0;
        $totalSale = 0;
        $totalTransactionSum = null;
        $checkoutResult = null;
        $orderproducts = collect();
        $transactions = collect();
        $productIds = $request->get('product_id', []);
        $tableSection = false;
        $formAction = route('billing.index');
        $gateway =
            (is_null($request->get('gateway')) || is_array($request->get('gateway'))) ? $request->get('gateway') : [$request->get('gateway')];

        $billings =
            BillingRepo::getInvoiceByDate($request->get('createdSinceDate'), $request->get('createdTillDate'), $gateway,
                $productIds);

        foreach ($billings as $billing) {
            $totalSale += Money::fromTomans($billing->sum_f_category_cost)->rials();
            $totalNumber += $billing->op_count;
        }
        $totalSale = number_format($totalSale);
        $filterDataCreatedSinceDate = $request->get('createdSinceDate');
        $filterDataCreatedTillDate = $request->get('createdTillDate');
        $filterDataGateway = $request->get('gateway');


        $data = [

            'products' => $products,
            'pageName' => $pageName,
            'checkoutStatuses' => $checkoutStatuses,
            'totalNumber' => $totalNumber,
            'totalSale' => $totalSale,
            'totalTransactionSum' => $totalTransactionSum,
            'checkoutResult' => $checkoutResult,
            'orderproducts' => $orderproducts,
            'transactions' => $transactions,
            'tableSection' => $tableSection,
            'formAction' => $formAction,
            'gateWays' => $gateWays,
            'filterDataCreatedSinceDate' => $filterDataCreatedSinceDate,
            'filterDataCreatedTillDate' => $filterDataCreatedTillDate,
            'filterDataGateway' => $filterDataGateway
        ];
        return response()->json($data);
    }

    private function callMiddlewares()
    {
        $this->middleware('permission:'.config('constants.LIST_BILLING_ACCESS'), ['only' => ['index',],]);
    }
}