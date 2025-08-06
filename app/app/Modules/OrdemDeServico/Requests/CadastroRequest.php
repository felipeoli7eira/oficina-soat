<?php

namespace App\Modules\OrdemDeServico\Requests;

use App\Modules\OrdemDeServico\Dto\CadastroDto;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CadastroRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    protected function prepareForValidation(): void
    {
    }

    public function rules(): array
    {
        return [
            'cliente_uuid'             => ['required', 'uuid', 'exists:cliente,uuid'],
            'veiculo_uuid'             => ['required', 'uuid', 'exists:veiculo,uuid'],
            'descricao'                => ['required', 'string', 'min:10', 'max:1000'],
            'valor_desconto'           => ['required', 'numeric', 'min:0.01', 'lte:valor_total'],
            'valor_total'              => ['required', 'numeric', 'min:0.01'],
            'usuario_uuid_atendente'   => ['required', 'uuid', 'exists:usuario,uuid'],
            'usuario_uuid_mecanico'    => ['required', 'uuid', 'exists:usuario,uuid'],
            'prazo_validade'           => ['required', 'integer', 'min:1'],
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
        return new CadastroDto(...$this->validated());
    }
}
