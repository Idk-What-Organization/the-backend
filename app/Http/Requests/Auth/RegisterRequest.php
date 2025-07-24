<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * This method is used to normalize or modify input values before validation.
     * In this case, it converts the email to lowercase to ensure uniqueness is not case-sensitive.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        Log::debug('RegisterRequest: Preparing data for validation.');

        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower($this->email),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> The validation rules for each input field.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z\s\'-]+$/u'
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'alpha_dash',
                'unique:users,username'
            ],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }
}
