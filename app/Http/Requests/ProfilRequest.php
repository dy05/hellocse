<?php

namespace App\Http\Requests;

use Alirezasedghi\LaravelImageFaker\ImageFaker;
use Alirezasedghi\LaravelImageFaker\Services\Picsum;
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
            'firstname' => 'required_without:deleted|string|max:255',
            'lastname' => 'required_without:deleted|string|max:255',
            'status' => 'nullable|string|in:' . join(',', getProfilStatuses()),
            'picture' => 'nullable|image',
            'deleted' => 'nullable|boolean',
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
                $imageFaker = new ImageFaker(new Picsum());
                $this->merge([
                    'avatar' => $imageFaker->imageUrl(400, 400),
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
