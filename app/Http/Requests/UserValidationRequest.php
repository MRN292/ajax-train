<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Unique;
// use Symfony\Component\HttpFoundation\JsonResponse;

class UserValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
            'name' => "required|min:3|max:255",
            'password' => 'required|max:255|min:8 ',
            'email' => 'required|email|max:255|unique:users',
            'file' => 'required|file|max:2048|mimes:png,jpg,jpeg',
        ];
    }

}
