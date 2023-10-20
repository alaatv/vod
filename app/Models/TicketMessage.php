<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use App\Traits\GetTehranTimeZoneTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketMessage extends BaseModel
{
    use HasFactory;
    use GetTehranTimeZoneTrait;

    protected $table = 'ticketMessages';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
        'files',
        'is_private',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::Class);
    }

    public function User()
    {
        return $this->belongsTo(User::Class);
    }

    public function logs()
    {
        return $this->hasMany(TicketActionLog::Class, 'ticket_message_id', 'id');
    }

    public function getLogsOrderbyTimeAttribute()
    {
        return $this->logs->sortByDesc('created_at');
    }

    /**
     * @param  array|null  $input
     *
     * @return void
     */
    public function setFilesAttribute(array $input = null)
    {
        if (is_null($input)) {
            $this->attributes['files'] = null;
        } else {
            $this->attributes['files'] = json_encode($input, JSON_UNESCAPED_UNICODE);
        }
    }

    public function getFilesAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        return json_decode($value);
    }

    public function getPhotoAttribute()
    {
        $photos = optional($this->files)->photos;

        if (!isset($photos[0]) || !isset($photos[0]->url) || empty($photos[0]->url)) {
            return null;
        }

        return Uploader::url(config('disks.TICKET_PHOTO_MINIO'), $photos[0]->url);
    }

    public function getFileAttribute()
    {
        $files = optional($this->files)->file;

        if (!isset($files[0]) || !isset($files[0]->url) || empty($files[0]->url)) {
            return null;
        }

        return Uploader::url(config('disks.TICKET_FILE_MINIO'), $files[0]->url);
    }

    public function getVoiceAttribute()
    {
        $voices = optional($this->files)->voices;

        if (!isset($voices[0]) || !isset($voices[0]->url) || empty($voices[0]->url)) {
            return null;
        }

        return Uploader::url(config('disks.TICKET_VOICE_MINIO'), $voices[0]->url);

    }

    public function getRowImageAttribute()
    {
        $photos = optional($this->files)->photos;

        return isset($photos) ? $photos[0]->url : '';
    }

    public function getRowVoiceAttribute()
    {
        $voices = optional($this->files)->voices;
        return isset($voices) ? $voices[0]->url : '';
    }
}
