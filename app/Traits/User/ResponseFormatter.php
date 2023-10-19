<?php


namespace App\Traits\User;


trait ResponseFormatter
{
    /**
     * @param  int  $resultCode
     * @param  string  $resultText
     *
     * @return array
     */
    private function makeErrorResponse(int $resultCode, string $resultText): array
    {
        return [
            'error' => [
                'code' => $resultCode,
                'message' => $resultText,
            ],
        ];
    }
}
