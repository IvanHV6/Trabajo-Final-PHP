<?php
$titulo = "Administración de Noticias";
require_once '../config/database.php';

// Verificar que el usuario esté logueado Y sea administrador
if (!usuario_logueado()) {
    header("Location: ../login.php");
    exit();
}

if (!es_administrador()) {
    header("Location: ../index.php");
    exit();
}

$idUser = $_SESSION['idUser'];
$mensaje = "";
$tipo_mensaje = "";

// Variable para edición
$editando = false;
$noticia_editar = null;

// CREAR NUEVA NOTICIA
if (isset($_POST['crear_noticia'])) {
    
    $titulo_noticia = limpiar_entrada($_POST['titulo']);
    $imagen = limpiar_entrada($_POST['imagen']);
    $texto = limpiar_entrada($_POST['texto']);
    $fecha = limpiar_entrada($_POST['fecha']);
    
    if (empty($titulo_noticia) || empty($imagen) || empty($texto) || empty($fecha)) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_mensaje = "error";
    } else {
        
        // Verificar que el título no exista ya
        $sql_verificar = "SELECT idNoticia FROM noticias WHERE titulo = '$titulo_noticia'";
        $resultado_verificar = mysqli_query($conexion, $sql_verificar);
        
        if (mysqli_num_rows($resultado_verificar) > 0) {
            $mensaje = "Ya existe una noticia con ese título.";
            $tipo_mensaje = "error";
        } else {
            
            $sql_insert = "INSERT INTO noticias (titulo, imagen, texto, fecha, idUser) 
                          VALUES ('$titulo_noticia', '$imagen', '$texto', '$fecha', $idUser)";
            
            if (mysqli_query($conexion, $sql_insert)) {
                $mensaje = "¡Noticia creada correctamente!";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Error al crear la noticia: " . mysqli_error($conexion);
                $tipo_mensaje = "error";
            }
        }
    }
}

// ACTUALIZAR NOTICIA
if (isset($_POST['actualizar_noticia'])) {
    
    $idNoticia = limpiar_entrada($_POST['idNoticia']);
    $titulo_noticia = limpiar_entrada($_POST['titulo']);
    $imagen = limpiar_entrada($_POST['imagen']);
    $texto = limpiar_entrada($_POST['texto']);
    $fecha = limpiar_entrada($_POST['fecha']);
    
    if (empty($titulo_noticia) || empty($imagen) || empty($texto) || empty($fecha)) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_mensaje = "error";
    } else {
        
        // Verificar que el título no esté en uso por otra noticia
        $sql_verificar = "SELECT idNoticia FROM noticias WHERE titulo = '$titulo_noticia' AND idNoticia != $idNoticia";
        $resultado_verificar = mysqli_query($conexion, $sql_verificar);
        
        if (mysqli_num_rows($resultado_verificar) > 0) {
            $mensaje = "Ya existe otra noticia con ese título.";
            $tipo_mensaje = "error";
        } else {
            
            $sql_update = "UPDATE noticias SET 
                          titulo = '$titulo_noticia',
                          imagen = '$imagen',
                          texto = '$texto',
                          fecha = '$fecha'
                          WHERE idNoticia = $idNoticia";
            
            if (mysqli_query($conexion, $sql_update)) {
                $mensaje = "¡Noticia actualizada correctamente!";
                $tipo_mensaje = "exito";
                header("refresh:1;url=noticias-administracion.php");
            } else {
                $mensaje = "Error al actualizar la noticia: " . mysqli_error($conexion);
                $tipo_mensaje = "error";
            }
        }
    }
}

// ELIMINAR NOTICIA
if (isset($_GET['eliminar'])) {
    
    $idNoticia = limpiar_entrada($_GET['eliminar']);
    
    $sql_delete = "DELETE FROM noticias WHERE idNoticia = $idNoticia";
    
    if (mysqli_query($conexion, $sql_delete)) {
        $mensaje = "Noticia eliminada correctamente.";
        $tipo_mensaje = "exito";
        header("refresh:1;url=noticias-administracion.php");
    } else {
        $mensaje = "Error al eliminar la noticia: " . mysqli_error($conexion);
        $tipo_mensaje = "error";
    }
}

// CARGAR NOTICIA PARA EDITAR
if (isset($_GET['editar'])) {
    $idNoticia = limpiar_entrada($_GET['editar']);
    
    $sql_editar = "SELECT * FROM noticias WHERE idNoticia = $idNoticia";
    $resultado_editar = mysqli_query($conexion, $sql_editar);
    
    if (mysqli_num_rows($resultado_editar) > 0) {
        $noticia_editar = mysqli_fetch_assoc($resultado_editar);
        $editando = true;
    }
}

// OBTENER TODAS LAS NOTICIAS
$sql_noticias = "SELECT n.*, ud.nombre, ud.apellidos 
                 FROM noticias n
                 INNER JOIN users_data ud ON n.idUser = ud.idUser
                 ORDER BY n.fecha DESC";
