<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectPatchRequest extends FormRequest
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
            'name'           => ['sometimes', 'required', 'string'],
            'customer_name'  => ['sometimes', 'required', 'string'],
            'code'           => ['sometimes', 'required', 'string'],
            'starts_at'      => ['required', 'date_format:Y-m-d,Y/m/d'],
            'ends_at'        => ['required', 'date_format:Y-m-d,Y/m/d', 'after_or_equal:starts_at'],
            'duration'       => ['sometimes', 'required', 'integer'],
            'status_id'      => ['sometimes', 'required', 'integer'],
            'pending_reason' => ['sometimes', 'required', 'string'],
            'user_ids'       => ['sometimes', 'required', 'array'],
        ];
    }

    public function validated ($key = null, $default = null)
    {
        $inputs = parent::validated($key, $default);

        if (isset($inputs['user_ids']))
        {
            $inputs['user_ids'] = array_unique(array_merge($inputs['user_ids'], [auth()->id()]));
        }

        return $inputs;
    }
}
