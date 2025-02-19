<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class ProfilRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'string|max:255',
            'lastname' => 'string|max:255',
            'status' => 'string|in:' . join(',', getProfilStatuses()),
            'image' => 'nullable|image',
        ];
    }
}
