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

function ordinal($n) 
{ 
    if (!in_array(($n % 100),array(11,12,13)))
    {
        switch ($n % 10)
        {
        // Only Handle 1st, 2nd, 3rd from Here
        case 1:  return $n .'st';
        case 2:  return $n .'nd';
        case 3:  return $n .'rd';
      }
    }
    return $n.'th';
}
