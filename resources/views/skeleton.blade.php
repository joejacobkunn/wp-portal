<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body class="d-flex flex-column min-vh-100">
    <div id="app">
        <div id="main" class="layout-horizontal">

            <div class="content-wrapper container">
                @yield('content')
            </div>


        </div>
    </div>
    @include('partials.footer')

    @include('partials.scripts')

</body>

</html>
