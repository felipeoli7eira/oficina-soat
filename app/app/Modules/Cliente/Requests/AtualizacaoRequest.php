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
        $this->merge([
            'uuid' => $this->route('uuid'),
        ]);
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'exists:cliente,uuid'],

            'nome' => ['sometimes', 'string', 'min:3', 'max:100'],

            // Se não fizer a exclusão do uuid atual, o Laravel vai acusar que o próprio valor atual já está no banco.
            'cpf' => ['sometimes', 'string', 'size:11', 'required_without_all:cnpj', 'unique:cliente,cpf,' . $this->route('uuid') . ',uuid'],
            'cnpj' => ['sometimes', 'string', 'size:14', 'required_without_all:cpf', 'unique:cliente,cnpj,' . $this->route('uuid') . ',uuid'],

            'email' => ['sometimes', 'string', 'email', 'min:5', 'max:50', 'unique:cliente,email,' . $this->route('uuid') . ',uuid'],
            'telefone_movel' => ['sometimes', 'string', 'min:10', 'max:20'],

            'cep' => ['sometimes', 'string', 'size:8'],
            'logradouro' => ['sometimes', 'string', 'min:3', 'max:100'],
            'numero' => ['sometimes', 'nullable', 'string', 'min:1', 'max:20'],
            'bairro' => ['sometimes', 'string', 'min:3', 'max:50'],
            'complemento' => ['sometimes', 'nullable', 'string', 'max:100'],
            'cidade' => ['sometimes', 'string', 'min:3', 'max:50'],
            'uf' => ['sometimes', 'string', 'size:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'O campo uuid é obrigatório.',
            'uuid.uuid' => 'O UUID informado nao é valido.',
            'uuid.exists' => 'O UUID informado nao existe.',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'Erros de validação',
            'errors'    => $validator->errors()->all(),
        ], Response::HTTP_BAD_REQUEST));
    }

    public function toDto(): \App\Modules\Cliente\Dto\AtualizacaoDto
    {
        return new \App\Modules\Cliente\Dto\AtualizacaoDto($this->validated());
    }
}
