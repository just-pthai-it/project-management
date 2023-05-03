<?php

namespace App\Http\Requests\Project;

use App\Models\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectPostRequest extends FormRequest
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
            'name'           => ['required', 'string'],
            'customer_name'  => ['required', 'string'],
            'code'           => ['required', 'string'],
            'starts_at'      => ['required', 'date'],
            'ends_at'        => ['required', 'date', 'after_or_equal:starts_at'],
            'duration'       => ['required', 'integer'],
            'status_id'      => ['required', 'integer'],
            'pending_reason' => ['required_if:status_id,' . ProjectStatus::STATUS_PENDING, 'string'],
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
        else
        {
            $inputs['user_ids'] = [auth()->id()];
        }

        return $inputs;
    }
}
