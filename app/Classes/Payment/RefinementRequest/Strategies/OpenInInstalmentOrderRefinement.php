<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 1/8/2019
 * Time: 1:29 PM
 */

namespace App\Classes\Payment\RefinementRequest\Strategies;

use App\Classes\Payment\RefinementRequest\Refinement;
use App\Models\Order;
use App\Services\TransactionsSerivce;
use Exception;
use Illuminate\Http\Response;

class OpenInInstalmentOrderRefinement extends Refinement
{
    private $seller;

    public function __construct(int $seller)
    {
        parent::__construct();
        $this->seller = $seller;
    }


    /**
     * @return Refinement
     */
    public function loadData(): Refinement
    {
        if ($this->statusCode != Response::HTTP_OK) {
            return $this;
        }
        $openOrder = $this->getOpenOrder();
        $openOrder->load('orderproducts');
        if ($openOrder->orderproducts->isEmpty()) {
            $this->message = 'There is no items in your cart';
            $this->statusCode = Response::HTTP_BAD_REQUEST;

            return $this;
        }

        $this->order = $openOrder;
        $this->orderUniqueId = $openOrder->id;
        $this->getOrderCost();
        $this->resetWalletPendingCredit();
        $this->donateCost = $this->order->getDonateCost();
        $this->payByWallet();

        if ($this->cost > 0) {
            $result = $this->getNewTransaction();
            $this->statusCode = $result['statusCode'];
            $this->message = $result['message'];
            $this->transaction = $result['transaction'];
        } else {
            if ($this->cost == 0) {
                $this->statusCode = Response::HTTP_OK;
                $this->message = 'Zero cost';
                $this->transaction = null;
            } else {
                $this->statusCode = Response::HTTP_BAD_REQUEST;
                $this->message = 'Cost cant be minus';
            }
        }

        return $this;
    }

    /**
     * @return Order
     */
    private function getOpenOrder(): Order
    {
        return $this->user->getOpenOrderOrCreate(1);
    }


    protected function payByWallet(): void
    {
        $this->paidFromWalletCost = 0;
    }

    /**
     * @param  bool  $deposit
     *
     * @return array
     */
    protected function getNewTransaction($deposit = true)
    {
        $orderproducts = $this->order->orderproducts()->get();

        $products_installment = TransactionsSerivce::calculateInstalments($orderproducts);
        $this->cost = $products_installment->first()->cost;
        try {
            $transactions = $this->order->transactions()->createMany($products_installment->toArray());
            $result = [
                'statusCode' => Response::HTTP_OK,
                'message' => 'تراکنش با موفقیت ثبت شد.',
                'transaction' => $transactions[0],
            ];
        } catch (Exception $exception) {
            $result = [
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'خطای پایگاه داده در ثبت تراکنش',
            ];
        }

        return $result;
    }
}
