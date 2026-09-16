<footer class="footer navbar-themed-section" style="background-color: {{ $themeColor }}">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-center text-sm-left d-block d-sm-inline-block themed-text">Sistem Manajemen Mutu
            Perkuliahan</span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center themed-text">Copyright &copy;
            {{ date('Y') }} All rights reserved.</span>
    </div>
</footer>
{{-- partial --}}
</div>
{{-- main-panel ends --}}
</div>
{{-- page-body-wrapper ends --}}
</div>
{{-- container-scroller --}}
{{-- plugins:js --}}
<script src="{{ asset('/assets/template/vendors/js/vendor.bundle.base.js') }}"></script>
{{-- endinject --}}
{{-- Plugin js for this page --}}
<script src="{{ asset('/assets/template/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/progressbar.js/progressbar.min.js') }}"></script>
<script src="{{ asset('/assets/template/vendors/select2/select2.min.js') }}"></script>
<script src="{{ asset('/assets/template/js/select2.js') }}"></script>
{{-- End plugin js for this page --}}
{{-- inject:js --}}
<script src="{{ asset('/assets/template/js/off-canvas.js') }}"></script>
<script src="{{ asset('/assets/template/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('/assets/template/js/template.js') }}?v={{ time() }}"></script>
<script src="{{ asset('/assets/template/js/settings.js') }}"></script>
<script src="{{ asset('/assets/template/js/todolist.js') }}"></script>
{{-- endinject --}}
{{-- Custom js for this page --}}
<script src="{{ asset('/assets/template/js/jquery.cookie.js') }}" type="text/javascript"></script>
<script src="{{ asset('/assets/template/js/dashboard.js') }}"></script>
<script src="{{ asset('/assets/template/js/Chart.roundedBarCharts.js') }}"></script>
<script src="{{ asset('/node_modules/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $.fn.dataTable.ext.errMode = 'none';

            function setupDataTableLayout() {
                $('.dataTable').each(function() {
                    var $table = $(this);
                    
                    var $parentResponsive = $table.parent('.table-responsive');
                    if ($parentResponsive.length > 0 && !$table.parent().hasClass('dataTables_wrapper')) {
                        $table.unwrap();
                    }

                    if (!$.fn.DataTable.isDataTable(this)) {
                        $table.DataTable({
                            "aaSorting": [],
                            "retrieve": true
                        });
                    }

                    if ($table.parent('.table-responsive').length === 0) {
                        $table.wrap('<div class="table-responsive" style="width:100%; overflow-x:auto; margin-bottom:1rem; clear:both;"></div>');
                    }
                });
            }

            setupDataTableLayout();
            setTimeout(setupDataTableLayout, 200);
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>
@if (isset($themeScript))
    <script src="{{ asset('assets/js/theme-utils.js') }}"></script>
@endif
</body>

</html>
