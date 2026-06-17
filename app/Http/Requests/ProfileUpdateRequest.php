<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{

    /**
     * Prepara os dados para validação (Sanitização)
     */
    protected function prepareForValidation()
    {
        // 🎯 Se o telefone foi enviado, limpa deixando apenas números
        if ($this->has('phone') && !empty($this->phone)) {
            $this->merge([
                'phone' => preg_replace('/\D/', '', $this->phone),
            ]);
        }
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
