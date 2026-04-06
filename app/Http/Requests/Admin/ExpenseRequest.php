<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
            'category' => 'required|string|max:100',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
