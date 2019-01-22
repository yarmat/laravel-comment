<?php

namespace Yarmat\Comment\Rules;

use Illuminate\Contracts\Validation\Rule;

class Spam implements Rule
{
    protected $spam = null;

    public function passes($attribute, $value)
    {
        $spamList = config('comment.spam_list');

        foreach ($spamList as $spam) {
            if (strpos($value, $spam) !== false) {
                $this->spam = $spam;
                return false;
            }
        }

        return true;
    }


    public function message()
    {
        return __('comment.validation.spam', [
            'spam' => $this->spam
        ]);
    }
}