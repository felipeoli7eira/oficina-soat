<?php

namespace App\Modules\PecaInsumo\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ObterUmPorUuidRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    protected function uuid(): string
    {
        return (string) $this->route('uuid');
    }

    public function prepareForValidation(): void
    {
        $this->merge(['uuid' => $this->route('uuid')]);
    }

    public function rules(): array
    {
        return ['uuid' => ['required', 'uuid', 'exists:peca_insumo,uuid']];
    }

    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();
        $uuidErrors = $errors->get('uuid');
        $status = Response::HTTP_BAD_REQUEST;

        if (!empty($uuidErrors)) {
            foreach ($uuidErrors as $message) {
                if (str_contains($message, 'inválido') || str_contains($message, 'invalid')  ) {
                    $status = Response::HTTP_NOT_FOUND;
                    break;
                }

                if (str_contains($message, 'válido') || str_contains($message, 'valid')) {
                    $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                }
            }
        }

        throw new HttpResponseException(response()->json([
            'message' => 'Erro de validação',
            'errors'  => $errors->all(),
        ], $status));
    }
}
