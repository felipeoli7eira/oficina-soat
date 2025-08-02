<?php

namespace App\Modules\Veiculo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListagemRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
