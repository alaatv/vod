<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\Productvoucher;
use Illuminate\Console\Command;

class FillCouponColumnOnProductvoucher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:productvoucher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill Coupon Column On Productvoucher';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $productVouchers = Productvoucher::query()->whereNotNull('package_name')->get();
        $bar = $this->output->createProgressBar($productVouchers->count());
        $packageConsts = [
            'godar_riyazi' => Coupon::HEKMAT_50_COUPON_ID,
            'godar_tajrobi' => Coupon::HEKMAT_50_COUPON_ID,
            'taftan_riyazi' => Coupon::HEKMAT_50_COUPON_ID,
            'taftan_tajrobi' => Coupon::HEKMAT_50_COUPON_ID,
            'arash_tajrobi' => Coupon::HEKMAT_40_COUPON_ID,
            'arash_riyazi' => Coupon::HEKMAT_40_COUPON_ID,
            'arash_ensani' => Coupon::HEKMAT_40_COUPON_ID,
            'rahe_abrisham' => Coupon::HEKMAT_40_COUPON_ID,
            'rahe_abrisham1400' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_riyazi' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_tajrobi' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_omoomi' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_ph_tol' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_ph_kazer' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_ch' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_riyazi_r' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_riyazi_t' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_z' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_ad' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_ar' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_en' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_di' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrisham1401_omoomi_r' => Coupon::HEKMAT_40_COUPON_ID,
            'taftan1400_tajrobi' => Coupon::HEKMAT_40_COUPON_ID,
            'taftan1400_riyazi' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrishamPro1402_omoomi' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrishamPro1402_riyazi' => Coupon::HEKMAT_40_COUPON_ID,
            'raheAbrishamPro1402_tajrobi' => Coupon::HEKMAT_40_COUPON_ID,
            '110konkur1402_tajrobi' => Coupon::HEKMAT_40_COUPON_ID,
            '110konkur1402_ensani' => Coupon::HEKMAT_40_COUPON_ID,
            '110konkur1402_riyazi' => Coupon::HEKMAT_40_COUPON_ID,
        ];
        foreach ($productVouchers as $productVoucher) {
            if ($coupon = Coupon::find($packageConsts[$productVoucher->package_name])) {
                $productVoucher->update(['coupon_id' => $coupon->id]);
            }

            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info('Product Voucher Coupon_id Updated');
    }
}
