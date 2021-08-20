<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TrainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'RezervasyonYapilacakKisiSayisi' => 'required|integer',
            'KisilerFarkliVagonlaraYerlestirilebilir' => 'required|bool',
        ];
    }

    public function messages()
    {
        return [
            'RezervasyonYapilacakKisiSayisi.required' => __('validation.required', ['attribute' => __('Kişi Sayısı')]),
            'KisilerFarkliVagonlaraYerlestirilebilir.required' => __('validation.required', ['attribute' => __('Farklı Vagonlar')]),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $data = array(
            'status' => false,
            'errors' => $validator->errors(),
            'data' => null,
            'message' => null
        );
        throw new HttpResponseException(response()->json($data, 422));
    }
}
