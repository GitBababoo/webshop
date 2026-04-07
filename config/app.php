<?php
define('APP_NAME',    'Shopee TH Admin');
define('APP_VERSION', '1.0.0');
define('APP_URL',     'http://localhost/webshop');
define('ADMIN_URL',   APP_URL . '/admin');
define('UPLOAD_DIR',  dirname(__DIR__) . '/uploads/');
define('UPLOAD_URL',  APP_URL . '/uploads/');
define('SESSION_NAME','shopee_admin_sess');
define('CSRF_TOKEN_NAME', '_csrf_token');

define('ROLES', [
    'superadmin' => 'Super Administrator',
    'admin'      => 'Administrator',
    'seller'     => 'Seller',
    'buyer'      => 'Buyer',
]);

define('ORDER_STATUSES', [
    'pending'          => ['label' => 'รอดำเนินการ',   'class' => 'warning'],
    'confirmed'        => ['label' => 'ยืนยันแล้ว',    'class' => 'info'],
    'processing'       => ['label' => 'กำลังเตรียม',    'class' => 'primary'],
    'shipped'          => ['label' => 'จัดส่งแล้ว',    'class' => 'primary'],
    'delivered'        => ['label' => 'ส่งถึงแล้ว',    'class' => 'success'],
    'completed'        => ['label' => 'สำเร็จ',        'class' => 'success'],
    'cancelled'        => ['label' => 'ยกเลิก',        'class' => 'danger'],
    'return_requested' => ['label' => 'ขอคืนสินค้า',   'class' => 'warning'],
    'returned'         => ['label' => 'คืนสินค้าแล้ว', 'class' => 'secondary'],
]);

define('PAYMENT_STATUSES', [
    'pending'  => ['label' => 'รอชำระ',     'class' => 'warning'],
    'paid'     => ['label' => 'ชำระแล้ว',  'class' => 'success'],
    'failed'   => ['label' => 'ล้มเหลว',   'class' => 'danger'],
    'refunded' => ['label' => 'คืนเงินแล้ว','class' => 'info'],
]);
