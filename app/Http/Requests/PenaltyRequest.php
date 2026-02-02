<?php

namespace App\Http\Requests;

use App\Helpers\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

class PenaltyRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rental_id' => 'required|exists:rentals,id',
            'title' => 'required|string|max:255',
            'reason' => 'required|string',
            'amount' => 'required|integer|min:1000'
        ];
    }

    public function messages(): array
{
    return [
        'rental_id.required' => 'Rental wajib dipilih.',
        'rental_id.exists'   => 'Data rental tidak ditemukan.',

        'title.required' => 'Judul denda wajib diisi.',
        'title.string'   => 'Judul denda harus berupa teks.',
        'title.max'      => 'Judul denda maksimal 255 karakter.',

        'reason.required' => 'Alasan denda wajib diisi.',
        'reason.string'   => 'Alasan denda harus berupa teks.',

        'amount.required' => 'Jumlah denda wajib diisi.',
        'amount.integer'  => 'Jumlah denda harus berupa angka.',
        'amount.min'      => 'Jumlah denda minimal 1000.',
    ];
}

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new HttpResponseException(Response::Error("Kesalahan dalam validasi", $validator->errors()));
    }
}
