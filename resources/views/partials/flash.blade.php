
@if ($message = Session::get('success'))
<script>
    // Listen for `DOMContentLoaded` event
    document.addEventListener('DOMContentLoaded', (e) => {
        Toastify({
            text: "{{ $message }}",
            duration: 3000,
            gravity:"bottom",
            position: "right",
            backgroundColor: "#4fbe87",
        }).showToast();
    });
</script>
@endif

<script>
    // Listen for `DOMContentLoaded` event
    document.addEventListener('show:toast', (e) => {
        Toastify({
            text: e.detail.message,
            duration: 3000,
            gravity:"bottom",
            position: "right",
            background: e.detail.type == 'success' ? "#4fbe87" : '#f3616d',
        }).showToast();
    });
</script>

