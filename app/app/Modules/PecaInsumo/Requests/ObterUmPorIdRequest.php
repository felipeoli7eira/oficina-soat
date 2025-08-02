<?php

namespace App\Modules\PecaInsumo\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ObterUmPorIdRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:peca_insumo,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O campo id é obrigatório',
            'id.id'     => 'O campo id deve ser um id válido',
            'id.exists'   => 'O id informado não existe',
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

