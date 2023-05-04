<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommentPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize ()
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
            'content'    => ['required', 'string'],
            'comment_id' => ['sometimes', 'required', 'integer'],
        ];
    }

    public function validated ($key = null, $default = null)
    {
        return parent::validated($key, $default) + ['user_id' => auth()->id()];
    }
}
