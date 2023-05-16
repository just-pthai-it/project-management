<?php

namespace App\Http\Requests\Task;

use App\Models\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class CreateTaskPostRequest extends FormRequest
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
            'description'    => ['required', 'string'],
            'starts_at'      => ['required', 'date_format:Y-m-d H:i:s,Y-m-d\\TH:i:sP'],
            'ends_at'        => ['required', 'date_format:Y-m-d H:i:s,Y-m-d\\TH:i:sP'],
            'duration'       => ['required', 'integer'],
            'status_id'      => ['sometimes', 'required', 'integer'],
            'pending_reason' => ['required_if:status_id,' . TaskStatus::STATUS_PENDING, 'string'],
            'parent_id'      => ['sometimes', 'required', 'integer'],
        ];
    }

    public function validated ($key = null, $default = null)
    {
        return parent::validated($key, $default) + ['user_id' => auth()->id()];
    }
}
