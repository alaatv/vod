<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class BonyadUsersExport implements FromCollection, WithHeadings, WithEvents
{
    private $users;

    public function __construct($users)
    {

        $this->users = $users;
    }

    /**
     * @return Collection
     */
    public function collection()
    {


        return $this->users->map(function ($user) {
            return [
                $user->id,
                $user->firstName,
                $user->lastName,
                $user->nationalCode,
                $user->phone,
                $user->address,
                $user->fatherMobile,
                $user->motherMobile,
                $user->mobile,
                $user->shahr?->ostan?->name,
                $user->shahr?->name,
                $user->major?->description,
                $user->grade?->displayName,
                $user->gender?->name,
            ];
        });
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {

        return [
            'آی دی کاربر',
            'نام',
            'نام حانوادگی',
            'کد ملی',
            'تلفن ثابت',
            'آدرس',
            'تلفن پدر',
            'تلفن مادر',
            'تلفن همراه',
            'استان',
            'شهر',
            'رشته',
            'پایه',
            'جنسیت',
        ];
    }

    /**
     * @inheritDoc
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
