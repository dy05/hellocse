<?php

namespace App\Http\Requests;

use App\Enums\ProfilStatusEnum;
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
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'status' => 'nullable|string|in:' . join(',', getProfilStatuses()),
            'picture' => 'nullable|image',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->method() === 'POST') {
            if (!$this->input('status')) {
                $this->merge([
                    'status' => ProfilStatusEnum::INACTIF->value,
                ]);
            }

            if (!$this->file('picture')) {
                $this->merge([
                    'avatar' => fake()->imageUrl(200, 200, 'people'),
                ]);
            }

            if ($user = $this->user()) {
                $this->merge([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
