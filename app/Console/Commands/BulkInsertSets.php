<?php

namespace App\Console\Commands;

use App\Classes\Uploader\Uploader;
use App\Exports\DefaultClassExport;
use App\Models\BatchContentInsert;
use App\Models\Contentset;
use App\Models\Product;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use SplFileInfo;

class BulkInsertSets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:add-sets {fileName} {productId} {insertId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command adds content sets to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $disk = config('disks.BATCH_CONTENT_INSERT');
        $excelHeaders = ['ردیف', 'اسم کوتاه', 'اسم بلند', 'لینک عکس', 'ایدی دبیر', 'آیدی ست'];
        $excelData = [];
        $splFileInfoObject = new SplFileInfo(storage_path('/app/public/general/'.$this->argument('fileName')));
        $data = Excel::toArray(new SetImport(), $splFileInfoObject);
        $product = Product::find($this->argument('productId'));
        $excelRowNumber = 1;
        foreach ($data[0] as $row) {
            if (array_search($row, $data[0]) == 0) {
                continue;
            }
            if (is_null($row[1])) {
                continue;
            }
            $set = Contentset::create([
                'small_name' => $row[1],
                'name' => $row[2],
                'photo' => basename($row[3]),
                'author_id' => $row[4],
                'enable' => 0,
                'display' => 0,
            ]);
            $product->sets()->attach([
                $set->id => ['order' => $excelRowNumber]
            ]);
            $excelData[] = [$excelRowNumber, $row[1], $row[2], $row[3], $row[4], $set->id];
            $excelRowNumber++;
        }
        $ext = substr($this->argument('fileName'), strpos($this->argument('fileName'), '.') + 1);
        $fileNameWithoutExtension = substr($this->argument('fileName'), 0, strpos($this->argument('fileName'), '.'));
        $downloadableFileName = $fileNameWithoutExtension.now()->timestamp.'.'.$ext;
        Excel::store(new DefaultClassExport(collect($excelData), $excelHeaders),
            'batchContentInsert/'.$downloadableFileName, $disk);
        Uploader::delete(config('disks.GENERAL'), $this->argument('fileName'));
        BatchContentInsert::find($this->argument('insertId'))->update([
            'downloadable_file' => $downloadableFileName,
            'status' => 'success',
        ]);
        return 0;
    }
}

class SetImport implements ToArray
{
    public function array(array $array)
    {
    }
}
