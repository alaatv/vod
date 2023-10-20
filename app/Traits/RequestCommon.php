<?php namespace App\Traits;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

trait RequestCommon
{
    /**
     * Converts a request to an Ajax request
     *
     * @param  FormRequest  $request
     *
     * @return void
     */
    public static function convertRequestToAjax(FormRequest &$request): void
    {
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);
    }

    /**
     * @param  FormRequest  $request
     *
     * @return bool
     */
    public function isRequestFromApp(FormRequest $request): bool
    {
        $isApp = (strlen(strstr($request->header('User-Agent'), 'Alaa')) > 0) ? true : false;

        return $isApp;
    }

    /**
     * Copy source request in to the new request
     *
     * @param  FormRequest  $sourceRequest
     * @param  FormRequest  $newRequest
     *
     * @return void
     */
    public function copyRequest(FormRequest $sourceRequest, FormRequest &$newRequest): void
    {
        $newRequest->merge($sourceRequest->all());
        $user = $sourceRequest->user();
        if (!isset($user)) {
            return;
        }
        $newRequest->setUserResolver(function () use ($user) {
            return $user;
        });
    }

    /**
     * @param  FormRequest  $request
     * @param               $dependencyIndex
     * @param               $secondaryIndex
     */
    private function checkOffsetDependency(FormRequest $request, $dependencyIndex, $secondaryIndex): void
    {
        $dependencyValue = $request->get($dependencyIndex);
        if (!isset($dependencyValue)) {
            $request->offsetUnset($secondaryIndex);
        }
    }

    private function storeRequestFile(FormRequest $request, string $inputIndex, string $disk): ?string
    {
        $file = $this->getRequestFile($request->all(), $inputIndex);
        if ($file !== false) {
            $storeMethod = (strrchr($disk, 'Sftp') !== false) ? 'storePhoto' : 'storeFileToLocalCdn';
            return $this->$storeMethod($file, $disk);
        }

        return null;
    }

    /**
     * @param  array  $data
     * @param         $index
     *
     * @return array|bool|UploadedFile|mixed|null
     */
    public function getRequestFile(array $data, string $index)
    {
        $hasFile = true;

        if (array_key_exists($index, $data)) {
            $file = $data[$index];
            if (!is_file($file)) {
                $hasFile = false;
            }
        } else {
            $hasFile = false;
        }

        if ($hasFile) {
            return $file;
        }
        return $hasFile;

    }

    private function storeRequestFileMinio(
        FormRequest $request,
        string $inputIndex,
        string $disk,
        string $path = ''
    ): ?string {
        $file = $this->getRequestFile($request->all(), $inputIndex);
        return $this->storeFileMinio($file, $disk, $path);
    }

    /**
     * @param  array|bool|UploadedFile|mixed|null  $file
     * @param  string  $disk
     * @param  string  $path
     * @return string|null
     */
    private function storeFileMinio($file, string $disk, string $path): ?string
    {
        if ($file !== false) {
            return $this->storePhotoMinio($file, $disk, $path);
        }

        return null;
    }

    private function requestHasNull(FormRequest $request, string $index)
    {
        return $request->has($index) && is_null($request->get($index));
    }

    private function getUserIdFromRequestBody(Request $request)
    {
        $requestSegments = $request->segments();
        $userId = end($requestSegments);
        if ((int) $userId == 0) {
            $userId = null;
        }

        return nullable($userId);
    }
}
