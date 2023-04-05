<?php

use App\Models\Core\Account;
use App\Services\Environment\Domain;

if (! function_exists('account')) {
    function account() {

        $account_id = Domain::getClientId();

        if(empty($account_id)) abort(403);

        return Account::find($account_id);
    }
}
