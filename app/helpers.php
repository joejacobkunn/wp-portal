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

if (! function_exists('accountLogo')) {
    function accountLogo()
    {
        try {
            $logo = account()->getFirstMedia('documents');

            if($logo && File::exists($logo->getPath())){
                return $logo->getUrl();
            }
        } catch(\Exception $e) {

        }

        return url('/assets/images/logo.png');
    }
}

if(! function_exists('isFileExists')) {
    function isFileExists($account)
    {
        try {
            $logo = $account->getFirstMedia('documents');
            if($logo && File::exists($logo->getPath())){
                return true;
            }
        } catch(\Exception $e) {
        }

        return false;
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
