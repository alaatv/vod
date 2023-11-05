<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;


class File extends BaseModel
{
    protected $fillable = [
        'name',
        'uuid',
    ];

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'educationalcontent_file', 'file_id', 'content_id')
            ->withPivot('caption');
    }

    public function disks()
    {
        return $this->belongsToMany(Disk::class)
            ->orderBy('priority')
            ->withPivot('priority');
    }

    public function getUrl()
    {
        $fileRemotePath = '';
        $disk = $this->disks->first();
        if (!isset($disk)) {
            return action('Web\ErrorPageController@error404');
        }
        $diskAdapter = Storage::disk($disk->name)
            ->getAdapter();
        $diskType = class_basename($diskAdapter);
        $sftpRoot = config('constants.SFTP_ROOT');
        $dProtocol = config('constants.DOWNLOAD_SERVER_PROTOCOL');
        $dName = config('constants.PAID_SERVER_NAME');

        switch ($diskType) {
            case 'SftpAdapter' :
                //                $fileHost = $diskAdapter->getHost();
                $fileRoot = $diskAdapter->getRoot();
                $fileRemotePath = str_replace($sftpRoot, $dProtocol.$dName, $fileRoot);
                $fileRemotePath .= $this->name;
                break;
        }

        return $fileRemotePath;
    }

    public function getExtention()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }
}
