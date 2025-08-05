<?php

namespace App\Modules\PecaInsumo\Requests;

use App\Modules\PecaInsumo\Dto\CadastroDto;
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
            'gtin' => ['required', 'string', 'max:50'],
            'descricao' => ['required', 'string', 'min:3', 'max:255'],
            'valor_custo' => ['required', 'numeric', 'min:0'],
            'valor_venda' => ['required', 'numeric', 'min:0'],
            'qtd_atual' => ['required', 'integer', 'min:0'],
            'qtd_segregada' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'min:3', 'max:30']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): CadastroDto
    {
        return new CadastroDto(
            gtin: $this->gtin,
            descricao: $this->descricao,
            valor_custo: $this->valor_custo,
            valor_venda: $this->valor_venda,
            qtd_atual: $this->qtd_atual,
            qtd_segregada: $this->qtd_segregada,
            status: $this->status
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
