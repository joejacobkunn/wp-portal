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

