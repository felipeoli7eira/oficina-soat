<?php

namespace App\Modules\OS\Requests;

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

        if ($this->papel) {
            $this->merge([
                'papel' => strtolower(trim($this->papel)),
            ]);
        }
    }

    public function uuid(): string
    {
        return (string) $this->route('uuid');
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'exists:usuario,uuid'],
            'nome' => ['sometimes', 'string', 'max:255', 'min:3'],
            'papel' => ['sometimes', 'string', 'max:255', 'min:3', 'exists:roles,name'],
            'status' => ['sometimes', 'string', 'max:255', 'min:3', 'in:ativo,inativo'],
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

    protected function passedValidation(): void
    {
        if (empty($this->validated())) {
            throw new HttpResponseException(response()->json([
                'error'   => true,
                'message' => 'Nenhum corpo enviado na requisição',
            ], Response::HTTP_BAD_REQUEST));
        }
    }

    public function toDto(): \App\Modules\OS\Dto\AtualizacaoDto
    {
        return new \App\Modules\OS\Dto\AtualizacaoDto($this->validated());
    }
}
