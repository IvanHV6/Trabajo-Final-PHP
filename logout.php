<?php
session_start();

// destruir sesion
$_SESSION = array();
session_destroy();

// redirigir
header("Location: index.php");
exit();
?>