/* ============================================================
   Shopee TH – Main Frontend JS
   ============================================================ */
'use strict';

/* ── Toast Notification ── */
function showToast(message, type = 'success', duration = 3000) {
  const container = document.getElementById('toastContainer') || (() => {
    const c = document.createElement('div');
    c.id = 'toastContainer';
    c.className = 'toast-container';
    document.body.appendChild(c);
    return c;
  })();
  const toast = document.createElement('div');
  const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill', warning: 'bi-exclamation-triangle-fill' };
  toast.className = `site-toast ${type}`;
  toast.innerHTML = `<i class="bi ${icons[type] || icons.success}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(10px)'; toast.style.transition = '.3s'; setTimeout(() => toast.remove(), 300); }, duration);
}

/* ── Cart Badge Update ── */
function updateCartBadge(count) {
  document.querySelectorAll('.cart-badge, .nav-badge[data-cart]').forEach(el => {
    el.textContent = count;
    el.style.display = count > 0 ? '' : 'none';
  });
}

/* ── Add to Cart ── */
async function addToCart(productId, quantity = 1, skuId = null) {
  try {
    const form = new FormData();
    form.append('product_id', productId);
    form.append('quantity', quantity);
    if (skuId) form.append('sku_id', skuId);
    form.append('action', 'add');

    const res  = await fetch('/webshop/api/cart.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      updateCartBadge(data.cart_count);
      showToast('เพิ่มสินค้าลงตะกร้าแล้ว!', 'success');
    } else {
      showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
    }
  } catch (e) {
    showToast('เกิดข้อผิดพลาด กรุณาลองใหม่', 'error');
  }
}

/* ── Toggle Wishlist ── */
async function toggleWishlist(productId, btn) {
  try {
    const form = new FormData();
    form.append('product_id', productId);
    form.append('action', 'toggle');

    const res  = await fetch('/webshop/api/wishlist.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      btn.classList.toggle('active', data.in_wishlist);
      btn.innerHTML = `<i class="bi ${data.in_wishlist ? 'bi-heart-fill' : 'bi-heart'}"></i>`;
      showToast(data.in_wishlist ? 'เพิ่มใน Wishlist แล้ว' : 'นำออกจาก Wishlist แล้ว', 'info');
    } else if (data.redirect) {
      window.location.href = data.redirect;
    }
  } catch (e) { showToast('เกิดข้อผิดพลาด', 'error'); }
}

/* ── Quantity Controls ── */
document.addEventListener('click', function(e) {
  const incBtn = e.target.matches('.qty-btn[data-action="inc"]') ? e.target : e.target.closest('.qty-btn[data-action="inc"]');
  const decBtn = e.target.matches('.qty-btn[data-action="dec"]') ? e.target : e.target.closest('.qty-btn[data-action="dec"]');

  if (incBtn) {
    const input = incBtn.closest('.quantity-ctrl').querySelector('.qty-input');
    if (!input) return;
    const max   = parseInt(input.dataset.max || input.getAttribute('max') || 999);
    const cur   = parseInt(input.value) || 0;
    if (cur >= max) {
      showToast(max <= 0 ? 'สินค้าหมดสต็อก' : `สินค้ามีเพียง ${max} ชิ้น`, 'warning');
    } else {
      input.value = cur + 1;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  if (decBtn) {
    const input = decBtn.closest('.quantity-ctrl').querySelector('.qty-input');
    if (!input) return;
    const min   = parseInt(input.dataset.min || input.getAttribute('min') || 1);
    const cur   = parseInt(input.value) || 1;
    if (cur <= min) {
      showToast('จำนวนขั้นต่ำคือ 1 ชิ้น', 'warning');
    } else {
      input.value = cur - 1;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }
});

/* ── Product Gallery Thumbnails ── */
document.addEventListener('click', function(e) {
  const thumb = e.target.closest('.thumb');
  if (!thumb) return;
  const src    = thumb.dataset.src;
  const gallery = thumb.closest('.product-gallery');
  if (!gallery) return;
  const main   = gallery.querySelector('#mainImage');
  if (main && src) main.src = src;
  gallery.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
  thumb.classList.add('active');
});

/* ── Variant Selection ── */
document.addEventListener('click', function(e) {
  const opt = e.target.closest('.variant-opt');
  if (!opt || opt.classList.contains('disabled')) return;
  const group = opt.closest('.variant-group');
  if (group) group.querySelectorAll('.variant-opt').forEach(o => o.classList.remove('active'));
  opt.classList.add('active');
  // Update price if data-price exists
  const price = opt.dataset.price;
  const origPrice = opt.dataset.origPrice;
  if (price) {
    const priceEl = document.getElementById('productPrice');
    if (priceEl) priceEl.textContent = '฿' + parseFloat(price).toLocaleString('th-TH', {minimumFractionDigits:0});
  }
});

/* ── Cart Item Update (cart page) ── */
document.addEventListener('change', async function(e) {
  if (!e.target.matches('.cart-qty-input')) return;
  const input  = e.target;
  const itemId = input.dataset.itemId;
  if (!itemId) return;
  const form = new FormData();
  form.append('cart_item_id', itemId);
  form.append('quantity', input.value);
  form.append('action', 'update');
  try {
    const res  = await fetch('/webshop/api/cart.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      // Update subtotal displays
      if (data.item_subtotal) {
        const sub = document.querySelector(`[data-item-sub="${itemId}"]`);
        if (sub) sub.textContent = '฿' + parseFloat(data.item_subtotal).toLocaleString('th-TH', {minimumFractionDigits:0});
      }
      if (data.cart_total) {
        const tot = document.getElementById('cartTotal');
        if (tot) tot.textContent = '฿' + parseFloat(data.cart_total).toLocaleString('th-TH', {minimumFractionDigits:0});
      }
      updateCartBadge(data.cart_count);
    }
  } catch (e) { showToast('อัพเดตตะกร้าไม่สำเร็จ', 'error'); }
});

/* ── Remove Cart Item ── */
document.addEventListener('click', async function(e) {
  const removeBtn = e.target.closest('[data-remove-item]');
  if (!removeBtn) return;
  if (!confirm('ลบสินค้านี้ออกจากตะกร้า?')) return;
  const itemId = removeBtn.dataset.removeItem;
  const form   = new FormData();
  form.append('cart_item_id', itemId);
  form.append('action', 'remove');
  try {
    const res  = await fetch('/webshop/api/cart.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      const row = document.querySelector(`[data-item-row="${itemId}"]`);
      if (row) { row.style.opacity = '0'; row.style.transition = '.3s'; setTimeout(() => row.remove(), 300); }
      updateCartBadge(data.cart_count);
      if (data.cart_total !== undefined) {
        const tot = document.getElementById('cartTotal');
        if (tot) tot.textContent = '฿' + parseFloat(data.cart_total).toLocaleString('th-TH', {minimumFractionDigits:0});
      }
      showToast('ลบสินค้าแล้ว', 'info');
    }
  } catch (e) { showToast('เกิดข้อผิดพลาด', 'error'); }
});

/* ── Select All Cart Items ── */
document.addEventListener('change', function(e) {
  if (e.target.id === 'selectAll') {
    document.querySelectorAll('.cart-item-check').forEach(cb => cb.checked = e.target.checked);
    updateCartSelection();
  }
  if (e.target.classList.contains('cart-item-check')) updateCartSelection();
});
function updateCartSelection() {
  let total = 0;
  document.querySelectorAll('.cart-item-check:checked').forEach(cb => {
    const row = cb.closest('[data-item-row]');
    if (row) {
      const price = parseFloat(row.dataset.price || 0);
      const qty   = parseInt(row.querySelector('.cart-qty-input')?.value || 1);
      total += price * qty;
    }
  });
  const totalEl = document.getElementById('selectedTotal');
  if (totalEl) totalEl.textContent = '฿' + total.toLocaleString('th-TH', {minimumFractionDigits:0});
}

/* ── Payment Method Selection ── */
document.addEventListener('click', function(e) {
  const opt = e.target.closest('.payment-option');
  if (!opt) return;
  document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
  opt.classList.add('selected');
  const input = opt.querySelector('input[type=radio]');
  if (input) input.checked = true;
  // Show/hide bank slip
  const method = opt.dataset.method;
  const bankDetails = document.getElementById('bankTransferDetails');
  if (bankDetails) bankDetails.style.display = method === 'bank_transfer' ? 'block' : 'none';
});

/* ── Address Selection (checkout) ── */
document.addEventListener('click', function(e) {
  const addr = e.target.closest('.address-card');
  if (!addr) return;
  document.querySelectorAll('.address-card').forEach(a => a.classList.remove('selected'));
  addr.classList.add('selected');
  const input = addr.querySelector('input[type=radio]');
  if (input) { input.checked = true; input.dispatchEvent(new Event('change')); }
  // Update shipping provider info
  const providerBox = document.getElementById('providerBox');
  const zoneLabel   = document.getElementById('provinceLabel');
  if (zoneLabel) zoneLabel.textContent = addr.dataset.province || '';
});

/* ── Image Lightbox (reviews) ── */
document.addEventListener('click', function(e) {
  const img = e.target.closest('.review-img');
  if (!img) return;
  const modal = document.getElementById('lightboxModal');
  if (!modal) return;
  const modalImg = modal.querySelector('#lightboxImg');
  if (modalImg) modalImg.src = img.src;
  new bootstrap.Modal(modal).show();
});

/* ── Search Input – Live Suggestions (simple) ── */
const searchInput = document.getElementById('siteSearch');
if (searchInput) {
  let debounceTimer;
  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 2) { hideSuggestions(); return; }
    debounceTimer = setTimeout(async () => {
      try {
        const res  = await fetch(`/webshop/api/search-suggest.php?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        showSuggestions(data.suggestions || []);
      } catch (e) {}
    }, 300);
  });
  searchInput.addEventListener('blur', () => setTimeout(hideSuggestions, 200));
}
function showSuggestions(items) {
  let box = document.getElementById('searchSuggest');
  if (!box) {
    box = document.createElement('div');
    box.id = 'searchSuggest';
    box.className = 'position-absolute bg-white border rounded-bottom shadow-sm w-100';
    box.style.cssText = 'top:100%;left:0;z-index:9999;max-height:280px;overflow-y:auto;';
    searchInput.closest('.position-relative, .input-group')?.appendChild(box);
  }
  if (!items.length) { hideSuggestions(); return; }
  box.innerHTML = items.map(s => `<a href="/webshop/search.php?q=${encodeURIComponent(s)}" class="d-flex align-items-center gap-2 px-3 py-2 text-dark hover-bg border-bottom" style="font-size:13px;"><i class="bi bi-search text-muted small"></i>${s}</a>`).join('');
  box.style.display = 'block';
}
function hideSuggestions() {
  const box = document.getElementById('searchSuggest');
  if (box) box.style.display = 'none';
}

