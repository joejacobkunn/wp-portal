<!DOCTYPE html>
<html lang="en">
    @include('partials.head')
    <body>
        <div id="app">
            @include('partials.sidebar')
            <div id="main">
                @include('partials.navbar')

                @include('partials.flash')

                @include('partials.errors')

                @yield('content')

            @livewireScripts


        @include('partials.footer')

    </body>

</html>