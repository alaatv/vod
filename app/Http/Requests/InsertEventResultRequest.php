<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertEventResultRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'nomre_taraz_dey' => 'nullable|numeric',
            'nomre_taraz_tir' => 'nullable|numeric',
            'nomre_taraz_moadel' => 'required|numeric',
            'nomre_taraz_kol' => 'required|numeric',
            'rank_in_region' => 'required_if:region_id,5|required_if:region_id,6',
            'rank_in_district' => 'required',
            'shahr_id' => 'required|integer|numeric|exists:shahr,id',
            'postalCode' => 'required|digits:10',
            'rank' => 'required|integer',
            'region_id' => 'required|exists:kunkur_regions,id',
            'major_id' => 'required|exists:majors,id|integer',
            'participationCode' => 'nullable|numeric',
//            'participationCode' => 'unique:eventresults,'.Hash::make($this->request->get('participationCode')),
            'event_id' => 'required|exists:events,id',
            'reportFile' => 'required|mimes:jpeg,jpg,png,pdf',
            'enableReportPublish' => 'sometimes|boolean',
            'eventresultstatus_id' => 'sometimes|exists:eventresultstatuses,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->replaceNumbers();
        parent::prepareForValidation();
    }

    protected function replaceNumbers()
    {
        $input = $this->request->all();

        foreach ($input as $key => $value) {
            if ($value == 'null') {
                unset($input[$key]);
            }
        }

        if (isset($input['participationCode'])) {
            $input['participationCode'] = preg_replace('/\s+/', '', $input['participationCode']);
            $input['participationCode'] = $this->convertToEnglish($input['participationCode']);
        }
        if (isset($input['nomre_taraz_dey'])) {
            $input['nomre_taraz_dey'] = preg_replace('/\s+/', '', $input['nomre_taraz_dey']);
            $input['nomre_taraz_dey'] = $this->convertToEnglish($input['nomre_taraz_dey']);
        }
        if (isset($input['nomre_taraz_tir'])) {
            $input['nomre_taraz_tir'] = preg_replace('/\s+/', '', $input['nomre_taraz_tir']);
            $input['nomre_taraz_tir'] = $this->convertToEnglish($input['nomre_taraz_tir']);
        }
        if (isset($input['nomre_taraz_moadel'])) {
            $input['nomre_taraz_moadel'] = preg_replace('/\s+/', '', $input['nomre_taraz_moadel']);
            $input['nomre_taraz_moadel'] = $this->convertToEnglish($input['nomre_taraz_moadel']);
        }
        if (isset($input['nomre_taraz_kol'])) {
            $input['nomre_taraz_kol'] = preg_replace('/\s+/', '', $input['nomre_taraz_kol']);
            $input['nomre_taraz_kol'] = $this->convertToEnglish($input['nomre_taraz_kol']);
        }
        if (isset($input['rank_in_region'])) {
            $input['rank_in_region'] = preg_replace('/\s+/', '', $input['rank_in_region']);
            $input['rank_in_region'] = $this->convertToEnglish($input['rank_in_region']);
        }
        if (isset($input['rank_in_district'])) {
            $input['rank_in_district'] = preg_replace('/\s+/', '', $input['rank_in_district']);
            $input['rank_in_district'] = $this->convertToEnglish($input['rank_in_district']);
        }
        if (isset($input['postalCode'])) {
            $input['postalCode'] = preg_replace('/\s+/', '', $input['postalCode']);
            $input['postalCode'] = $this->convertToEnglish($input['postalCode']);
        }
        if (isset($input['rank'])) {
            $input['rank'] = preg_replace('/\s+/', '', $input['rank']);
            $input['rank'] = $this->convertToEnglish($input['rank']);
        }

        $this->replace($input);
    }
}
