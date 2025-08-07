<?php

namespace App\Modules\PecaInsumo\Requests;

use App\Modules\PecaInsumo\Dto\AtualizacaoDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class AtualizacaoRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function prepareForValidation(): void
    {
        $this->merge([
            'uuid' => $this->route('uuid'),
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'uuid' => ['required', 'uuid', 'exists:peca_insumo,uuid'],
            'descricao' => ['required', 'string', 'min:3', 'max:255'],
            'valor_custo' => ['required', 'numeric', 'min:0.01'],
            'valor_venda' => ['required', 'numeric', 'min:0.01'],
            'qtd_atual' => ['required', 'integer', 'min:1'],
            'qtd_segregada' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:ativo,inativo']
        ];

        if ($this->route('uuid') && \Illuminate\Support\Str::isUuid($this->route('uuid'))) {
            $rules['gtin'] = [
                'required',
                'string',
                'min:7',
                'max:20',
                'unique:peca_insumo,gtin,' . $this->route('uuid') . ',uuid'
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'O campo uuid é obrigatório',
            'uuid.uuid'     => 'O campo uuid deve ser um uuid válido',
            'uuid.exists'   => 'O uuid informado não existe',
            'gtin.unique'   => 'Este GTIN já está sendo utilizado por outra peça/insumo',
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
        $uuidErrors = $errors->get('uuid');
        $status = Response::HTTP_BAD_REQUEST;

        if (!empty($uuidErrors)) {
            foreach ($uuidErrors as $message) {
                if (str_contains($message, 'obrigatório') ||
                    str_contains($message, 'required') ||
                    str_contains($message, 'não existe') ||
                    str_contains($message, 'not exist')) {
                    $status = Response::HTTP_NOT_FOUND;
                    break;
                }
            }
        }

        throw new HttpResponseException(response()->json([
            'message' => 'Dados enviados incorretamente',
            'errors'  => $errors->all(),
        ], $status));
    }
}
