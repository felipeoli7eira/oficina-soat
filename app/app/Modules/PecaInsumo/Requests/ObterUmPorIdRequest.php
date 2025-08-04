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
        $errors = $validator->errors();
        $status = Response::HTTP_BAD_REQUEST;
        $idErrors = $errors->get('id');

        if (!empty($idErrors)) {
            foreach ($idErrors as $message) {
                if (str_contains($message, 'existe') || str_contains($message, 'válido')) {
                    $status = Response::HTTP_NOT_FOUND;
                    break;
                }

                if (str_contains($message, 'válido') || str_contains($message, 'valid')) {
                    $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                }
            }
        }
        throw new HttpResponseException(response()->json([
            'message'   => 'Erros de validação',
            'errors'    => $errors->all(),
        ], $status));
    }
}

