<footer class="footer navbar-themed-section" style="background-color: #f8f9fa;">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-center text-sm-left d-block d-sm-inline-block themed-text">Sistem Manajemen Mutu
            Perkuliahan</span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center themed-text">Copyright &copy;
            {{ date('Y') }} All rights reserved.</span>
    </div>
</footer>

</div> <!-- main-panel -->
</div> <!-- page-body-wrapper -->
</div> <!-- container-scroller (jika ada di header) -->

<!-- Plugins JS -->
<script src="{{ asset('/assets/template/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/progressbar.js/progressbar.min.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('/assets/template/js/select2.js') }}"></script>

<!-- Template JS -->
<script src="{{ asset('/assets/template/js/off-canvas.js') }}"></script>
<script src="{{ asset('/assets/template/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('/assets/template/js/template.js') }}"></script>
<script src="{{ asset('/assets/template/js/settings.js') }}"></script>
<script src="{{ asset('/assets/template/js/todolist.js') }}"></script>

<!-- Custom JS -->
<script src="{{ asset('/assets/template/js/jquery.cookie.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/template/js/dashboard.js') }}"></script>
<script src="{{ asset('/assets/template/js/Chart.roundedBarCharts.js') }}"></script>
<script src="{{ asset('/node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('.dataTable').DataTable({
            "aaSorting": []
        });
    });
</script>

</body>

</html>
