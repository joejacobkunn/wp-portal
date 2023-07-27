<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'W&P Portal')</title>
    <link rel="stylesheet" href="assets/css/main/app.css?ver={{ config('constants.asset_version') }}">
    <link rel="stylesheet" href="assets/css/pages/auth.css?ver={{ config('constants.asset_version') }}">
    <link rel="shortcut icon" href="assets/images/logo/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="assets/images/logo/favicon.png" type="image/png">
</head>

<body>
    <div id="auth">

<div class="row h-100">
    <div class="col-lg-4 col-12 pe-0">
        <div id="auth-left">
            <div class="auth-logo">
                <a href="/">
                    <img src="{{ accountLogo() }}" />
                </a>
            </div>

            @yield('left_column')
        </div>
    </div>
    <div class="col-lg-8 d-none d-lg-block ps-0">
        <div id="auth-right">
            @yield('right_column')
        </div>
    </div>
</div>

    </div>
</body>

</html>
