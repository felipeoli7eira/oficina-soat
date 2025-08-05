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
            'uuid' => $this->route('uuid'),
        ]);
    }

    public function rules(): array
    {
        return [
            'uuid' => [
                'required',
                'uuid',
                function ($attribute, $value, $fail) {
                    $exists = \DB::table('peca_insumo')->where('uuid', $value)->exists();
                    if (!$exists) {
                        $fail('O uuid informado não existe');
                    }
                }
            ],
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
            'uuid.required' => 'O campo uuid é obrigatório',
            'uuid.uuid'     => 'O campo uuid deve ser um uuid válido',
            'uuid.exists'   => 'O uuid informado não existe',
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
        $status = $this->determineHttpStatus($errors);

        throw new HttpResponseException(response()->json([
            'message' => 'Dados enviados incorretamente',
            'errors'  => $errors->all(),
        ], $status));
    }

    private function determineHttpStatus($errors): int
    {
        $uuidErrors = $errors->get('uuid');

        if (empty($uuidErrors)) {
            return Response::HTTP_BAD_REQUEST;
        }

        foreach ($uuidErrors as $message) {
            if (str_contains($message, 'obrigatório') ||
                str_contains($message, 'required') ||
                str_contains($message, 'não existe') ||
                str_contains($message, 'not exist')) {
                return Response::HTTP_NOT_FOUND;
            }

            if (str_contains($message, 'válido') || str_contains($message, 'valid')) {
                return Response::HTTP_UNPROCESSABLE_ENTITY;
            }
        }

        return Response::HTTP_BAD_REQUEST;
    }
}
