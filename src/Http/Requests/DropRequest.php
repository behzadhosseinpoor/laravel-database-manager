<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DropRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'foreign_key_check' => 'bail|required|bool',
        ];
    }
}
