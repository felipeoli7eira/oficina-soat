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
        throw new HttpResponseException(response()->json([
            'error'   => true,
            'message' => 'Dados enviados incorretamente',
            'data'    => $validator->errors()->all(),
        ], Response::HTTP_BAD_REQUEST));
    }

    public function toDto(): \App\Modules\Servico\Dto\AtualizacaoDto
    {
        return new \App\Modules\Servico\Dto\AtualizacaoDto($this->validated());
    }
}
