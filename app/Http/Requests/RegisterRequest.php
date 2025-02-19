<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'bail|required|string|email|max:255|unique:administrators',
            'password' => 'bail|required|min:4|confirmed',
        ];
    }

    public function passedValidation(): void
    {
        // hash password to not doing again in controller
        $this->merge([
            'password' => bcrypt($this->input('password')),
        ]);
    }
}
