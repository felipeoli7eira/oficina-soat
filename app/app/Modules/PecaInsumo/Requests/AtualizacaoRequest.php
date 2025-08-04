<?php

namespace App\Modules\PecaInsumo\Requests;

use App\Modules\PecaInsumo\Dto\AtualizacaoDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class AtualizacaoRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:peca_insumo,id'],
            'gtin' => ['required', 'string', 'max:50'],
            'descricao' => ['required', 'string', 'min:3', 'max:255'],
            'valor_custo' => ['required', 'numeric', 'min:0'],
            'valor_venda' => ['required', 'numeric', 'min:0'],
            'qtd_atual' => ['required', 'integer', 'min:0'],
            'qtd_segregada' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'min:3', 'max:30']
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O campo id é obrigatório',
            'id.id'     => 'O campo id deve ser um id válido',
            'id.exists'   => 'O id informado não existe',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AtualizacaoDto
    {
        return new AtualizacaoDto($this->validated());
    }

    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();
        $status = Response::HTTP_BAD_REQUEST;
        $idErrors = $errors->get('id');

        if (!empty($idErrors)) {
            foreach ($idErrors as $message) {
                if (str_contains($message, 'existe') || str_contains($message, 'válido')) {
                    $status = Response::HTTP_NOT_FOUND;
                    break;
                }

                if (str_contains($message, 'válido') || str_contains($message, 'valid')) {
                    $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                }
            }
        }
        throw new HttpResponseException(response()->json([
            'message'   => 'Erros de validação',
            'errors'    => $errors->all(),
        ], $status));
    }
}
