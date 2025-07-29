<?php

namespace App\Modules\Veiculo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObterUmPorUuidRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'string', 'uuid']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
