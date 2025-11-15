<?php
$titulo = "Mis Citas";
require_once '../config/database.php';

// Verificar que el usuario esté logueado
if (!usuario_logueado()) {
    header("Location: ../login.php");
    exit();
}

$idUser = $_SESSION['idUser'];
$mensaje = "";
$tipo_mensaje = "";

// Variable para edición
$editando = false;
$cita_editar = null;

// CREAR NUEVA CITA
if (isset($_POST['crear_cita'])) {
    
    $fecha_cita = limpiar_entrada($_POST['fecha_cita']);
    $hora_cita = limpiar_entrada($_POST['hora_cita']);
    $motivo_cita = limpiar_entrada($_POST['motivo_cita']);
    
    // Combinar fecha y hora correctamente
    $fecha_hora = $fecha_cita . ' ' . $hora_cita . ':00';
    
    if (empty($fecha_cita) || empty($hora_cita) || empty($motivo_cita)) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_mensaje = "error";
    } else {
        
        // Verificar que la fecha no sea en el pasado
        $ahora = date('Y-m-d H:i:s');
        if ($fecha_hora < $ahora) {
            $mensaje = "No puedes crear una cita en el pasado.";
            $tipo_mensaje = "error";
        } else {
            
            $sql_insert = "INSERT INTO citas (idUser, fecha_cita, motivo_cita) 
                          VALUES ($idUser, '$fecha_hora', '$motivo_cita')";
            
            if (mysqli_query($conexion, $sql_insert)) {
                $mensaje = "¡Cita creada correctamente!";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Error al crear la cita: " . mysqli_error($conexion);
                $tipo_mensaje = "error";
            }
        }
    }
}

// ACTUALIZAR CITA
if (isset($_POST['actualizar_cita'])) {
    
    $idCita = limpiar_entrada($_POST['idCita']);
    $fecha_cita = limpiar_entrada($_POST['fecha_cita']);
    $hora_cita = limpiar_entrada($_POST['hora_cita']);
    $motivo_cita = limpiar_entrada($_POST['motivo_cita']);
    
    // Combinar fecha y hora
    $fecha_hora = $fecha_cita . ' ' . $hora_cita . ':00';
    
    if (empty($fecha_cita) || empty($hora_cita) || empty($motivo_cita)) {
        $mensaje = "Por favor, completa todos los campos.";
        $tipo_mensaje = "error";
    } else {
        
        $sql_update = "UPDATE citas SET 
                      fecha_cita = '$fecha_hora',
                      motivo_cita = '$motivo_cita'
                      WHERE idCita = $idCita AND idUser = $idUser";
        
        if (mysqli_query($conexion, $sql_update)) {
            $mensaje = "¡Cita actualizada correctamente!";
            $tipo_mensaje = "exito";
            header("refresh:1;url=citaciones.php");
        } else {
            $mensaje = "Error al actualizar la cita: " . mysqli_error($conexion);
            $tipo_mensaje = "error";
        }
    }
}

// ELIMINAR CITA
if (isset($_GET['eliminar'])) {
    
    $idCita = limpiar_entrada($_GET['eliminar']);
    
    $sql_delete = "DELETE FROM citas WHERE idCita = $idCita AND idUser = $idUser";
    
    if (mysqli_query($conexion, $sql_delete)) {
        $mensaje = "Cita eliminada correctamente.";
        $tipo_mensaje = "exito";
        header("refresh:1;url=citaciones.php");
    } else {
        $mensaje = "Error al eliminar la cita: " . mysqli_error($conexion);
        $tipo_mensaje = "error";
    }
}

// CARGAR CITA PARA EDITAR
if (isset($_GET['editar'])) {
    $idCita = limpiar_entrada($_GET['editar']);
    
    $sql_editar = "SELECT * FROM citas WHERE idCita = $idCita AND idUser = $idUser";
    $resultado_editar = mysqli_query($conexion, $sql_editar);
    
    if (mysqli_num_rows($resultado_editar) > 0) {
        $cita_editar = mysqli_fetch_assoc($resultado_editar);
        $editando = true;
    }
}

// OBTENER TODAS LAS CITAS DEL USUARIO
$sql_citas = "SELECT * FROM citas WHERE idUser = $idUser ORDER BY fecha_cita DESC";
$resultado_citas = mysqli_query($conexion, $sql_citas);
$num_citas = mysqli_num_rows($resultado_citas);

