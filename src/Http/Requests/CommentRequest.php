<?php

namespace Yarmat\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->attributes->add(['message' => strip_tags($this->get('message'), config('comment.allowable_tags'))]); //Remove all html tags
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validationRules = \Auth::check() ? config('comment.validation.auth')
            : config('comment.validation.not_auth');

        return $validationRules;
    }

    public function messages()
    {
        return config('comment.validation.messages');
    }
}
