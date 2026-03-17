<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|min:3|max:70',
            'last_name' => 'string|min:3|max:70',
            'email' => 'string|email|unique:users,email',
            'password' => ['required','confirmed',Password::min(8)->letters()->mixedCase() ->numbers()->symbols(),],
        ];
    }
}
