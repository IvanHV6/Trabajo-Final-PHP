<?php
require_once 'config/database.php';

// obtener pagina actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo . ' - ' : ''; ?>Proyecto Final</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav>
        <div class="contenedor">
            <a href="index.php" class="logo">Mi Sitio Web</a>
            
            <ul class="menu">
                <?php if (!usuario_logueado()): ?>
                    <!-- Menu para visitantes -->
                    <li><a href="index.php" <?php if($pagina_actual == 'index.php') echo 'class="activo"'; ?>>Inicio</a></li>
                    <li><a href="noticias.php" <?php if($pagina_actual == 'noticias.php') echo 'class="activo"'; ?>>Noticias</a></li>
                    <li><a href="registro.php" <?php if($pagina_actual == 'registro.php') echo 'class="activo"'; ?>>Registro</a></li>
                    <li><a href="login.php" <?php if($pagina_actual == 'login.php') echo 'class="activo"'; ?>>Login</a></li>
                
                <?php elseif (es_administrador()): ?>
                    <!-- Menu para administradores -->
                    <li><a href="index.php" <?php if($pagina_actual == 'index.php') echo 'class="activo"'; ?>>Inicio</a></li>
                    <li><a href="noticias.php" <?php if($pagina_actual == 'noticias.php') echo 'class="activo"'; ?>>Noticias</a></li>
                    <li><a href="admin/usuarios-administracion.php" <?php if($pagina_actual == 'usuarios-administracion.php') echo 'class="activo"'; ?>>Usuarios</a></li>
                    <li><a href="admin/citas-administracion.php" <?php if($pagina_actual == 'citas-administracion.php') echo 'class="activo"'; ?>>Citas</a></li>
                    <li><a href="admin/noticias-administracion.php" <?php if($pagina_actual == 'noticias-administracion.php') echo 'class="activo"'; ?>>Admin Noticias</a></li>
                    <li><a href="usuario/perfil.php" <?php if($pagina_actual == 'perfil.php') echo 'class="activo"'; ?>>Perfil</a></li>
                    <li><a href="logout.php">Cerrar Sesion</a></li>
                
                <?php else: ?>
                    <!-- Menu para usuarios normales -->
                    <li><a href="index.php" <?php if($pagina_actual == 'index.php') echo 'class="activo"'; ?>>Inicio</a></li>
                    <li><a href="noticias.php" <?php if($pagina_actual == 'noticias.php') echo 'class="activo"'; ?>>Noticias</a></li>
                    <li><a href="usuario/citaciones.php" <?php if($pagina_actual == 'citaciones.php') echo 'class="activo"'; ?>>Mis Citas</a></li>
                    <li><a href="usuario/perfil.php" <?php if($pagina_actual == 'perfil.php') echo 'class="activo"'; ?>>Perfil</a></li>
                    <li><a href="logout.php">Cerrar Sesion</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <main>
