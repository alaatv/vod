<?php

namespace App\Imports;

use App\Models\PhoneBook;
use App\Models\PhoneBook;
use App\Models\PhoneNumber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PhoneNumberImport implements ToCollection, WithChunkReading, ShouldQueue
{
    use Importable;

    private int $phoneBookId;

    public function __construct(int $phoneBookId)
    {
        $this->phoneBookId = $phoneBookId;
    }

    public function collection(Collection $rows)
    {
        $phoneBook = PhoneBook::find($this->phoneBookId);
        $phoneNumbersId = [];

        foreach ($rows as $row) {
            $phoneNumber = PhoneNumber::firstOrCreate([
                'number' => $row[0],
                'provider_id' => $this->getUserMobileOperator($row[0]),
            ]);

            $phoneNumbersId[] = $phoneNumber->id;
        }

        $phoneBook->phoneNumbers()->syncWithoutDetaching($phoneNumbersId);
    }


    private function getUserMobileOperator(string $mobile): ?int
    {
        $patterns = [
            1 => '/0?9((1[0-9])|(9[0-4]))[0-9]{7}/', // Hamrahe avval
            2 => '/0?9(0[1-5]|3([0-1]|[3-9]))[0-9]{7}/', // Irancell
            3 => '/0?92[0-2][0-9]{7}/', // Rightel
            4 => '/0?9981[0-4][0-9]{5}/', // Shatel
            5 => '/0?932[0-9]{7}/', // Talia
        ];

        foreach ($patterns as $operatorId => $pattern) {

            if (preg_match($pattern, $mobile)) {
                return $operatorId;
            }
        }

        return null; // Other operators
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
