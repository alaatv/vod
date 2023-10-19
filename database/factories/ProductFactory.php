<?php

namespace Database\Factories;

use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Product::class;

    public function definition()
    {
        $images = [
            'https://sanatisharif.ir/image/4/338/338/A19_20190109163129.jpg',
            'https://sanatisharif.ir/image/4/338/338/A18_20190115104024.jpg',
            'https://sanatisharif.ir/image/4/338/338/poster-zist%20200%20%282%29_20190103162949.jpg',
            'https://sanatisharif.ir/image/4/338/338/A21_20190115104041.jpg',
            'https://sanatisharif.ir/image/4/338/338/A17_20190108110031.jpg',
            'https://sanatisharif.ir/image/4/338/338/Jozve%20Moqari_20190120152947.jpg',
            'https://sanatisharif.ir/image/4/338/338/A20_20190115104001.jpg'
        ];
        return [
            'name' => $this->faker->name,
            'basePrice' => rand(1000, 100000),
            'shortDescription' => $this->faker->sentence,
            'longDescription' => $this->faker->paragraph,
            'image' => $images[rand(0, 6)],
            'attributeset_id' => 3,
        ];
    }
}
