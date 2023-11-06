<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePhoneBookRequest;
use App\Models\PhoneBook;
use Illuminate\Http\JsonResponse;

class PhoneBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_PHONE_BOOK_ACCESS'), ['only' => 'index']);
    }

    public function index(): JsonResponse
    {
        $phonebooks = PhoneBook::latest()->paginate();

        return response()->json(['phonebooks' => $phonebooks]);
    }

    public function store(CreatePhoneBookRequest $request): JsonResponse
    {
        $phonebook = new PhoneBook();
        $phonebook->fill($request->all());

        if ($phonebook->save()) {
            return response()->json(['message' => 'Phone book successfully added!'], 200);
        } else {
            return response()->json(['error' => 'Database error!'], 500);
        }
    }
}