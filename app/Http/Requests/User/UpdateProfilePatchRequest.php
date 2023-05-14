<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize () : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules () : array
    {
        return [
            'name'          => ['sometimes', 'required', 'string'],
            'email'         => ['sometimes', 'required', 'email:rfc'],
            'date_of_birth' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'phone'         => ['sometimes', 'required', 'string', 'regex:/^0[0-9]{9}$/'],
            'address'       => ['sometimes', 'required', 'string'],
        ];
    }
}
