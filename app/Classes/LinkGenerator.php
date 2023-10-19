<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-08-23
 * Time: 15:22
 */

namespace App\Classes;

use App\Adapter\AlaaSftpAdapter;
use App\Classes\Uploader\Uploader;
use App\Models\Content;
use App\Models\File;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use stdClass;

/**
 * Class LinkGenerator
 *
 * @package App\Classes
 */
class LinkGenerator
{
    protected const DOWNLOAD_CONTROLLER_NAME = "Web\\HomeController@newDownload";

    protected $uuid;

    protected $disk;

    protected $url;

    protected $fileName;

    protected $quality;

    /**
     * LinkGenerator constructor.
     *
     * @param $file
     */
    public function __construct(stdClass $file)
    {
        $this->setDisk($file->disk)
            ->setUuid($file->uuid)
            ->setQuality($file->res)
            ->setUrl($file)
            ->setFileName($file->fileName);
    }

    /**
     * @param  mixed  $fileName
     *
     * @return LinkGenerator
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    private function setUrl($file)
    {
        if (isset($file->url)) {
            $this->url = $file->url;
        }

        return $this;
    }

    private function setQuality(?string $quality): static
    {
        $this->quality = isset(Content::QUALITY_MAP[$quality]) ? Content::QUALITY_MAP[$quality] : '';

        return $this;
    }

    /**
     * @param  mixed  $uuid
     *
     * @return LinkGenerator
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @param  mixed  $disk
     *
     * @return LinkGenerator
     */
    public function setDisk($disk)
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * LinkGenerator constructor.
     *
     * @param $uuid
     * @param $disk
     * @param $url
     * @param $fileName
     *
     * @return LinkGenerator
     */
    public static function create($uuid, $disk, $url, $fileName)
    {
        $input = new stdClass();
        //        dd("0");
        $input->disk = null;
        //        dd("1");
        if (isset($disk)) {
            $input->disk = $disk;
        } else {
            if (isset($uuid)) {
                $input->disk = self::findDiskNameFromUUID($uuid);
            }
        }
        //        dd("2");
        $input->uuid = $uuid;
        $input->url = $url;
        $input->fileName = $fileName;

        //        dd($input);
        return new LinkGenerator($input);
    }

    /**
     * @param $uuid
     *
     * @return null | string
     */
    private static function findDiskNameFromUUID($uuid)
    {
        $file = File::where('uuid', $uuid)
            ->get();
        if (!($file->isNotEmpty() && $file->count() == 1)) {

            return null;
        }
        $file = $file->first();
        if ($file->disks->isNotEmpty()) {
            return $file->disks->first()->name;
        }


        return null;
    }

    /**
     * @param  Content  $content
     * @param  Closure  $closure
     * @param  bool  $encryptedLink
     *
     * @return array|null|string
     * @throws Exception
     */
    public function getLinks(Content $content, Closure $closure, $encryptedLink = false)
    {
        if (isset($this->url)) {
            return $this->url;
        }

        if (!isset($this->disk, $this->fileName)) {
            throw new Exception("DiskName and FileName should be set \n File uuid=".$this->uuid);
        }

        $fileName = $closure($this->fileName, $this->quality);

        $url = Uploader::url($this->disk, $fileName, false);

        if (!$encryptedLink) {
            return $url;
        }

        $data = encrypt([
            'url' => $url,
            'data' => ['content_id' => $content->id],
        ]);

        return action(self::DOWNLOAD_CONTROLLER_NAME, $data);
    }

    /**
     * @param  int  $isFree
     *
     * @return array|null|string
     * @throws Exception
     */
    public function getLinksForApp(Content $content, Closure $closure, $encryptedLink = false)
    {
        if (isset($this->url)) {
            return $this->url;
        }

        if (!isset($this->disk, $this->fileName)) {
            throw new Exception("DiskName and FileName should be set \n File uuid=".$this->uuid);
        }

        $fileName = $closure($this->fileName, $this->quality);

        $url = Uploader::url($this->disk, $fileName, false);

        if (!$encryptedLink) {
            return $url;
        }

        return getSecureUrl($url, 1);

    }

    private function fetchUrl(AlaaSftpAdapter $diskAdapter, $fileName)
    {
        try {
            return $diskAdapter->getUrl($fileName);
        } catch (Exception $exception) {
            Log::error(json_encode([
                'message' => 'fetchUrl failed!',
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'fileName' => $fileName,
            ], JSON_UNESCAPED_UNICODE));

            return null;
        }
    }

    /**
     * @return array
     */
    private function stream()
    {
        $f = $this;
        $fs = Storage::disk($f->disk)
            ->getDriver();
        try {
            $stream = $fs->readStream($f->fileName);
            $result = [
                'read-stream' => $stream,
            ];
        } catch (FileNotFoundException $e) {
            $result = [
                'read-stream' => null,
                'exception' => $e,
            ];
        }

        return $result;
    }

    private function makeFreeContentFileName($contentSetId, $contenttypeId)
    {
        if ($contenttypeId == config('constants.CONTENT_TYPE_VIDEO')) {
            $qualitySubFolder = $this->quality ? $this->quality.'/' : '';
            return $contentSetId.'/'.$qualitySubFolder.$this->fileName;
        }

        return $this->fileName;
    }

    private function makePaidContentFileName()
    {
        return str_ireplace('/paid/', '', $this->fileName);
    }


}
