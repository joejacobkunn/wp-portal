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
