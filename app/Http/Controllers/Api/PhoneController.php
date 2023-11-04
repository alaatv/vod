<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditPhoneRequest;
use App\Http\Requests\InsertPhoneRequest;
use App\Models\Phone;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PhoneController extends Controller
{
    protected Response $response;

    public function __construct()
    {
        /** setting permissions
         *
         */
        $this->middleware('permission:'.config('constants.EDIT_CONTACT_ACCESS'), ['only' => 'edit']);

        $this->response = new Response();
    }

    public function store(InsertPhoneRequest $request)
    {
        $phone = new Phone();
        $phone->fill($request->all());
        $phone->priority = preg_replace('/\s+/', '', $phone->priority);
        if (strlen($phone->priority == 0)) {
            $phone->priority = 0;
        }
        if ($phone->save()) {
            return $this->response->setStatusCode(ResponseAlias::HTTP_OK);
        }
        return $this->response->setStatusCode(ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function update(EditPhoneRequest $request, $phone)
    {
        $phone->fill($request->all());
        if ($phone->update()) {
            session()->put('success', 'شماره تماس با موفقیت اصلاح شد');
            return true;
        }
        session()->put('error', 'خطای پایگاه داده.');
        return false;
    }

    public function destroy($id)
    {
        return $id;
        //
    }
}