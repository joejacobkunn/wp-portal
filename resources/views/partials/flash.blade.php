@if ($message = Session::get('success'))
<script>
    // Listen for `DOMContentLoaded` event
    document.addEventListener('DOMContentLoaded', (e) => {
        notyf.open({
            type: 'success',
            message: '{{ $message }}'
        });
    });
    

</script>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('warning'))
<script>
    document.addEventListener('DOMContentLoaded', (e) => {
        notyf.open({
            type: 'warning',
            message: '{{ $message }}'
        });
    });


</script>

@endif


@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
