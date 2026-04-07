// Sidebar toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function () {
  const sidebar = document.getElementById('sidebar');
  if (window.innerWidth <= 768) {
    sidebar.classList.toggle('show');
  } else {
    sidebar.classList.toggle('collapsed');
  }
});

// Auto-dismiss alerts
setTimeout(function () {
  document.querySelectorAll('.alert.fade').forEach(function (el) {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
    bsAlert.close();
  });
}, 4000);

// DataTables default init
document.addEventListener('DOMContentLoaded', function () {
  if (typeof $.fn.DataTable !== 'undefined') {
    $('.datatable').DataTable({
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/th.json'
      },
      pageLength: 25,
      responsive: true
    });
  }
});

// Confirm delete
document.querySelectorAll('[data-confirm]').forEach(function (el) {
  el.addEventListener('click', function (e) {
    e.preventDefault();
    const href = this.getAttribute('href') || this.getAttribute('data-href');
    const msg  = this.getAttribute('data-confirm') || 'ยืนยันการลบ?';
    Swal.fire({
      title: 'ยืนยัน',
      text: msg,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#EE4D2D',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'ใช่, ดำเนินการ',
      cancelButtonText: 'ยกเลิก'
    }).then(function (r) {
      if (r.isConfirmed && href) window.location.href = href;
    });
  });
});

// Image preview
document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
  input.addEventListener('change', function () {
    const targetId = this.getAttribute('data-preview');
    const preview  = document.getElementById(targetId);
    if (preview && this.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        if (preview.tagName === 'IMG') {
          preview.src = e.target.result;
        } else {
          preview.style.backgroundImage = 'url(' + e.target.result + ')';
        }
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
});

// Slug generator
document.querySelectorAll('[data-slug-source]').forEach(function (input) {
  input.addEventListener('input', function () {
    const targetId = this.getAttribute('data-slug-source');
    const target   = document.getElementById(targetId);
    if (target && !target.getAttribute('data-manual')) {
      target.value = slugify(this.value);
    }
  });
});

document.querySelectorAll('[data-slug-target]').forEach(function (input) {
  input.addEventListener('input', function () {
    this.setAttribute('data-manual', '1');
    this.value = slugify(this.value);
  });
});

function slugify(str) {
  return str.toLowerCase().trim().replace(/\s+/g, '-').replace(/[^\w\-ก-๙]/g, '').replace(/\-+/g, '-');
}

// Color picker sync
document.querySelectorAll('input[type="color"]').forEach(function (picker) {
  const textId = picker.getAttribute('data-text-sync');
  const textEl = textId ? document.getElementById(textId) : null;
  if (textEl) {
    picker.addEventListener('input', function () { textEl.value = this.value; });
    textEl.addEventListener('input', function () {
      if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) picker.value = this.value;
    });
  }
});

// Toast helper
function showToast(message, type = 'success') {
  const toastEl = document.createElement('div');
  toastEl.className = 'toast align-items-center text-bg-' + type + ' border-0 position-fixed bottom-0 end-0 m-3';
  toastEl.style.zIndex = '9999';
  toastEl.setAttribute('role', 'alert');
  toastEl.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
  document.body.appendChild(toastEl);
  const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
  toast.show();
  toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// Select all checkboxes
const selectAll = document.getElementById('selectAll');
if (selectAll) {
  selectAll.addEventListener('change', function () {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = this.checked);
  });
}
