<?php
require_once dirname(__DIR__) . '/includes/auth.php';
logout();
header('Location: ' . ADMIN_URL . '/login.php?logged_out=1');
exit;
