<?php

if (! function_exists('account')) {
    function account()
    {

        $account = app('domain')->getClient();

        if (empty($account)) {
            abort(403);
        }

        return $account;
    }
}

function format_phone(string $phone_no)
{
    return preg_replace(
        "/.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4})/",
        '($1) $2-$3',
        $phone_no
    );
}

if (! function_exists('abbreviation')) {
    function abbreviation($string, $stringCount = 2)
    {
        $acronym = Str::of($string)->headline()->acronym();
        $acronym = Str::substr($acronym, 0, $stringCount);
        return Str::upper($acronym);
    }
}
