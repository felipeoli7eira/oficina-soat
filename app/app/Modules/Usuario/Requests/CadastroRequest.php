<?php

namespace App\Modules\Usuario\Requests;

use App\Modules\Usuario\Dto\CadastroDto;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CadastroRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    protected function prepareForValidation(): void
    {
        if ($this->papel) {
            $this->merge([
                'papel' => strtolower(trim($this->papel)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nome'   => ['required', 'string', 'max:255', 'min:3'],
            'email'  => ['required', 'email', 'min:6', 'max:255', 'unique:usuario,email'],
            'senha'  => ['required', 'string', 'min:8', 'max:255'],
            'papel'  => ['required', 'string', 'max:255', 'min:3', 'exists:roles,name'],
            'status' => ['required', 'string', 'max:255', 'min:3', 'in:ativo,inativo'],
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
