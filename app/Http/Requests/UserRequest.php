<?php

namespace App\Http\Requests;

use App\Helpers\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::where('email', $value)->first();

                    if ($user && $user->email_verified_at !== null) {
                        $fail('Email sudah terdaftar.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.regex' => 'Email harus menggunakan domain yang valid',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(Response::Error("Kesalahan dalam validasi", $validator->errors()));
    }
}