$resultado_noticias = mysqli_query($conexion, $sql_noticias);
$num_noticias = mysqli_num_rows($resultado_noticias);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Mi Sitio Web</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav>
        <div class="contenedor">
            <a href="../index.php" class="logo">Mi Sitio Web</a>
            
            <ul class="menu">
                <li><a href="../index.php">Inicio</a></li>
                <li><a href="../noticias.php">Noticias</a></li>
                <li><a href="usuarios-administracion.php">Usuarios</a></li>
                <li><a href="citas-administracion.php">Citas</a></li>
                <li><a href="noticias-administracion.php" class="activo">Admin Noticias</a></li>
                <li><a href="../usuario/perfil.php">Perfil</a></li>
                <li><a href="../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>
    
    <main>
<div class="contenedor">
    <h1 class="texto-centro" style="margin-bottom: 2rem;">Administración de Noticias</h1>
    
    <?php 
    if (!empty($mensaje)) {
        echo mostrar_alerta($tipo_mensaje, $mensaje);
    }
    ?>
    
    <!-- Formulario: Crear o Editar Noticia -->
    <div class="formulario">
        <h2><?php echo $editando ? 'Editar Noticia' : 'Nueva Noticia'; ?></h2>
        
        <form method="POST" action="">
            <?php if ($editando): ?>
                <input type="hidden" name="idNoticia" value="<?php echo $noticia_editar['idNoticia']; ?>">
            <?php endif; ?>
            
            <div class="campo">
                <label for="titulo">Título de la Noticia *</label>
                <input type="text" id="titulo" name="titulo" required
                       value="<?php echo $editando ? htmlspecialchars($noticia_editar['titulo']) : ''; ?>"
                       placeholder="Ej: Nueva actualización del sistema">
            </div>
            
            <div class="campo">
                <label for="imagen">Nombre de la Imagen *</label>
                <input type="text" id="imagen" name="imagen" required
                       value="<?php echo $editando ? htmlspecialchars($noticia_editar['imagen']) : ''; ?>"
                       placeholder="Ej: noticia5.jpg">
                <small style="color: #666;">Solo el nombre del archivo (debe estar en la carpeta images/)</small>
            </div>
            
            <div class="campo">
                <label for="fecha">Fecha de Publicación *</label>
                <input type="date" id="fecha" name="fecha" required
                       value="<?php echo $editando ? $noticia_editar['fecha'] : date('Y-m-d'); ?>">
            </div>
            
            <div class="campo">
                <label for="texto">Texto de la Noticia *</label>
                <textarea id="texto" name="texto" rows="8" required placeholder="Escribe el contenido de la noticia..."><?php echo $editando ? htmlspecialchars($noticia_editar['texto']) : ''; ?></textarea>
            </div>
            
            <?php if ($editando): ?>
                <button type="submit" name="actualizar_noticia" class="boton boton-principal boton-completo">
                    Actualizar Noticia
                </button>
                <a href="noticias-administracion.php" class="boton boton-secundario boton-completo" style="margin-top: 0.5rem;">
                    Cancelar
                </a>
            <?php else: ?>
                <button type="submit" name="crear_noticia" class="boton boton-principal boton-completo">
                    Crear Noticia
                </button>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Lista de Noticias -->
    <div class="tabla-contenedor" style="margin-top: 2rem;">
        <h2>Todas las Noticias (<?php echo $num_noticias; ?>)</h2>
        
        <?php if ($num_noticias > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Autor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($noticia = mysqli_fetch_assoc($resultado_noticias)): ?>
                        <tr>
                            <td><?php echo $noticia['idNoticia']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($noticia['titulo']); ?></strong>
                                
                                <small style="color: #666;">
                                    <?php echo substr(htmlspecialchars($noticia['texto']), 0, 80); ?>...
                                </small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></td>
                            <td><?php echo htmlspecialchars($noticia['nombre'] . ' ' . $noticia['apellidos']); ?></td>
                            <td>
                                <a href="noticias-administracion.php?editar=<?php echo $noticia['idNoticia']; ?>" 
                                   class="boton boton-advertencia" style="font-size: 0.9rem;">
                                    Editar
                                </a>
                                <a href="noticias-administracion.php?eliminar=<?php echo $noticia['idNoticia']; ?>" 
                                   class="boton boton-peligro" style="font-size: 0.9rem;"
                                   onclick="return confirm('¿Estás seguro de eliminar esta noticia?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alerta alerta-info" style="margin-top: 1rem;">
                <p>📭 No hay noticias. ¡Crea la primera noticia usando el formulario de arriba!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
    </main>
    
    <footer>
        <div class="contenedor">
            <p>&copy; <?php echo date('Y'); ?> - Proyecto Final PHP y MySQL</p>
            <p>Desarrollado por Iván</p>
        </div>
    </footer>

    <script src="../js/scripts.js"></script>
</body>
</html>