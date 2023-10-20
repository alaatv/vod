<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 17:20
 */

namespace App\Traits\User;

trait WalletTrait
{
    public function getWallet()
    {
        $wallets = $this->wallets->loadMissing('walletType');
        $walletsArrayForReturn = [];
        foreach ($wallets as $wallet) {
            $walletsArrayForReturn[] = [
                'id' => $wallet->id,
                'name' => $wallet->walletType->name,
                'hint' => $wallet->walletType->displayName,
                'balance' => $this->getWalletBalance($wallet->wallettype_id),
            ];
        }

        return $walletsArrayForReturn ?: null;
    }
}
