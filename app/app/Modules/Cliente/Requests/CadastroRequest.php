<?php

namespace App\Modules\Cliente\Requests;

use App\Modules\Cliente\Dto\CadastroDto;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CadastroRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function prepareForValidation(): void
    {
        $this->merge([]);
    }

    public function rules(): array
    {
        return [
            'nome'           => ['required', 'string', 'min:3', 'max:100'],

            /**
             * cpf só é obrigatório se cnpj não for informado
             * cnpj só é obrigatório se cpf não for informado
             * Se os dois vierem juntos → OK
             * Se nenhum vier → erro
             */
            'cpf'  => ['nullable', 'string', 'size:11', 'required_without_all:cnpj', 'unique:cliente,cpf'],
            'cnpj' => ['nullable', 'string', 'size:14', 'required_without_all:cpf', 'unique:cliente,cnpj'],

            'email'          => ['required', 'string', 'email', 'min:5', 'max:50', 'unique:cliente,email'],

            'telefone_movel' => ['required', 'string', 'min:10', 'max:20'],

            'cep'            => ['required', 'string', 'size:8'],
            'logradouro'     => ['required', 'string', 'min:3', 'max:100'],
            'numero'         => ['nullable', 'string', 'min:1', 'max:20'],
            'bairro'         => ['required', 'string', 'min:3', 'max:50'],
            'complemento'    => ['nullable', 'string', 'max:100'],
            'cidade'         => ['required', 'string', 'min:3', 'max:50'],
            'uf'             => ['required', 'string', 'size:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'        => 'O campo nome é obrigatório.',
            'nome.string'          => 'O campo nome deve ser uma string.',
            'nome.min'             => 'O campo nome deve ter no mínimo 3 caracteres.',
            'nome.max'             => 'O campo nome deve ter no máximo 100 caracteres.',

            'cpf.string'               => 'O campo CPF deve ser uma string.',
            'cpf.size'                 => 'O campo CPF deve ter 11 caracteres.',
            'cpf.required_without_all' => 'O campo CPF é obrigatório.',
            'cpf.required_without'     => 'O campo CPF é obrigatório.',
            'cpf.unique'               => 'O CPF já está cadastrado.',

            'cnpj.string'               => 'O campo CNPJ deve ser uma string.',
            'cnpj.size'                 => 'O campo CNPJ deve ter 14 caracteres.',
            'cnpj.required_without_all' => 'O campo CNPJ é obrigatório.',
            'cnpj.required_without'     => 'O campo CNPJ é obrigatório.',
            'cnpj.unique'               => 'O CNPJ já está cadastrado.',

            'email.required'       => 'O campo email é obrigatório.',
            'email.string'         => 'O campo email deve ser uma string.',
            'email.email'          => 'O campo email deve ser um email válido.',
            'email.min'            => 'O campo email deve ter no mínimo 5 caracteres.',
            'email.max'            => 'O campo email deve ter no máximo 50 caracteres.',
            'email.unique'         => 'O email já está cadastrado.',

            'telefone_movel.required'        => 'O campo telefone é obrigatório.',
            'telefone_movel.string'          => 'O campo telefone deve ser uma string.',
            'telefone_movel.min'             => 'O campo telefone deve ter no mínimo 10 caracteres.',
            'telefone_movel.max'             => 'O campo telefone deve ter no máximo 20 caracteres.',

            'cep.required'         => 'O campo CEP é obrigatório.',
            'cep.string'           => 'O campo CEP deve ser uma string.',
            'cep.length'           => 'O campo CEP deve ter 8 caracteres.',

            'logradouro.required'         => 'O campo rua é obrigatório.',
            'logradouro.string'           => 'O campo rua deve ser uma string.',
            'logradouro.min'              => 'O campo rua deve ter no mínimo 3 caracteres.',
            'logradouro.max'              => 'O campo rua deve ter no máximo 100 caracteres.',

            'numero.required'      => 'O campo número é obrigatório.',
            'numero.string'        => 'O campo número deve ser uma string.',
            'numero.min'           => 'O campo número deve ter no mínimo 1 caracteres.',
            'numero.max'           => 'O campo número deve ter no máximo 20 caracteres.',

            'bairro.required'      => 'O campo bairro é obrigatório.',
            'bairro.string'        => 'O campo bairro deve ser uma string.',
            'bairro.min'           => 'O campo bairro deve ter no mínimo 3 caracteres.',
            'bairro.max'           => 'O campo bairro deve ter no máximo 50 caracteres.',

            'cidade.required'      => 'O campo cidade é obrigatório.',
            'cidade.string'        => 'O campo cidade deve ser uma string.',
            'cidade.min'           => 'O campo cidade deve ter no mínimo 3 caracteres.',
            'cidade.max'           => 'O campo cidade deve ter no máximo 50 caracteres.',

            'uf.required'          => 'O campo UF é obrigatório.',
            'uf.string'            => 'O campo UF deve ser uma string.',
            'uf.length'            => 'O campo UF deve ter 2 caracteres.',

            'complemento.string'   => 'O campo complemento deve ser uma string.',
            'complemento.max'      => 'O campo complemento deve ter no máximo 100 caracteres.',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'Erros de validação',
            'errors'    => $validator->errors()->all(),
        ], Response::HTTP_BAD_REQUEST));
    }

    public function toDto(): CadastroDto
    {
        return new CadastroDto(
            nome: $this->input('nome'),
            cpf: $this->input('cpf'),
            cnpj: $this->input('cnpj'),
            email: $this->input('email'),
            telefone_movel: $this->input('telefone_movel'),
            cep: $this->input('cep'),
            logradouro: $this->input('logradouro'),
            numero: $this->input('numero'),
            bairro: $this->input('bairro'),
            complemento: $this->input('complemento'),
            cidade: $this->input('cidade'),
            uf: $this->input('uf')
        );
    }
}