/* ── Price Range Filter ── */
const priceMin = document.getElementById('priceMin');
const priceMax = document.getElementById('priceMax');
if (priceMin && priceMax) {
  function applyPriceFilter() {
    const url  = new URL(window.location);
    if (priceMin.value) url.searchParams.set('price_min', priceMin.value);
    else url.searchParams.delete('price_min');
    if (priceMax.value) url.searchParams.set('price_max', priceMax.value);
    else url.searchParams.delete('price_max');
    url.searchParams.delete('page');
    window.location.href = url.toString();
  }
  priceMin.addEventListener('keypress', e => e.key === 'Enter' && applyPriceFilter());
  priceMax.addEventListener('keypress', e => e.key === 'Enter' && applyPriceFilter());
  document.getElementById('applyPriceFilter')?.addEventListener('click', applyPriceFilter);
}

/* ── Sort Buttons ── */
document.addEventListener('click', function(e) {
  const sortBtn = e.target.closest('.sort-btn[data-sort]');
  if (!sortBtn) return;
  const url = new URL(window.location);
  url.searchParams.set('sort', sortBtn.dataset.sort);
  url.searchParams.delete('page');
  window.location.href = url.toString();
});

/* ── Filter Chips ── */
document.addEventListener('click', function(e) {
  const chip = e.target.closest('.filter-chip[data-filter]');
  if (!chip) return;
  const url   = new URL(window.location);
  const key   = chip.dataset.filterKey;
  const value = chip.dataset.filter;
  if (chip.classList.contains('active')) {
    url.searchParams.delete(key);
  } else {
    url.searchParams.set(key, value);
  }
  url.searchParams.delete('page');
  window.location.href = url.toString();
});

