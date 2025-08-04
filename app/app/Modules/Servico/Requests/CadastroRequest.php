<?php

namespace App\Modules\Servico\Requests;

use App\Modules\Servico\Dto\CadastroDto;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CadastroRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * @see https://github.com/LaravelLegends/pt-br-validator
     * @return array
    */
    public function rules(): array
    {
        return [
            'descricao' => ['required', 'string', 'min:3', 'max:100', 'unique:servicos,descricao'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', 'in:ATIVO,INATIVO'],
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
        return new CadastroDto(
            $this->validated('descricao'),
            $this->validated('valor'),
            $this->validated('status')
        );
    }
}
