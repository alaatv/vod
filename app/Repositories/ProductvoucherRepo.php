<?php


namespace App\Repositories;


use App\Models\Productvoucher;
use Carbon\Carbon;

class ProductvoucherRepo extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Productvoucher::class;
    }

    public static function findVoucherByCode(?string $code)
    {
        return self::initiateQuery()->where('code', $code);
    }

    public static function disableVoucher(Productvoucher $voucher)
    {
        return $voucher->update([
            'enable' => 0,
        ]);
    }

    public static function all()
    {
        return self::initiateQuery()->with(['user', 'contractor']);
    }

    public static function filter(
        string|null $since,
        string|null $till,
        ?int $company = 0,
        ?int $expire = 0,
        string $search = null
    ) {
        $vouchers = self::initiateQuery();
        if ($search) {
            $vouchers = $vouchers->search($search);
        }
        if ($company) {
            $vouchers = $vouchers->where('contractor_id', $company);
        }
        if ($since && $till) {
            if ($since == $till) {
                $till = Carbon::parse($till)->addDay()->toDateString();
            }
            $vouchers = $vouchers->whereBetween('created_at', [$since, $till,]);
        }
        if (!$expire) {
            return $vouchers;
        }

        if ($expire == 1) {
            $vouchers = $vouchers->where('expirationdatetime', '>=', Carbon::now());
        } else {
            $vouchers = $vouchers->where('expirationdatetime', '<', Carbon::now());
        }

        return $vouchers;
    }

    public static function create(array $data): ?Productvoucher
    {
        return Productvoucher::create($data);
    }
}
