<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePhoneNumberRequest;
use App\Imports\PhoneNumberImport;
use App\Models\PhoneNumber;
use App\Models\PhoneNumberProvider;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_PHONE_NUMBER_ACCESS'), ['only' => 'index']);
    }

    public function index(Request $request): JsonResponse
    {
        $number = $request->input('number');
        $phoneBookId = $request->input('phone_book_id');
        $phoneNumberProvider = $request->input('phone_number_provider');
        $phoneNumberProviders = PhoneNumberProvider::all();

        $phoneNumbers = PhoneNumber::latest()->with([
            'phoneBooks' => function ($query) use ($phoneBookId) {
                if (!empty($phoneBookId)) {
                    $query->where('id', $phoneBookId);
                }
            }
        ], 'phoneNumberProvider')->filter([
            'number' => $number, 'phoneBookId' => $phoneBookId, 'phoneNumberProvider' => $phoneNumberProvider
        ])->get();

        return response()->json(['phoneNumbers' => $phoneNumbers, 'phoneNumberProviders' => $phoneNumberProviders]);
    }

    public function store(CreatePhoneNumberRequest $request): JsonResponse
    {
        $phoneBookId = (int) $request->input('phone_book_id');
        $file = $request->file('file');
        $import = new PhoneNumberImport($phoneBookId);

        try {
            $import->queue($file);
            return response()->json(['message' => 'Phone numbers successfully added to phone book!'], 200);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Database error!'], 500);
        }
    }
}