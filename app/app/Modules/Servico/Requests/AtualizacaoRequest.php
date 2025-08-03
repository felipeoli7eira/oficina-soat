<?php

namespace App\Modules\Servico\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AtualizacaoRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function prepareForValidation(): void
    {
        $this->merge(['uuid' => $this->route('uuid')]);
    }

    protected function uuid(): string
    {
        return (string) $this->route('uuid');
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'exists:servicos,uuid'],
            'descricao' => ['sometimes', 'string', 'min:3', 'max:100'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', 'string', 'min:3', 'max:30'],
        ];
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
            'message' => 'Dados enviados incorretamente',
            'errors'  => $errors->all(),
        ], $status));
    }

    public function toDto(): \App\Modules\Servico\Dto\AtualizacaoDto
    {
        return new \App\Modules\Servico\Dto\AtualizacaoDto($this->validated());
    }
}
