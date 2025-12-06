<?php

namespace BehzadHosseinPoor\DatabaseManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrowseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_by' => 'bail|nullable|string',
            'order_type' => 'bail|nullable|required_with:order_by|string|in:desc,asc',
            'per_page' => 'bail|required|int|min:5|max:100',
            'page' => 'bail|required|int|min:1',
        ];
    }
}