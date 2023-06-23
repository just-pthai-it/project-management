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
            'summary'        => ['string'],
            'starts_at'      => ['required', 'date_format:Y-m-d,Y/m/d'],
            'ends_at'        => ['required', 'date_format:Y-m-d,Y/m/d', 'after_or_equal:starts_at'],
            'duration'       => ['required', 'integer'],
            'status_id'      => ['required', 'integer'],
            'pending_reason' => ['required_if:status_id,' . ProjectStatus::STATUS_PENDING, 'string'],
            'user_ids'       => ['required', 'array'],
        ];
    }
}
