<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<!-- Javascript -->
<script src="{{ asset('assets/admin/assets/plugins/popper.min.js') }}"></script>


{{-- <!-- Charts JS -->
<script src="{{ asset('assets/admin/assets/plugins/chart.js/chart.min.js') }}"></script>
<script src="{{ asset('assets/admin/assets/js/index-charts.js') }}"></script> --}}

<!-- Page Specific JS -->
<script src="{{ asset('assets/admin/assets/js/app.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@if(session()->has('success'))
<script>
    toastr.success("{{session()->get('success')}}")
</script>
@endif
@yield('script')
