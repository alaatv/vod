<?php

namespace App\Imports;

use App\Traits\APIRequestCommon;
use App\Traits\CharacterCommon;
use App\Traits\OrderCommon;
use App\Traits\User\AssetTrait;
use App\Traits\UserCommon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class UsersOrderImport implements ToCollection, WithProgressBar
{
    use APIRequestCommon;
    use AssetTrait;
    use CharacterCommon;
    use Importable;
    use OrderCommon;
    use UserCommon;

    public static $rows;

    public function collection(Collection $rows)
    {
        self::$rows = $rows;
    }
}
