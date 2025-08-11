<?php

namespace App\Modules\PecaInsumo\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ObterUmPorUuidRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function prepareForValidation(): void
    {
        $this->merge([
            'uuid' => $this->route('uuid'),
        ]);
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'exists:peca_insumo,uuid,excluido,0'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'O campo uuid é obrigatório',
            'uuid.uuid'     => 'O campo uuid deve ser um UUID válido',
            'uuid.exists'   => 'O uuid informado não existe',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'Erros de validação',
            'errors'    => $validator->errors()->all(),
        ], Response::HTTP_BAD_REQUEST));
    }
}
