<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'prefix_th'     => ['nullable', 'string', 'max:100'],
            'first_name_th' => ['nullable', 'string', 'max:255'],
            'last_name_th'  => ['nullable', 'string', 'max:255'],
            'prefix_en'     => ['nullable', 'string', 'max:100'],
            'first_name_en' => ['nullable', 'string', 'max:255'],
            'last_name_en'  => ['nullable', 'string', 'max:255'],
            'birthday'      => ['nullable', 'date'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
            'shirt_size'    => ['nullable', 'string', 'max:10'],
        ];
    }
}
