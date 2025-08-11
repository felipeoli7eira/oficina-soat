<?php

namespace App\Modules\OrdemDeServicoServico\Requests;

use App\Modules\OrdemDeServicoServico\Dto\CadastroDto;

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
            'servico_uuid' => ['required', 'uuid', 'exists:servicos,uuid'],
            'os_uuid'          => ['required', 'uuid', 'exists:os,uuid'],
            'observacao'     => ['required', 'string', 'min:3', 'max:500'],
            'quantidade'     => ['required', 'integer', 'min:1'],
            'valor'          => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'servico_uuid.required' => 'O campo serviço é obrigatório.',
            'servico_uuid.exists'   => 'O serviço informado não existe.',
            'os_uuid.required'          => 'O campo ordem de serviço é obrigatório.',
            'os_uuid.exists'            => 'A ordem de serviço informada não existe.',
            'observacao.required'     => 'O campo observação é obrigatório.',
            'observacao.min'          => 'A observação deve ter pelo menos 3 caracteres.',
            'observacao.max'          => 'A observação não pode ter mais de 500 caracteres.',
            'quantidade.required'     => 'O campo quantidade é obrigatório.',
            'quantidade.min'          => 'A quantidade deve ser pelo menos 1.',
            'valor.required'          => 'O campo valor é obrigatório.',
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

    public function toDto(): CadastroDto
    {
        $validated = $this->validated();

        return new CadastroDto(
            servico_uuid: $validated['servico_uuid'],
            os_uuid: $validated['os_uuid'],
            observacao: $validated['observacao'],
            quantidade: $validated['quantidade'],
            valor: $validated['valor']
        );
    }
}