/* ── Back to Top ── */
const backToTop = document.querySelector('.back-to-top');
if (backToTop) {
  window.addEventListener('scroll', () => {
    backToTop.classList.toggle('show', window.scrollY > 400);
  });
  backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

/* ── Sticky Navbar Shadow on Scroll ── */
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.site-navbar');
  if (navbar) navbar.classList.toggle('shadow', window.scrollY > 10);
});

/* ── Image Preview Upload (profile) ── */
document.addEventListener('change', function(e) {
  if (!e.target.matches('[data-preview]')) return;
  const previewId = e.target.dataset.preview;
  const preview   = document.getElementById(previewId);
  if (!preview || !e.target.files[0]) return;
  const reader = new FileReader();
  reader.onload = ev => preview.src = ev.target.result;
  reader.readAsDataURL(e.target.files[0]);
});

/* ── Voucher Code Toggle ── */
document.getElementById('applyVoucher')?.addEventListener('click', async function() {
  const code = document.getElementById('voucherCode')?.value.trim();
  if (!code) { showToast('กรุณากรอกโค้ดส่วนลด', 'warning'); return; }
  const form = new FormData();
  form.append('code', code);
  form.append('action', 'apply');
  try {
    const res  = await fetch('/webshop/api/voucher.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      showToast(`ใช้โค้ด ${code} ลดได้ ${data.discount_text}`, 'success');
      const discountEl = document.getElementById('voucherDiscount');
      if (discountEl) discountEl.textContent = '-' + data.discount_text;
      document.getElementById('checkoutTotal')?.textContent && recalcTotal();
    } else {
      showToast(data.message || 'โค้ดไม่ถูกต้องหรือหมดอายุแล้ว', 'error');
    }
  } catch (e) { showToast('เกิดข้อผิดพลาด', 'error'); }
});

