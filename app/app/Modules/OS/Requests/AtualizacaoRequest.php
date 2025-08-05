<?php

namespace App\Modules\OS\Requests;

use App\Modules\OS\Dto\AtualizacaoDto;
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
        return $this->route('uuid', '');
    }

    public function rules(): array
    {
        return [
            'uuid'                     => ['required', 'uuid', 'exists:os,uuid'],
            'cliente_uuid'             => ['sometimes', 'uuid', 'exists:cliente,uuid'],
            'veiculo_uuid'             => ['sometimes', 'uuid', 'exists:veiculo,uuid'],
            'descricao'                => ['sometimes', 'string', 'min:10', 'max:1000'],
            'valor_desconto'           => ['sometimes', 'numeric', 'min:0.01', 'lte:valor_total'],
            'valor_total'              => ['sometimes', 'numeric', 'min:0.01'],
            'usuario_uuid_atendente'   => ['sometimes', 'uuid', 'exists:usuario,uuid'],
            'usuario_uuid_mecanico'    => ['sometimes', 'uuid', 'exists:usuario,uuid'],
            'prazo_validade'           => ['sometimes', 'integer', 'min:1'],
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
