<?php

namespace App\Http\Requests\Task;

use App\Models\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskPatchRequest extends FormRequest
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
            'description'    => ['sometimes', 'required', 'string'],
            'starts_at'      => ['sometimes', 'required', 'date_format:Y-m-d H:i:s,Y-m-d\\TH:i:sP'],
            'ends_at'        => ['sometimes', 'required', 'date_format:Y-m-d H:i:s,Y-m-d\\TH:i:sP'],
            'duration'       => ['sometimes', 'required', 'integer'],
            'status_id'      => ['sometimes', 'required', 'integer'],
            'pending_reason' => [
//                'required_if:status_id,' . TaskStatus::STATUS_PENDING, 'string'
            ],
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
