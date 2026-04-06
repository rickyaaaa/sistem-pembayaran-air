<?php

namespace App\Http\Requests\Admin;

use App\Enums\RegistrationCategory;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category'     => 'required|in:' . implode(',', array_column(RegistrationCategory::cases(), 'value')),
            'resident_id'  => 'nullable|exists:residents,id',
            'payment_date' => 'required|date',
            'amount'       => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
            'months'       => 'nullable|array',
            'iuran_year'   => 'nullable|integer',
        ];
    }
}