// Fecha actual para comparaciones
$fecha_actual = date('Y-m-d H:i:s');
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
                <?php if (es_administrador()): ?>
                    <li><a href="../admin/usuarios-administracion.php">Usuarios</a></li>
                    <li><a href="../admin/citas-administracion.php">Citas</a></li>
                    <li><a href="../admin/noticias-administracion.php">Admin Noticias</a></li>
                <?php else: ?>
                    <li><a href="citaciones.php" class="activo">Mis Citas</a></li>
                <?php endif; ?>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>
    
    <main>
<div class="contenedor">
    <h1 class="texto-centro" style="margin-bottom: 2rem;">Gestión de Citas</h1>
    
    <?php 
    if (!empty($mensaje)) {
        echo mostrar_alerta($tipo_mensaje, $mensaje);
    }
    ?>
    
    <!-- Formulario: Crear o Editar Cita -->
    <div class="formulario">
        <h2><?php echo $editando ? 'Editar Cita' : 'Nueva Cita'; ?></h2>
        
        <form method="POST" action="">
            <?php if ($editando): ?>
                <input type="hidden" name="idCita" value="<?php echo $cita_editar['idCita']; ?>">
            <?php endif; ?>
            
            <div class="campo">
                <label for="fecha_cita">Fecha de la Cita *</label>
                <input type="date" id="fecha_cita" name="fecha_cita" required
                       value="<?php echo $editando ? date('Y-m-d', strtotime($cita_editar['fecha_cita'])) : ''; ?>"
                       min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="campo">
                <label for="hora_cita">Hora de la Cita *</label>
                <input type="time" id="hora_cita" name="hora_cita" required
                       value="<?php echo $editando ? date('H:i', strtotime($cita_editar['fecha_cita'])) : ''; ?>">
            </div>
            
            <div class="campo">
                <label for="motivo_cita">Motivo de la Cita *</label>
                <textarea id="motivo_cita" name="motivo_cita" rows="4" required><?php echo $editando ? htmlspecialchars($cita_editar['motivo_cita']) : ''; ?></textarea>
            </div>
            
            <?php if ($editando): ?>
                <button type="submit" name="actualizar_cita" class="boton boton-principal boton-completo">
                    Actualizar Cita
                </button>
                <a href="citaciones.php" class="boton boton-secundario boton-completo" style="margin-top: 0.5rem;">
                    Cancelar
                </a>
            <?php else: ?>
                <button type="submit" name="crear_cita" class="boton boton-principal boton-completo">
            <!--<div class="campo">
                <label>Hora</label>
                <input type="time" name="hora">
            </div>-->
                    Crear Cita
                </button>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Lista de Citas -->
    <div class="tabla-contenedor" style="margin-top: 2rem;">
        <h2>Mis Citas</h2>
        
        <?php if ($num_citas > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cita = mysqli_fetch_assoc($resultado_citas)): ?>
                        <?php
                        $es_futura = ($cita['fecha_cita'] > $fecha_actual);
                        $clase_fila = $es_futura ? '' : 'style="opacity: 0.6;"';
                        ?>
                        <tr <?php echo $clase_fila; ?>>
                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($cita['fecha_cita'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($cita['motivo_cita']); ?></td>
                            <td>
                                <?php if ($es_futura): ?>
                                    <span style="color: #28a745; font-weight: bold;">Próxima</span>
                                <?php else: ?>
                                    <span style="color: #6c757d;">Pasada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($es_futura): ?>
                                    <a href="citaciones.php?editar=<?php echo $cita['idCita']; ?>" 
                                       class="boton boton-advertencia" style="font-size: 0.9rem;">
                                        Editar
                                    </a>
                                    <a href="citaciones.php?eliminar=<?php echo $cita['idCita']; ?>" 
                                       class="boton boton-peligro" style="font-size: 0.9rem;"
                                       onclick="return confirm('¿Estás seguro de eliminar esta cita?')">
                                        Eliminar
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alerta alerta-info" style="margin-top: 1rem;">
                <p>📭 No tienes citas programadas. ¡Crea tu primera cita usando el formulario de arriba!</p>
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