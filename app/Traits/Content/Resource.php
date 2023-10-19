<?php


namespace App\Traits\Content;



use App\Http\Resources\Author;
use App\Http\Resources\ContentTimePointAPI;
use App\Http\Resources\PamphletFile;
use App\Http\Resources\SetInContent;
use App\Http\Resources\VideoFile;
use App\Models\Content;

trait Resource
{

    public function getContentFile()
    {
        if (!$this->resource instanceof Content) {
            return [];
        }

        $file = $this->file;

        $videoFileCollection = optional($file)->get('video') ?? collect();
        $pamphletFileCollection = optional($file)->get('pamphlet') ?? collect();
        $voiceFileCollection = optional($file)->get('voice') ?? collect();
        return [
            'video' => $this->when(isset($videoFileCollection), function () use ($videoFileCollection) {
                return $videoFileCollection->count() > 0 ? VideoFile::collection($videoFileCollection) : null;
            }),
            'pamphlet' => $this->when(isset($pamphletFileCollection), function () use ($pamphletFileCollection) {
                return $pamphletFileCollection->count() > 0 ? PamphletFile::collection($pamphletFileCollection) : null;
            }),
            'voice' => $this->when(isset($voiceFileCollection), function () use ($voiceFileCollection) {
                return $voiceFileCollection->count() > 0 ? PamphletFile::collection($voiceFileCollection) : null;
            }),
        ];
    }

    public function getContentExplicitFile()
    {
        if (!$this->resource instanceof Content) {
            return [
            ];
        }
        $file = $this->file_for_app;
        $videoFileCollection = optional($file)->get('video') ?? collect();
        $pamphletFileCollection = optional($file)->get('pamphlet') ?? collect();
        $voiceFileCollection = optional($file)->get('voice') ?? collect();
        return [
            'video' => $this->when(isset($videoFileCollection), function () use ($videoFileCollection) {
                return $videoFileCollection->count() > 0 ? VideoFile::collection($videoFileCollection) : null;
            }),
            'pamphlet' => $this->when(isset($pamphletFileCollection), function () use ($pamphletFileCollection) {
                return $pamphletFileCollection->count() > 0 ? PamphletFile::collection($pamphletFileCollection) : null;
            }),
            'voice' => $this->when(isset($voiceFileCollection), function () use ($voiceFileCollection) {
                return $voiceFileCollection->count() > 0 ? PamphletFile::collection($voiceFileCollection) : null;
            }),
        ];
    }

    public function hasFile(): bool
    {
        if (!$this->resource instanceof Content) {
            return false;
        }
        return $this->contenttype_id == config('constants.CONTENT_TYPE_PAMPHLET') || $this->contenttype_id == config('constants.CONTENT_TYPE_VIDEO');
    }

    private function getFiles($isWithEncryption = false)
    {
        if (!$this->resource instanceof Content) {
            return [
            ];
        }

        if ($isWithEncryption) {
            return $this->file;
        }

        return $this->file_for_app;

    }

    private function getIsFavored()
    {
        if (!isset($this->isFavored)) {
            return true;
        }

        return $this->isFavored;
    }

    private function getSetInContent()
    {
        if (is_null($this->contentset_id)) {
            return null;
        }

        return new SetInContent($this->set);
    }

    private function getAuthor()
    {
        if (!isset($this->author)) {
            //Note:It is like this because of android ! please don't change it
            return [
                'id' => null,
                'first_name' => null,
                'last_name' => null,
                'photo' => null,
            ];
        }
        return new Author($this->user);
    }

    private function getTimePoints()
    {
        $timepoints = $this->times;
        if ($timepoints->isEmpty()) {
            return null;
        }

        return ContentTimePointAPI::collection($timepoints);
    }

    private function getTimePointsFavoredByUser($contentId)
    {
        $timepoints = auth()->user()->getActiveFavoredTimepointsForContent($contentId);
        if ($timepoints->isEmpty()) {
            return null;
        }

        return ContentTimePointAPI::collection($timepoints);
    }

    private function getType()
    {
//        return New Contenttype($this->contenttype);
        return $this->contenttype_id;
    }
}
