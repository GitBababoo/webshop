<?php $siteName = getSetting('site_name','Shopee TH'); ?>
</div><!-- .content-area -->
<footer class="admin-footer">
  <div class="d-flex justify-content-between align-items-center">
    <span class="text-muted small">&copy; <?= date('Y') ?> <?= e($siteName) ?> Admin Panel v<?= APP_VERSION ?></span>
    <span class="text-muted small">เข้าสู่ระบบในฐานะ <strong><?= e(currentUser()['name']) ?></strong></span>
  </div>
</footer>
</div><!-- .main-content -->
</div><!-- .wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= ADMIN_URL ?>/assets/js/admin.js"></script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
