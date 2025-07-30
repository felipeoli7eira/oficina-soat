<?php

namespace App\Modules\Cliente\Requests;

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
            'uuid' => ['required', 'uuid', 'exists:cliente,uuid'],
            'nome' => ['sometimes', 'string', 'min:3', 'max:100'],

            // Se não fizer a exclusão do uuid atual, o Laravel vai acusar que o próprio valor atual já está no banco,
            // ou seja, os campos CNPJ e CPF devem ser únicos, desconsiderando o próprio usuário que está realizando a atualização.
            'cpf'  => ['sometimes', 'string', 'cpf', 'required_without_all:cnpj', 'unique:cliente,cpf,' . $this->uuid() . ',uuid'],
            'cnpj' => ['sometimes', 'string', 'cnpj', 'required_without_all:cpf', 'unique:cliente,cnpj,' . $this->uuid() . ',uuid'],

            'email'         => ['sometimes', 'string', 'email', 'unique:cliente,email,' . $this->uuid() . ',uuid'],
            'telefone_movel' => ['sometimes', 'string', 'regex:/^\(\d{2}\) 9\d{4}-\d{4}$/'],

            'cep'         => ['sometimes', 'string', 'formato_cep'],
            'logradouro'  => ['sometimes', 'string', 'min:3', 'max:100'],
            'numero'      => ['sometimes', 'nullable', 'string', 'min:1', 'max:20'],
            'bairro'      => ['sometimes', 'string', 'min:3', 'max:50'],
            'complemento' => ['sometimes', 'nullable', 'string', 'max:100'],
            'cidade'      => ['sometimes', 'string', 'min:3', 'max:50'],
            'uf'          => ['sometimes', 'string', 'uf'],
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

    public function toDto(): \App\Modules\Cliente\Dto\AtualizacaoDto
    {
        return new \App\Modules\Cliente\Dto\AtualizacaoDto($this->validated());
    }
}
