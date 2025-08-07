<?php

namespace App\Modules\Veiculo\Requests;

use App\Modules\Veiculo\Dto\AtualizacaoDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;


class AtualizacaoRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function rules(): array
    {
        $veiculoUuid = $this->route('uuid');

        return [
            'uuid' => ['required', 'uuid', 'exists:veiculo,uuid'],
            'placa' => [
                'nullable',
                'string',
                'regex:/^[A-Z]{3}-[0-9]{4}$|^[A-Z]{3}[0-9][A-Z][0-9]{2}$/',
                'unique:veiculo,placa,' . $veiculoUuid . ',uuid'
            ],
            'cor' => ['required', 'string', 'min:2', 'max:30'],
            'cliente_uuid' => ['nullable', 'uuid', 'exists:cliente,uuid']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'uuid' => $this->route('uuid'),
        ]);
    }

    public function messages(): array
    {
        return [
                // Mensagens específicas para UUID do veículo
            'uuid.required' => 'UUID do veículo é obrigatório.',
            'uuid.uuid' => 'O formato do UUID do veículo é inválido.',
            'uuid.exists' => 'Veículo não encontrado com o UUID fornecido.',

            // Mensagens para placa
            'placa.unique' => 'Esta placa já está cadastrada para outro veículo.',
            'placa.regex' => 'A placa deve estar no formato ABC-1234 ou ABC1D23.',

            // Mensagens para cliente_uuid
            'cliente_uuid.uuid' => 'O formato do UUID do cliente é inválido.',
            'cliente_uuid.exists' => 'Cliente não encontrado com o UUID fornecido.',
        ];
    }

    public function toDto(): AtualizacaoDto
    {
        $dados = $this->all();

        unset($dados['chassi']);
        unset($dados['ano_fabricacao']);
        unset($dados['marca']);
        unset($dados['modelo']);
        unset($dados['ano']);

        return new AtualizacaoDto($dados);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'error' => true,
                'message' => 'Dados de entrada inválidos',
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST)
        );
    }
}
