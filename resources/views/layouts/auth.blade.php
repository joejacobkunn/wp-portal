<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'W&P Portal')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/main/app.css') . '?ver='. config('constants.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') . '?ver='. config('constants.asset_version') }}">
    <link rel="shortcut icon"
        href="https://wandpmanagement.com/wp-content/uploads/2017/11/cropped-WPLogoFinalFavicon512X512-32x32.png"
        type="image/x-icon">
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12 pe-0">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="/">
                            <img src="{{ accountLogo() }}" />
                        </a>
                    </div>

                    @yield('left_column')
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block ps-0">
                <div id="auth-right">
                    <center><img style="margin-top:30%" src="{{ url('/assets/images/wp-logo.jpg') }}" /></center>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
