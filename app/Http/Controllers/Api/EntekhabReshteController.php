<?php

namespace App\Http\Controllers\Api;

use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntekhabReshteRequest;
use App\Http\Resources\EntekhabReshteResource;
use App\Models\Consultant;
use App\Models\EntekhabReshte;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class EntekhabReshteController extends Controller
{
    /**
     * @throws Throwable
     */
    public function store(EntekhabReshteRequest $request)
    {
        $user = auth()->user();
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = Uploader::put($request->file('file'), config('disks.ENTEKHAB_RESHTE'));
        }
        $inputs = $request->validated();
        $syncShahrArray = [];
        $shahrha = json_decode(Arr::get($inputs, 'shahrha'), true);
        foreach ($shahrha as $shahr) {
            $syncShahrArray[$shahr['id']] = ['order' => $shahr['order']];
        }
        return DB::transaction(function () use ($user, $inputs, $syncShahrArray, $filePath) {
            $user->update([
                'phone' => Arr::get($inputs, 'phone'),
            ]);
            $entekhabReshte = EntekhabReshte::updateOrCreate(['user_id' => $user->id], [
                'file' => $filePath,
                'comment' => Arr::get($inputs, 'comment') ?? null,
                'majors' => Arr::get($inputs, 'majors'),
            ]);
            $entekhabReshte->shahrha()->sync($syncShahrArray);
            $entekhabReshte->universityTypes()->sync(Arr::get($inputs, 'university_types'));
            if (array_key_exists('consultant_mobile', $inputs)) {
                $consultant = Consultant::firstOrCreate(
                    [
                        'mobile' => Arr::get($inputs, 'consultant_mobile'),
                    ],
                    [
                        'first_name' => Arr::get($inputs, 'consultant_firstname'),
                        'last_name' => Arr::get($inputs, 'consultant_lastname'),
                    ]
                );
                $user->consultants()->sync($consultant->id);
            }
            return new EntekhabReshteResource($entekhabReshte);
        });
    }
}
