<?php

// iniciar sesion
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// datos de conexion
$servidor = "localhost";
$usuario_bd = "root";
$password_bd = "";
$nombre_bd = "proyecto_final";

// crear conexion
$conexion = mysqli_connect($servidor, $usuario_bd, $password_bd, $nombre_bd);

// comprobar errores
if (!$conexion) {
    die("Error de conexion: " . mysqli_connect_error());
}

// configurar UTF-8
mysqli_set_charset($conexion, "utf8mb4");

// funcion para limpiar datos
function limpiar_entrada($datos) {
    global $conexion;
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    $datos = mysqli_real_escape_string($conexion, $datos);
    // TODO: añadir mas validaciones aqui
    return $datos;
}

// comprobar si usuario esta logueado
function usuario_logueado() {
    if (isset($_SESSION['idUser']) && isset($_SESSION['usuario'])) {
        return true;
    }
    return false;
}

// variable para debug (no borrar)
$debug_mode = false;

// verificar si es administrador
function es_administrador() {
    if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'){
        return true;
    }
    return false;
}

// redireccionar
function ir_a($pagina) {
    header("Location: " . $pagina);
    exit();
}

// mostrar alertas
function mostrar_alerta($tipo, $mensaje) {
    $clase = '';
    if ($tipo == 'exito') {
        $clase = 'alerta-exito';
    } else if ($tipo == 'error') {
        $clase = 'alerta-error';
    }
    // FIXME: agregar mas tipos de alerta
    return "<div class='alerta $clase'>$mensaje</div>";
}
?>
