<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPostRequest extends FormRequest
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
            'name'          => ['required', 'string'],
            'email'         => ['required', 'email:rfc'],
            'password'      => ['required', 'string'],
            'date_of_birth' => ['required', 'date_format:Y-m-d'],
            'phone'         => ['required', 'string', 'regex:/^0[0-9]{9}$/'],
            'address'       => ['required', 'string'],
            'job_title'     => ['required', 'string'],
            'role_ids'      => ['sometimes', 'required', 'array'],
            'role_ids.*'    => ['sometimes', 'required', 'integer'],
        ];
    }

    public function validated ($key = null, $default = null)
    {
        return array_merge(parent::validated($key, $default), ['password' => bcrypt($this->all()['password'])]);
    }
}
