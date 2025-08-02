<?php

namespace App\Modules\PecaInsumo\Requests;

use App\Modules\PecaInsumo\Dto\AtualizacaoDto;
use Illuminate\Foundation\Http\FormRequest;

class AtualizacaoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'gtin' => ['required', 'string', 'max:50'],
            'descricao' => ['required', 'string', 'min:2', 'max:255'],
            'valor_custo' => ['required', 'numeric', 'min:0'],
            'valor_venda' => ['required', 'numeric', 'min:0'],
            'qtd_atual' => ['required', 'integer', 'min:0'],
            'qtd_segregada' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:ativo,inativo'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toDto(): AtualizacaoDto
    {
        $dados = $this->all();

        return new AtualizacaoDto($dados);
    }
}
