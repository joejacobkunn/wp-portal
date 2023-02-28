@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><span class="fas fa-exclamation-circle"></span> Errors!</h4>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>

        <hr>
        <p class="mb-0">Please fix and try again.</p>
    </div>
@endif