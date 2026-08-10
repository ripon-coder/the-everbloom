@if (session()->has('success'))
    <script>
        if (typeof toastr !== 'undefined') {
            toastr.success("{{ session('success') }}");
        }
    </script>
@endif

@if (session()->has('danger') || session()->has('error'))
    <script>
        if (typeof toastr !== 'undefined') {
            toastr.error("{{ session('danger') ?? session('error') }}");
        }
    </script>
@endif
