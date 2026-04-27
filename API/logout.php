<?php
session_start();
session_destroy();
header("Location: /"); // Ubah dari index.php
exit();
?>