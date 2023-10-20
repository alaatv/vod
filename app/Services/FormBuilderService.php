<?php

namespace App\Services;

use App\Http\Resources\Contenttype;
use App\Http\Resources\Major;
use App\Http\Resources\Paymentstatus;
use App\Http\Resources\RegionResource;
use App\Http\Resources\StudyEventResource;
use App\Http\Resources\UniversityTypeResource;
use App\Models\Region;
use App\Models\Studyevent;
use App\Models\UniversityType;
use Illuminate\Support\Collection;

class FormBuilderService
{
    private array $types;
    private ?array $allResources = null;

    public function setTypes($types): static
    {
        $this->types = $types;
        return $this;
    }

    public function getResources(): ?array
    {
        $this->setAllResources();
        return $this->allResources;
    }

    private function setAllResources(): void
    {
        foreach ($this->types as $type) {
            $resource = match ($type) {
                'majors' => Major::class,
                'paymentStatuses' => Paymentstatus::class,
                'contenttypes' => Contenttype::class,
                'studyEvents' => StudyEventResource::class,
                'regions' => RegionResource::class,
                'universityTypes' => UniversityTypeResource::class,
                default => null,
            };
            if (isset($resource)) {
                $this->allResources[$type] = $resource::collection($this->{$type}());
            }
        }
    }

    private function majors(): Collection|array
    {
        return \App\Major::whereIn('id',
            [\App\Major::RIYAZI, \App\Major::TAJROBI, \App\Major::ENSANI])->get();
    }

    private function paymentStatuses()
    {
        return \App\Paymentstatus::all();
    }

    private function contenttypes()
    {
        return \App\Contenttype::all();
    }

    private function studyEvents()
    {
        return Studyevent::all();
    }

    private function regions()
    {
        return Region::whereNotIn('id', [4, 5])->get();
    }

    private function universityTypes()
    {
        return UniversityType::all();
    }
}
