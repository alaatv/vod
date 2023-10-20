<?php

namespace App\Http\Controllers\Api\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVoucherByCompany;
use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Http\Resources\Admin\ProductvoucherResource;
use App\Models\Productvoucher;
use App\Repositories\ProductvoucherRepo;
use App\Traits\DateTrait;
use Illuminate\Http\Response;


class VoucherManagementController extends Controller
{

    use DateTrait;


    public function __construct()
    {
        $this->middleware('role:'.config('constants.ROLE_VOUCHER_MANAGER'));

    }

    public function store(StoreVoucherRequest $storeVoucherRequest)
    {
        $voucher = ProductvoucherRepo::create($storeVoucherRequest->validated());
        return (new ProductvoucherResource($voucher))->response();
    }


    public function show(Productvoucher $productVoucher)
    {
        return (new ProductvoucherResource($productVoucher))->response();
    }


    public function update(UpdateVoucherRequest $updateVoucherRequest, Productvoucher $productVoucher)
    {
        $data = $updateVoucherRequest->validated();
        $data['coupon_id'] = 6474;
        $productVoucher->update($data);
        return (new ProductvoucherResource($productVoucher))->response();
    }

    public function destroy(Productvoucher $productVoucher)
    {
        $productVoucher->delete();
        return response()->json(['message' => 'ok'], Response::HTTP_OK);
    }

    public function createVoucherByCompany(CreateVoucherByCompany $request)
    {
        $codes = '';
        for ($i = 1; $i <= $request->validated()['count']; $i++) {
            do {
                $voucher = 'h-'.rand(1000, 9999);
            } while (!is_null(Productvoucher::where('code',
                $voucher)->first())); //TODO : count queries for every check -- can be refactored

            $attributes = array_merge($request->validated(), [
                'code' => $voucher,
                'description' => route('web.voucher.submit', ['code' => $voucher]),
            ]);
            ProductvoucherRepo::create($attributes);

            $codes = $codes.$voucher.' , ';
        }

        return response()->json(['data' => ['codes' => $codes]], Response::HTTP_OK);
    }

}
