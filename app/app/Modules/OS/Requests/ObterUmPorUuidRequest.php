<?php

namespace App\Modules\OS\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ObterUmPorUuidRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    protected function uuid(): string
    {
        return (string) $this->route('uuid');
    }

    public function prepareForValidation(): void
    {
        $this->merge(['uuid' => $this->route('uuid')]);
    }

    public function rules(): array
    {
        return ['uuid' => ['required', 'uuid', 'exists:usuario,uuid']];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'Erros de validação',
            'errors'    => $validator->errors()->all(),
        ], Response::HTTP_BAD_REQUEST));
    }
}
