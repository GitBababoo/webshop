<?php $siteName = getSetting('site_name','Shopee TH'); ?>
<!-- ── Lightbox Modal ── -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <button type="button" class="btn-close btn-close-white ms-auto mb-2" data-bs-dismiss="modal"></button>
      <img id="lightboxImg" src="" class="img-fluid rounded" alt="">
    </div>
  </div>
</div>

<!-- ── Toast Container ── -->
<div class="toast-container" id="toastContainer"></div>

<!-- ── Back to Top ── -->
<button class="back-to-top" title="กลับขึ้นด้านบน"><i class="bi bi-chevron-up"></i></button>

<!-- ── Footer ── -->
<footer class="site-footer">
  <div class="container-xl">
    <div class="row g-4">
      <!-- Brand -->
      <div class="col-md-3">
        <div class="footer-brand mb-3"><?= e($siteName) ?></div>
        <p style="font-size:13px;line-height:1.8">แพลตฟอร์มช้อปปิ้งออนไลน์ที่ดีที่สุด ราคาถูก ส่งไว ปลอดภัย</p>
        <div class="social-links">
          <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" title="LINE"><i class="bi bi-line"></i></a>
          <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" title="TikTok"><i class="bi bi-tiktok"></i></a>
          <a href="#" title="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
      <!-- Links -->
      <div class="col-6 col-md-2">
        <h6>เกี่ยวกับเรา</h6>
        <a href="/webshop/page.php?slug=about">เกี่ยวกับ <?= e($siteName) ?></a>
        <a href="/webshop/page.php?slug=careers">ร่วมงานกับเรา</a>
        <a href="/webshop/page.php?slug=press">ห้องข่าว</a>
        <a href="/webshop/page.php?slug=policies">นโยบายสิทธิ์</a>
        <a href="/webshop/page.php?slug=privacy">นโยบายความเป็นส่วนตัว</a>
      </div>
      <div class="col-6 col-md-2">
        <h6>ช่วยเหลือ</h6>
        <a href="/webshop/page.php?slug=faq">คำถามที่พบบ่อย</a>
        <a href="/webshop/page.php?slug=shipping">การจัดส่ง</a>
        <a href="/webshop/page.php?slug=return-policy">การคืนสินค้า</a>
        <a href="/webshop/page.php?slug=payment">วิธีชำระเงิน</a>
        <a href="/webshop/page.php?slug=contact">ติดต่อเรา</a>
      </div>
      <div class="col-6 col-md-2">
        <h6>ขายของกับเรา</h6>
        <a href="#">เริ่มขายบน <?= e($siteName) ?></a>
        <a href="#">วิธีการสมัครร้านค้า</a>
        <a href="#">ศูนย์การเรียนรู้ผู้ขาย</a>
        <a href="#">Flash Sale สำหรับผู้ขาย</a>
      </div>
      <!-- Payment + Delivery -->
      <div class="col-6 col-md-3">
        <h6>ช่องทางชำระเงิน</h6>
        <div class="payment-icons mb-3">
          <span class="payment-icon d-flex align-items-center justify-content-center" style="width:50px;height:28px;background:#1a1a6e;color:#fff;font-size:11px;font-weight:700;">VISA</span>
          <span class="payment-icon d-flex align-items-center justify-content-center" style="width:50px;height:28px;background:#eb001b;color:#fff;font-size:10px;font-weight:700;">MASTER</span>
          <span class="payment-icon d-flex align-items-center justify-content-center" style="width:50px;height:28px;background:#f79e1b;color:#fff;font-size:11px;font-weight:700;">COD</span>
          <span class="payment-icon d-flex align-items-center justify-content-center" style="width:50px;height:28px;background:#004F9F;color:#fff;font-size:10px;font-weight:700;">SCB</span>
        </div>
        <h6>บริษัทขนส่ง</h6>
        <div class="d-flex gap-2 flex-wrap" style="font-size:12px;color:#888">
          <span>J&T Express</span>
          <span>Flash Express</span>
          <span>Kerry</span>
          <span>Thailand Post</span>
          <span>DHL</span>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="mb-0">© <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved. | พัฒนาด้วย ❤️ ในประเทศไทย</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/webshop/assets/js/main.js"></script>
<?php if (isset($extraJs)): ?><?= $extraJs ?><?php endif; ?>
</body>
</html>
