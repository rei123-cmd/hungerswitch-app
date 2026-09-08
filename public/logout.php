<?php
include 'config.php'; // Biar config yang start session
session_destroy();    // Hancurkan session
header("Location: index.php");
exit();
?>