<?php

namespace App\Console\Commands;

use App\Classes\Uploader\Uploader;
use App\Exports\DefaultClassExport;
use App\Models\BatchContentInsert;
use App\Models\Content;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use SplFileInfo;

class BulkInsertContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:add-contents {fileName} {productId} {insertId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command adds contents to database';

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
        $disk = config('disks.BATCH_CONTENT_INSERT');
        $excelHeaders = ['شماره درس', 'نام', 'توضیح', 'آیدی دبیر', 'نوع محتوا', 'ترتیب', 'آیدی محتوا'];
        $excelData = [];
        $splFileInfoObject = new SplFileInfo(storage_path('/app/public/general/'.$this->argument('fileName')));
        $data = Excel::toArray(new ContentImport(), $splFileInfoObject);
        foreach ($data as $sheet) {
            foreach ($sheet as $row) {
                if (array_search($row, $sheet) == 0) {
                    continue;
                }
                if (is_null($row[0])) {
                    continue;
                }
                $contentSetId = $row[0];
                $contentName = $row[1];
                $contentDescription = $row[2];
                $contentAuthorId = $row[3];
                $contentSessionNumber = $row[5];
                $productId = $this->argument('productId');
                $fileName = $contentSetId.str_pad($contentSessionNumber, 3, '0',
                        STR_PAD_LEFT).makeRandomOnlyAlphabeticalString(4).'.mp4';
                $contentFiles = [
                    [
                        'uuid' => (string) Str::uuid(),
                        'disk' => 'productFileSFTP',
                        'url' => null,
                        'fileName' => DIRECTORY_SEPARATOR.'paid'.DIRECTORY_SEPARATOR.$productId.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.'HD_720p'.DIRECTORY_SEPARATOR.$fileName,
                        'size' => null,
                        'caption' => 'کیفیت عالی',
                        'res' => '720p',
                        'type' => 'video',
                        'ext' => 'mp4'
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'disk' => 'productFileSFTP',
                        'url' => null,
                        'fileName' => DIRECTORY_SEPARATOR.'paid'.DIRECTORY_SEPARATOR.$productId.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.'hq'.DIRECTORY_SEPARATOR.$fileName,
                        'size' => null,
                        'caption' => 'کیفیت بالا',
                        'res' => '480p',
                        'type' => 'video',
                        'ext' => 'mp4'
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                        'disk' => 'productFileSFTP',
                        'url' => null,
                        'fileName' => DIRECTORY_SEPARATOR.'paid'.DIRECTORY_SEPARATOR.$productId.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.'240p'.DIRECTORY_SEPARATOR.$fileName,
                        'size' => null,
                        'caption' => 'کیفیت متوسط',
                        'res' => '240p',
                        'type' => 'video',
                        'ext' => 'mp4'
                    ],
                ];
                $contetThumbnail = [
                    'uuid' => (string) Str::uuid(),
                    'disk' => 'contentThumbnailMinio',
                    'fileName' => $fileName,
                    'size' => null,
                    'caption' => null,
                    'res' => null,
                    'type' => 'thumbnail',
                    'ext' => 'jpg'
                ];
                $content = new Content();
                $content->setRawAttributes([
                    'enable' => 0,
                    'display' => 0,
                    'isFree' => 0,
                    'contenttype_id' => 8,
                    'contentset_id' => $contentSetId,
                    'content_status_id' => 3,
                    'template_id' => 1,
                    'file' => json_encode($contentFiles),
                    'thumbnail' => json_encode($contetThumbnail),
                    'validSince' => now()->toDateTimeString(),
                    'order' => $contentSessionNumber,
                    'author_id' => $contentAuthorId,
                    'name' => $contentName,
                    'description' => $contentDescription,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ]);
                $content->save();
                $excelData[] = [
                    $contentSetId, $contentName, $contentDescription, $contentAuthorId, $row[4], $contentSessionNumber,
                    $content->id
                ];
            }
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

class ContentImport implements ToArray
{
    public function array(array $array)
    {
    }
}
