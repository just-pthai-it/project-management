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
            'starts_at'      => ['sometimes', 'required', 'date'],
            'ends_at'        => ['sometimes', 'required', 'date', 'after_or_equal:starts_at'],
            'duration'       => ['sometimes', 'required', 'integer'],
            'status_id'      => ['sometimes', 'required', 'integer'],
            'pending_reason' => ['sometimes', 'required', 'string'],
            'user_ids'       => ['sometimes', 'required', 'array'],
        ];
    }
}
