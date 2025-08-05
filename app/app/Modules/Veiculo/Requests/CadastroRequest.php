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
            'placa' => ['required', 'string', 'regex:/^[A-Z]{3}-[0-9]{4}$|^[A-Z]{3}[0-9][A-Z][0-9]{2}$/'],
            'cor' => ['nullable', 'string', 'max:30'],
            'chassi' => ['required', 'string', 'min:17', 'max:17'],
            'cliente_uuid' => ['nullable', 'uuid', 'exists:clientes,uuid']
        ];
    }

    public function authorize(): bool
    {
        return true;
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
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
