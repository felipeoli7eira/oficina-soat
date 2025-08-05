<?php

namespace App\Modules\Veiculo\Requests;

use App\Modules\Veiculo\Dto\AtualizacaoDto;
use Illuminate\Foundation\Http\FormRequest;

class AtualizacaoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'marca' => ['nullable', 'string', 'min:2', 'max:50'],
            'modelo' => ['nullable', 'string', 'min:2', 'max:50'],
            'ano_fabricacao' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'placa' => ['nullable', 'string', 'regex:/^[A-Z]{3}-[0-9]{4}$|^[A-Z]{3}[0-9][A-Z][0-9]{2}$/'],
            'cor' => ['nullable', 'string', 'max:30'],
            'chassi' => ['nullable', 'string', 'min:17', 'max:17'],
            'cliente_uuid' => ['nullable', 'uuid', 'exists:clientes,uuid']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AtualizacaoDto
    {
        $dados = $this->all();

        if (isset($dados['ano'])) {
            $dados['ano_fabricacao'] = $dados['ano'];
            unset($dados['ano']);
        }

        return new AtualizacaoDto($dados);
    }
}
