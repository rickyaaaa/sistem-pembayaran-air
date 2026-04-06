<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'name' => 'required|string|max:255',
                'block' => 'required|string|max:10',
                'house_number' => 'required|string|max:10',
                'phone_number' => 'nullable|string|max:20',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ];
    }
}
