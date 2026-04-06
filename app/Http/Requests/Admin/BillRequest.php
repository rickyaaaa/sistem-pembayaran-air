<?php

namespace App\Http\Requests\Admin;

use App\Enums\BillStatus;
use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'amount' => 'required|numeric|min:0',
        ];

        if ($this->isMethod('POST')) {
            $rules['month'] = 'required|integer|between:1,12';
            $rules['year'] = 'required|integer|min:2020|max:2099';
            $rules['type'] = 'required|in:bulk,single';
            $rules['resident_id'] = 'required_if:type,single|nullable|exists:residents,id';
        } else {
            $rules['status'] = 'required|in:unpaid,pending,paid';
        }

        return $rules;
    }
}
