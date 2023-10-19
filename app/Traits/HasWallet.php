<?php

namespace App\Traits;



use App\Models\Wallet;

trait HasWallet
{
    /**
     * Retrieve the balance of all of this user's wallet
     */
    protected $user_wallet_balance_cache;

    public function getTotalWalletBalance()
    {
        if (!is_null($this->user_wallet_balance_cache)) {
            return $this->user_wallet_balance_cache;
        }
        $this->user_wallet_balance_cache = (int) $this->wallets()->sum('balance');
        return $this->user_wallet_balance_cache;
    }

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
        $wallet = $this->wallets->where('wallettype_id', $type)->first();
        $balance = 0;
        if (isset($wallet)) {
            $balance = $wallet->balance;
        }

        return $balance;
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
        $done = false;
        if (!isset($walletType)) {
            $walletType = config('constants.WALLET_TYPE_MAIN');
        }
        $wallet = $this->wallets->where('wallettype_id', $walletType)->first();
        if (isset($wallet)) {
            /** @var Wallet $wallet */
            $result = $wallet->deposit($amount);
            if ($result['result']) {
                $responseText = 'SUCCESSFUL';
                $done = true;
            } else {
                $responseText = $result['responseText'];
            }
        } else {
            $wallet = Wallet::create([
                'user_id' => $this->id,
                'wallettype_id' => $walletType,
            ]);
            if (isset($wallet)) {
                $wallet->deposit($amount);
                $responseText = 'SUCCESSFUL';
                $done = true;
            } else {
                $responseText = 'CAN_NOT_CREATE_WALLET';
            }
        }

        return [
            'result' => $done,
            'responseText' => (isset($responseText)) ? $responseText : '',
            'wallet' => (isset($wallet)) ? $wallet->id : null,
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
     * @param  boolean  $shouldAccept
     *
     * @return array
     */
    public function withdraw($amount, $walletType = null, $shouldAccept = true)
    {
        $failed = true;
        $responseText = '';

        if (!isset($walletType)) {
            $walletType = config('constants.WALLET_TYPE_MAIN');
        }

        /** @var Wallet $wallet */
        $wallet = $this->wallets->where('wallettype_id', $walletType)->first();
        if (isset($wallet)) {
            $result = $wallet->withdraw($amount);
            if ($result['result']) {
                $failed = false;
            } else {
                $failed = true;
                $responseText = $result['responseText'];
            }
        } else {
            $wallet = Wallet::create([
                'user_id' => $this->id,
                'wallettype_id' => $walletType,
            ]);
            $failed = false;
            if (!isset($wallet)) {
                $failed = true;
                $responseText = 'CAN_NOT_CREATE_WALLET';
            }
        }

        if (!$failed) {
            $responseText = 'SUCCESSFUL';
        }

        return [
            'result' => !$failed,
            'responseText' => $responseText,
            'wallet' => (isset($wallet)) ? $wallet->id : 0,
        ];
    }

    /**
     * Returns the actual balance for this wallet.
     * Might be different from the balance property if the database is manipulated
     *
     * @return void balance
     */
    public function actualBalance()
    {
        //        $credits = $this->wallet->transactions()
        //            ->whereIn('type', ['deposit', 'refund'])
        //            ->sum('amount');
        //        $debits = $this->wallet->transactions()
        //            ->whereIn('type', ['withdraw', 'payout'])
        //            ->sum('amount');
        //        return $credits - $debits;
    }
}
