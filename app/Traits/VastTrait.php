<?php

namespace App\Traits;

use App\Classes\Uploader\Uploader;
use App\Models\Vast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use SimpleXMLElement;
use stdClass;

trait VastTrait
{

    public function generateVastXmlFileName(): string
    {
        return 'vast_'.$this->nowTimestamp().'.xml';
    }

    private function requestOffsetSet(Request $request, array $uploadedVideoNames, string $vastXmlFileName)
    {
        $request->offsetSet('is_default', $request->has('is_default'));
        $request->offsetSet('enable', $request->has('enable'));
        $request->offsetSet('files', json_encode($this->makeContentFilesArray($uploadedVideoNames)));
        $request->offsetSet('file_url', $vastXmlFileName);
    }

    private function makeContentFilesArray(array $videos): array
    {
        $files = [];
        foreach ($videos as $quality => $name) {
            if (strlen($name) > 0) {
                $files[] = $this->makeVideoFileStdClass($name, $this->getQualityDisk($quality), $quality);
            }
        }
        return $files;
    }

    private function makeVideoFileStdClass(string $filename, string $disk, string $res): stdClass
    {
        $file = new stdClass();
        $file->uuid = Str::uuid()->toString();
        $file->name = $filename;
        $file->res = $res;
        $file->caption = Vast::VIDEO_QUALITY_CAPTIONS[$res];
        $file->type = 'video';
        $file->size = null;
        $file->disk = $disk;
        $file->ext = pathinfo($filename, PATHINFO_EXTENSION);

        return $file;
    }

    public function getQualityDisk(string $quality)
    {
        $disk = Vast::QUALITY_DISK_MAP[$quality];
        return config("disks.{$disk}");
    }

    private function generationXmlFile(
        string $vastXmlFileName,
        array $videoNames = [],
        string $moreInfoLink = null,
        string $clickId = null,
        string $clickName = null
    ): bool {

        $vastXmlFilePath = $this->makeVastXmlFilePath($vastXmlFileName);

        if (!file_exists($vastXmlFilePath)) {
            $handle = fopen($vastXmlFilePath, 'w');
            fclose($handle);
        }

        if (!$this->fillVastXmlFile($vastXmlFilePath, $videoNames, $moreInfoLink, $clickId, $clickName)) {
            return false;
        }


        Artisan::call('cache:clear');
        Uploader::put($vastXmlFilePath, config('disks.VAST_XML_MINIO'), fileName: $vastXmlFileName);

        // 5. Remove raw vast xml file from public
        unlink($vastXmlFilePath);

        return true;
    }

    public function makeVastXmlFilePath(string $vastXmlFileName = 'vast.xml'): string
    {
        return $this->vastXmlFileBasePath().DIRECTORY_SEPARATOR.$vastXmlFileName;
    }

    public function vastXmlFileBasePath(): string
    {
        return public_path().DIRECTORY_SEPARATOR.'acm'.DIRECTORY_SEPARATOR.'videojs';
    }

    private function fillVastXmlFile(
        string $vastXmlFilePath,
        array $videoNames = [],
        string $moreInfoLink = null,
        string $clickId = null,
        string $clickName = null
    ) {
        // TODO: The following path works well in the Windows local environment but not in the Ubuntu local environment.
        $vastTemplateXmlFilePath = $this->vastTemplateXmlFilePath();

        if (!file_exists($vastTemplateXmlFilePath)) {
            return false;
        }

        $xml = new SimpleXMLElement(file_get_contents($vastTemplateXmlFilePath));

        // Notice: Add the attribute xmlns:xs to VAST tag manually. I don't know why the attribute xmlns:xs aren't added to Vast tag automatically!!!
        $xml->addAttribute('xmlns\:xs', 'http://www.w3.org/2001/XMLSchema');

        // Add MediaFile tags to output xml file.
        $linear = $xml->Ad->InLine->Creatives->Creative->Linear;
        foreach ($videoNames as $quality => $name) {
            if (strlen($name) > 0) {
                $mediaFile = $linear->MediaFiles;
                $this->addMediaFile($mediaFile, Uploader::url($this->getQualityDisk($quality), $name));
            }

        }

        if (!empty($moreInfoLink)) {
            // Add ClickThrough tags to output xml file.
            $linear->addChild('VideoClicks', '');
            $videoClicks = $linear->VideoClicks;
            $this->addClickThrough($videoClicks, $moreInfoLink, $clickId, $clickName);
        }

        // Write to output vast xml file.
        $xml->asXML($vastXmlFilePath);

        $this->changeSpecialCharacter($vastXmlFilePath);

        return true;
    }

    public function vastTemplateXmlFilePath(): string
    {
        return $this->makeVastXmlFilePath(Vast::VAST_TEMPLATE_XML);
    }

    private function addMediaFile(&$mediaFile, $videoPath)
    {
        $mediaFile = $mediaFile->addChild('MediaFile', "<![CDATA[{$videoPath}]]>");
        $mediaFile->addAttribute('id', '5244');
        $mediaFile->addAttribute('delivery', 'progressive');
        $mediaFile->addAttribute('type', 'video/mp4');
        $mediaFile->addAttribute('bitrate', '1000');
        $mediaFile->addAttribute('width', '854');
        $mediaFile->addAttribute('height', '480');
        $mediaFile->addAttribute('minBitrate', '700');
        $mediaFile->addAttribute('maxBitrate', '1500');
        $mediaFile->addAttribute('scalable', '1');
        $mediaFile->addAttribute('maintainAspectRatio', '1');
        $mediaFile->addAttribute('codec', 'H.264');
        $mediaFile->addAttribute('label', 'معمولی');
        $mediaFile->addAttribute('res', '480p');
    }

    private function addClickThrough(&$clickThrough, $moreInfoLink, $clickId, $clickName)
    {
        $clickThrough = $clickThrough->addChild('ClickThrough', "<![CDATA[{$moreInfoLink}]]>");
        $clickThrough->addAttribute('id', $clickId);
        $clickThrough->addAttribute('name', $clickName);
        $clickThrough->addAttribute('creative', 'vast');
        $clickThrough->addAttribute('position', '0');
    }

    private function changeSpecialCharacter(string $vastXmlFilePath)
    {
        // Read the entire string
        $str = file_get_contents($vastXmlFilePath);

        // Replace special character
        $string = '&lt;';
        $AlternateString = '<';
        $str = str_replace($string, $AlternateString, $str);
        $string = '&gt;';
        $AlternateString = '>';
        $str = str_replace($string, $AlternateString, $str);

        // Write the entire string
        file_put_contents($vastXmlFilePath, $str);
    }

    private function updateVideos(Vast $vast, array $videos)
    {
        $uploadedVideoNames = $this->uploadVastVideos($videos);

        foreach ($vast->files as $file) {
            $uploadedVideoNames[$file->res] = isset($uploadedVideoNames[$file->res]) ? $uploadedVideoNames[$file->res] : $file->name;
        }

        return $uploadedVideoNames;
    }

    private function uploadVastVideos(array $videos): array
    {
        $uploadedVideoNames = [];
        foreach ($videos as $quality => $file) {
            $uploadedFileName = Uploader::put($file, config('disks.'.Vast::QUALITY_DISK_MAP[$quality]));
            $uploadedVideoNames[$quality] = $uploadedFileName;
        }
        return $uploadedVideoNames;
    }
}
