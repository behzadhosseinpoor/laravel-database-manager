<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => 'bail|nullable|int|min:5|max:100',
            'page' => 'bail|nullable|int|min:1',
            'query' => 'bail|required|string',
        ];
    }
}