/* ── Follow Shop ── */
document.addEventListener('click', async function(e) {
  const btn = e.target.closest('[data-follow-shop]');
  if (!btn) return;
  const shopId = btn.dataset.followShop;
  const form   = new FormData();
  form.append('shop_id', shopId);
  form.append('action', 'toggle');
  try {
    const res  = await fetch('/webshop/api/follow.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.success) {
      btn.classList.toggle('btn-orange', data.following);
      btn.classList.toggle('btn-outline-orange', !data.following);
      btn.innerHTML = data.following ? '<i class="bi bi-person-check-fill me-1"></i>ติดตามแล้ว' : '<i class="bi bi-person-plus me-1"></i>ติดตาม';
      showToast(data.following ? 'ติดตามร้านนี้แล้ว' : 'เลิกติดตามแล้ว', 'info');
    } else if (data.redirect) window.location.href = data.redirect;
  } catch (e) { showToast('เกิดข้อผิดพลาด', 'error'); }
});

/* ── Star Rating Input ── */
document.addEventListener('click', function(e) {
  const star = e.target.closest('.star-input');
  if (!star) return;
  const val  = parseInt(star.dataset.val);
  const wrap = star.closest('.stars-input-wrap');
  if (!wrap) return;
  wrap.querySelectorAll('.star-input').forEach((s, i) => {
    s.classList.toggle('bi-star-fill', i < val);
    s.classList.toggle('bi-star', i >= val);
    s.style.color = i < val ? '#ffa500' : '#ccc';
  });
  const hiddenInput = wrap.querySelector('input[type=hidden]');
  if (hiddenInput) hiddenInput.value = val;
});

/* ── Copy to Clipboard ── */
document.addEventListener('click', function(e) {
  const copyBtn = e.target.closest('[data-copy]');
  if (!copyBtn) return;
  navigator.clipboard.writeText(copyBtn.dataset.copy).then(() => showToast('คัดลอกแล้ว!', 'info', 1500));
});

/* ── On DOM Ready ── */
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
  // Init Swiper if exists
  if (typeof Swiper !== 'undefined') {
    const heroSwiper = document.querySelector('.hero-swiper');
    if (heroSwiper) {
      new Swiper(heroSwiper, {
        loop: true, autoplay: { delay: 4000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
      });
    }
    const prodSwiper = document.querySelector('.product-swiper');
    if (prodSwiper) {
      new Swiper(prodSwiper, {
        slidesPerView: 2, spaceBetween: 12,
        breakpoints: { 576: { slidesPerView: 3 }, 768: { slidesPerView: 4 }, 1024: { slidesPerView: 5 } },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
      });
    }
  }
  // Flash sale countdown
  const countdown = document.getElementById('flashCountdown');
  if (countdown) {
    const endTime = parseInt(countdown.dataset.end) * 1000;
    setInterval(() => {
      const diff = endTime - Date.now();
      if (diff <= 0) { countdown.innerHTML = '<span class="countdown-box">00</span>:<span class="countdown-box">00</span>:<span class="countdown-box">00</span>'; return; }
      const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
      const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
      const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
      countdown.innerHTML = `<span class="countdown-box">${h}</span>:<span class="countdown-box">${m}</span>:<span class="countdown-box">${s}</span>`;
    }, 1000);
  }
  // Select all images for zoom
  document.querySelectorAll('.gallery-main img').forEach(img => {
    img.style.cursor = 'zoom-in';
  });

  // Global broken image handler for ALL images (Universal Guard)
  document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
      const img = e.target;
      if (img.dataset.retry) return; // Prevent infinite loop if placeholder also fails
      img.dataset.retry = "1";
      
      const width  = img.width || 200;
      const height = img.height || 200;
      const text   = img.alt || 'No Image';
      
      // If it's a product or category image, use a nicer placeholder
      if (img.classList.contains('product-img') || img.classList.contains('cart-img') || img.classList.contains('thumb')) {
        img.src = `https://placehold.co/${width}x${height}/f1f1f1/ee4d2d?text=${encodeURIComponent(text)}`;
      } else {
        // Fallback for avatars or others
        img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(text)}&background=random&color=fff&size=${width}`;
      }
      img.onerror = null; 
    }
  }, true);
});
