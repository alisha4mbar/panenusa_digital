<?php
session_start();
session_unset();
session_destroy();
setcookie('panenusa_auth', '', time() - 3600, '/'); // Hapus cookie agar tidak bisa re-login otomatis
header("Location: /"); 
exit();
?>