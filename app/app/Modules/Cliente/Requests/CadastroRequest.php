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

    /**
     * @see https://github.com/LaravelLegends/pt-br-validator
     * @return array
    */
    public function rules(): array
    {
        return [
            'nome'           => ['required', 'string', 'min:3', 'max:100'],
            'cpf'            => ['numeric', 'cpf', 'required_without:cnpj', 'unique:cliente,cpf'],
            'cnpj'           => ['numeric', 'cnpj', 'required_without:cpf', 'unique:cliente,cnpj'],
            'email'          => ['required', 'string', 'email', 'unique:cliente,email'],
            'telefone_movel' => ['required', 'string', 'regex:/^\(\d{2}\) 9\d{4}-\d{4}$/'],
            'cep'            => ['required', 'string', 'formato_cep'],
            'logradouro'     => ['required', 'string', 'min:3', 'max:100'],
            'numero'         => ['nullable', 'string', 'min:1', 'max:20'],
            'bairro'         => ['required', 'string', 'min:3', 'max:50'],
            'complemento'    => ['nullable', 'string', 'max:100'],
            'cidade'         => ['required', 'string', 'min:3', 'max:50'],
            'uf'             => ['required', 'string', 'uf'],
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

    public function toDto(): CadastroDto
    {
        return new CadastroDto(
            $this->validated('nome'),
            $this->validated('cpf'),
            $this->validated('cnpj'),
            $this->validated('email'),
            $this->validated('telefone_movel'),
            $this->validated('cep'),
            $this->validated('logradouro'),
            $this->validated('numero'),
            $this->validated('bairro'),
            $this->validated('complemento'),
            $this->validated('cidade'),
            $this->validated('uf')
        );
    }
}
