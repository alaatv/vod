<?php

namespace Database\Factories;

use App\Contractor;
use App\Models\Contractor;
use App\Models\Productvoucher;
use App\Product;
use App\Productvoucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductvoucherFactory extends Factory
{

    protected $model = Productvoucher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $contractors = Contractor::all()->pluck('id');
        $products = Product::all()->random(3)->pluck('id')->toArray();
        $packageNames = ['test1', 'test1', 'test3'];
        return [
            'contractor_id' => $this->faker->randomElement($contractors),
            'package_name' => $this->faker->randomElement($packageNames),
            'products' => $products,
            'code' => $this->faker->regexify('h-[A-Za-z0-9]{4}'),
            'expirationdatetime' => now()->addMonths(random_int(1, 5)),
            'enable' => random_int(0, 1),
            'description' => $this->faker->text(100),
        ];
    }
}
