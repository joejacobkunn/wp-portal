<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body>
    <div id="app">
        <div id="main" class="layout-horizontal">
            @include('partials.header')

            <div class="content-wrapper container">
                @include('partials.flash')

                @include('partials.errors')

                @yield('content')
            </div>

            @include('partials.footer')

        </div>
    </div>

    @include('partials.scripts')

</body>

</html>
