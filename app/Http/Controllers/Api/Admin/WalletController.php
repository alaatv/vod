<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateWalletRequest;
use App\Http\Requests\Admin\EditWalletRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Wallettype;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WalletController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return \App\Http\Resources\Wallet::collection(Wallet::query()->with(['user'])->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateWalletRequest  $request
     * @return JsonResponse
     */
    public function store(CreateWalletRequest $request)
    {
        $user = User::firstWhere('id', $request->get('user_id'));
        $wallettype = Wallettype::firstWhere('id', $request->get('wallettypes_id'));

        error(function () {
            return myAbort(Response::HTTP_BAD_REQUEST, 'این کیف پول برای کاربر منتخب ساخته شده است.');
        });

        $newWallet = Wallet::create([
            'user_id' => $user->id,
            'wallettype_id' => $wallettype->id,
        ]);

        return new \App\Http\Resources\Wallet($newWallet->fresh());
    }

    /**
     * Display the specified resource.
     * @param  Wallet  $wallet
     * @return JsonResponse
     */
    public function show(Wallet $wallet)
    {
        return new \App\Http\Resources\Wallet($wallet);
    }

    /**
     * @param  EditWalletRequest  $request
     * @param  Wallet  $wallet
     * @return JsonResponse
     */
    public function addCredit(EditWalletRequest $request, Wallet $wallet)
    {
        $credit = $request->get('balance', 0);
        $result = $wallet->deposit($credit);

        if ($result['result']) {
            return new \App\Http\Resources\Wallet($wallet);
        }

        //ToDo: flush cache
        return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Unexpected error');
    }
}
