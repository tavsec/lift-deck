@if(session()->has('ga_event'))
<script>
    (function () {
        var e = @json(session('ga_event'));
        if (typeof gtag === 'function') {
            gtag('event', e.name, e.params || {});
        }
    })();
</script>
@endif
