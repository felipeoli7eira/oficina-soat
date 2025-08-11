<?php

namespace App\Modules\Veiculo\Requests;

use App\Modules\Veiculo\Dto\CadastroDto;
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
            'marca' => ['required', 'string', 'min:2', 'max:50'],
            'modelo' => ['required', 'string', 'min:2', 'max:50'],
            'ano' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'placa' => ['required', 'string', 'regex:/^[A-Z]{3}-[0-9]{4}$|^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', 'unique:veiculo,placa'],
            'cor' => ['required', 'string','min:2', 'max:30'],
            'chassi' => ['required', 'string', 'min:17', 'max:17', 'unique:veiculo,chassi'],
            'cliente_uuid' => ['nullable', 'uuid', 'exists:cliente,uuid']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'placa.unique' => 'Esta placa já está cadastrada para outro veículo.',
            'placa.regex' => 'A placa deve estar no formato ABC-1234 ou ABC1D23.',
            'chassi.unique' => 'Este chassi já está cadastrado para outro veículo.',
            'chassi.min' => 'O chassi deve ter exatamente 17 caracteres.',
            'chassi.max' => 'O chassi deve ter exatamente 17 caracteres.',
            'cliente_uuid.exists' => 'Cliente não encontrado.',
        ];
    }

    public function toDto(): CadastroDto
    {
        return new CadastroDto(
            marca: $this->marca,
            modelo: $this->modelo,
            ano: $this->ano,
            placa: $this->placa,
            cor: $this->cor,
            chassi: $this->chassi,
            clienteUuid: $this->cliente_uuid
        );
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
