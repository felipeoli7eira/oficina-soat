<?php

namespace App\Modules\Veiculo\Requests;

use App\Modules\Veiculo\Dto\ListagemDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ListagemRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function rules(): array
    {
        return [
            'cliente_uuid' => ['nullable', 'uuid', 'exists:cliente,uuid'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): ListagemDto
    {
        return new ListagemDto(
            clienteUuid: $this->cliente_uuid,
            page: $this->page,
            perPage: $this->per_page
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
