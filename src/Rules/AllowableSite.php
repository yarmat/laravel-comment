<?php

namespace Yarmat\Comment\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllowableSite implements Rule
{

    public function passes($attribute, $value)
    {
        $allowableSites = config('comment.allowable_sites');

        if (count($allowableSites) < 1) return true;

        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $value, $match);

        foreach ($match[0] as $url) {
            $host = parse_url($url, PHP_URL_HOST);
            if (!in_array($host, $allowableSites)) return false;
        }

        return true;
    }


    public function message()
    {
        return __('comment.messages.validation.black_site');
    }
}