        </div><!-- /.page-content -->
    </main><!-- /.content-area -->

    <!-- Footer -->
    <footer class="app-footer">
        <div class="footer-inner">
            <div class="footer-left">
                <strong>SIMPELDAR</strong> &mdash; Sistem Informasi Manajemen Pelayanan Darah
            </div>
            <div class="footer-right">
                &copy; 2026 RS Kanker Dharmais
            </div>
        </div>
    </footer>

    </div><!-- /.app-container -->

</div><!-- /.app-wrapper -->

<!-- jQuery 3.7 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.1/js/responsive.bootstrap5.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
<!-- Custom App JS -->
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>

<!-- Page-specific JS (loaded after jQuery + DataTables + app.js) -->
<?php if (isset($rawat_inap_js) && $rawat_inap_js): ?>
<script src="<?php echo base_url('assets/js/rawat-inap.js?v=' . time()); ?>"></script>
<?php endif; ?>

</body>
</html>
