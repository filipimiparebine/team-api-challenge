<?php

namespace App\Http\Requests;

use Illuminate\Http\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $error = $validator->errors()->first();
        $field = $validator->errors()->keys()[0];
        $value = $this->input($field) ?? 'unknown';
        if(is_array($value)) {
            $value = json_encode($value);
        }

        throw new HttpResponseException(
            response()->json([
                'message' => "Invalid value for {$field}: {$value} - Error message: $error"
            ], Response::HTTP_BAD_REQUEST)
        );
    }
}
