<!DOCTYPE html>
<html lang="en">
<input class="form-check-input me-0 d-none" type="checkbox" id="toggle-dark" style="cursor: pointer">
@include('partials.head')

<body>
    <div id="app">
        @include('partials.sidebar')
        <div id="main">
            @include('partials.navbar')

            @include('partials.flash')

            @include('partials.errors')

            @yield('content')

            @include('partials.footer')

</body>

</html>