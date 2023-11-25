<?php

namespace Database\Seeders\Other;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavorableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contentIds = Content::pluck('id')->toArray();
        $setIds = Contentset::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();
        $data = [];
        foreach (array_rand($contentIds, 10) as $contentId) {
            $data[] = [
                'user_id' => 1,
                'favorable_id' => $contentId,
                'favorable_type' => 'App\Models\Content',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        foreach (array_rand($setIds, 10) as $setId) {
            $data[] = [
                'user_id' => 1,
                'favorable_id' => $setId,
                'favorable_type' => 'App\Models\Contentset',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        foreach (array_rand($productIds, 10) as $productId) {
            $data[] = [
                'user_id' => 1,
                'favorable_id' => $productId,
                'favorable_type' => 'App\Models\Product',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('favorables')->insert($data);
    }
}
