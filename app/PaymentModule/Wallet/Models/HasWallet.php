<?php

namespace App\PaymentModule\Wallet\Models;

use App\Http\Controllers\Web\WalletController;
use App\Models\Wallet;
use App\PaymentModule\Wallet\WalletRepo;

trait HasWallet
{
    /**
     * Retrieve the wallet of this user
     *
     * @return mixed
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Retrieve the balance of all of this user's wallet
     */
    public function getTotalWalletBalance()
    {
        $wallets = $this->wallets;
        $totalBalance = 0;
        foreach ($wallets as $wallet) {
            $totalBalance += $wallet->balance;
        }

        return $totalBalance;
    }

    /**
     * Determine if the user can withdraw the given amount
     *
     * @param  integer  $amount
     *
     * @return boolean
     */
    public function canWithdraw($amount)
    {
        return $this->getWalletBalance() >= $amount;
    }

    /**
     * Retrieve the balance of this user's wallet
     *
     * @param  int  $type
     *
     * @return int
     */
    public function getWalletBalance($type = 1)
    {
        return $this->wallets->where('wallettype_id', $type)
            ->value('balance') ?: 0;
    }

    /**
     * Fail to move credits to this account
     *
     * @param  integer  $amount
     * @param  string  $type
     * @param  array  $meta
     */
    public function failDeposit($amount, $type = 'deposit', $meta = [])
    {
        $this->deposit($amount, $type, $meta, false);
    }

    /**
     * Move credits to this account
     *
     * @param  integer  $amount
     * @param  null  $walletType
     *
     * @return array
     */
    public function deposit($amount = 0, $walletType = null)
    {
        $walletType = $walletType ?: config('constants.WALLET_TYPE_MAIN');

        $wallet = $this->wallets->where('wallettype_id', $walletType)
            ->first();

        /*     if (!$wallet) {
                return $this->createAndDepositeInWallet($amount, $walletType, $wallet);
            }*/

        $result = $wallet->deposit($amount);

        if ($result) {
            return $this->respond(true, 'SUCCESSFUL', $wallet);
        }

        return $this->respond(false, 'CAN_NOT_UPDATE_WALLET', $wallet);
    }

    /**
     * @param  bool  $result
     * @param  string  $responseText
     * @param          $wallet
     *
     * @return array
     */
    private function respond(bool $result, string $responseText, $wallet): array
    {
        return [
            'result' => $result,
            'responseText' => $responseText,
            'wallet' => (isset($wallet)) ? $wallet->id : 0,
        ];
    }

    /**
     * Move credits from this account
     *
     * @param  integer  $amount
     * @param           $walletType
     *
     * @return array
     */
    public function forceWithdraw($amount, $walletType)
    {
        if (!isset($walletType)) {
            $walletType = config('constants.WALLET_TYPE_MAIN');
        }

        return $this->withdraw($amount, $walletType, false);
    }

    /**
     * Attempt to move credits from this account
     *
     * @param  integer  $amount
     * @param  null  $walletType
     *
     * @return array
     */
    public function withdraw($amount, $walletType = null)
    {
        $walletType = $walletType ?: config('constants.WALLET_TYPE_MAIN');

        $wallet = $this->wallets->where('wallettype_id', $walletType)
            ->first();

        if ($wallet) {
            $result = $wallet->withdraw($amount);

            if ($result) {
                return $this->respond(true, 'SUCCESSFUL', $wallet);
            }

            return $this->respond(false, 'failed to withdraw', $wallet);
        }

        $wallet = Wallet::create([
            'user_id' => $this->id,
            'wallettype_id' => $walletType,
        ]);
        return $this->respond(true, 'SUCCESSFUL', $wallet);

//        return $this->createAndDepositeInWallet($amount = 0, $walletType, $wallet);
    }

    /**
     * Returns the actual balance for this wallet.
     * Might be different from the balance property if the database is manipulated
     *
     * @return void balance
     */
    public function actualBalance()
    {
        //        $credits = $this->wallet->transactions() ->whereIn('type', ['deposit', 'refund']) ->sum('amount');
        //        $debits = $this->wallet->transactions() ->whereIn('type', ['withdraw', 'payout']) ->sum('amount');
        //        return $credits - $debits;
    }

    /**
     * @param $amount
     * @param $walletType
     * @param $wallet
     *
     * @return array
     */
    private function createAndDepositeInWallet($amount, $walletType, $wallet): array
    {
        $walletId = $this->storeWallet($walletType);

        if (!$walletId) {
            return $this->respond(false, 'CAN_NOT_CREATE_WALLET', $wallet);
        }

        $wallet = $this->depositWallet($amount, $walletId);

        return $this->respond(true, 'SUCCESSFUL', $wallet);
    }

    /**
     * @param $walletType
     *
     * @return WalletController|void
     */
    private function storeWallet($walletType)
    {
        return WalletRepo::insertNewRow([
            'user_id' => $this->id,
            'wallettype_id' => $walletType,
        ]);
    }

    /**
     * @param $amount
     * @param $walletId
     *
     * @return mixed
     */
    private function depositWallet($amount, $walletId)
    {
        $wallet = Wallet::find($walletId);
        $wallet->deposit($amount);

        return $wallet;
    }
}
