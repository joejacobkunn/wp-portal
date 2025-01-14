<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body class="d-flex flex-column min-vh-100">
    <div id="app">
        <div id="main" class="layout-horizontal">
            @include('partials.header')

            <div class="content-wrapper container">
                @include('partials.flash')

                @include('partials.errors')

                @yield('content')
            </div>


        </div>
    </div>

    <x-confirmation></x-confirmation>
    
    @include('partials.footer')

    @include('partials.scripts')

</body>

</html>
