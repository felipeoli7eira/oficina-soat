<?php

namespace App\Modules\OrdemDeServicoServico\Requests;

use App\Modules\OrdemDeServicoServico\Dto\AtualizacaoDto;
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

    public function uuid(): string
    {
        return (string) $this->route('uuid', '');
    }

    public function rules(): array
    {
        return [
            'uuid'             => ['required', 'uuid', 'exists:os_servico,uuid'],
            'observacao'     => ['sometimes', 'string', 'min:3', 'max:500'],
            'quantidade'     => ['sometimes', 'integer', 'min:1'],
            'valor'          => ['sometimes', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuid.required'             => 'O Uuid do item é obrigatório.',
            'uuid.exists'               => 'O item informado não existe.',
            'observacao.min'          => 'A observação deve ter pelo menos 3 caracteres.',
            'observacao.max'          => 'A observação não pode ter mais de 500 caracteres.',
            'quantidade.min'          => 'A quantidade deve ser pelo menos 1.',
            'valor.min'               => 'O valor deve ser maior que zero.',
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

    protected function passedValidation(): void
    {
        if (empty($this->validated())) {
            throw new HttpResponseException(response()->json([
                'error'   => true,
                'message' => 'Nenhum corpo enviado na requisição',
            ], Response::HTTP_BAD_REQUEST));
        }
    }

    public function toDto(): AtualizacaoDto
    {
        return new AtualizacaoDto($this->validated());
    }
}
