@if($class == 'success')
<div class="alert alert-success" role="alert">
@endif
@if($class == 'error')
<div class="alert alert-danger" role="alert">
@endif
    <i class="fas fa-info-circle"></i>&nbsp;
    {{ $message }}
</div>
