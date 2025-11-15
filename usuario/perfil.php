<?php
$titulo = "Mi Perfil";
require_once '../config/database.php';

// Verificar que el usuario esté logueado
if (!usuario_logueado()) {
    header("Location: ../login.php");
    exit();
}

$intentos = 0; // contador de intentos
// Variables para mensajes
$mensaje = "";
$tipo_mensaje = "";

// Obtener los datos actuales del usuario
$idUser = $_SESSION['idUser'];

$sql = "SELECT ud.*, ul.usuario 
        FROM users_data ud
        INNER JOIN users_login ul ON ud.idUser = ul.idUser
        WHERE ud.idUser = $idUser";

$resultado = mysqli_query($conexion, $sql);
$usuario = mysqli_fetch_assoc($resultado);

// Procesar formulario de actualización de datos
if (isset($_POST['actualizar_datos'])) {
    
    $nombre = limpiar_entrada($_POST['nombre']);
    $apellidos = limpiar_entrada($_POST['apellidos']);
    $email = limpiar_entrada($_POST['email']);
    $telefono = limpiar_entrada($_POST['telefono']);
    $fecha_nacimiento = limpiar_entrada($_POST['fecha_nacimiento']);
    $direccion = limpiar_entrada($_POST['direccion']);
    $sexo = limpiar_entrada($_POST['sexo']);
    
    // Validar que los campos obligatorios no estén vacíos
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || empty($fecha_nacimiento) || empty($sexo)) {
        $mensaje = "Por favor, completa todos los campos obligatorios.";
        $tipo_mensaje = "error";
    } else {
        
        // Verificar si el email ya está en uso por otro usuario
        $sql_email = "SELECT idUser FROM users_data WHERE email = '$email' AND idUser != $idUser";
        $resultado_email = mysqli_query($conexion, $sql_email);
        
        if (mysqli_num_rows($resultado_email) > 0) {
            $mensaje = "Este correo electrónico ya está en uso por otro usuario.";
            $tipo_mensaje = "error";
        } else {
            
            // Actualizar los datos
            $sql_update = "UPDATE users_data SET 
                          nombre = '$nombre',
                          apellidos = '$apellidos',
                          email = '$email',
                          telefono = '$telefono',
                          fecha_nacimiento = '$fecha_nacimiento',
                          direccion = '$direccion',
                          sexo = '$sexo'
                          WHERE idUser = $idUser";
            
            if (mysqli_query($conexion, $sql_update)) {
                // Actualizar la sesión con el nuevo nombre
                $_SESSION['nombre'] = $nombre;
                $_SESSION['apellidos'] = $apellidos;
                
                $mensaje = "¡Datos actualizados correctamente!";
                $tipo_mensaje = "exito";
                
                // Recargar los datos actualizados
                $resultado = mysqli_query($conexion, $sql);
                $usuario = mysqli_fetch_assoc($resultado);
            } else {
                $mensaje = "Error al actualizar los datos: " . mysqli_error($conexion);
                $tipo_mensaje = "error";
            }
        }
    }
}

// Procesar formulario de cambio de contraseña
if (isset($_POST['cambiar_password'])) {
    
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        $mensaje = "Por favor, completa todos los campos de contraseña.";
        $tipo_mensaje = "error";
    } else if ($password_nueva != $password_confirmar) {
        $mensaje = "Las contraseñas nuevas no coinciden.";
        $tipo_mensaje = "error";
    } else if (strlen($password_nueva) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
        $tipo_mensaje = "error";
    } else {
        
        // Verificar la contraseña actual
        $sql_pass = "SELECT contraseña FROM users_login WHERE idUser = $idUser";
        $resultado_pass = mysqli_query($conexion, $sql_pass);
        $fila_pass = mysqli_fetch_assoc($resultado_pass);
        
        if (password_verify($password_actual, $fila_pass['contraseña'])) {
            
            // Contraseña actual correcta, actualizar
            $password_encriptada = password_hash($password_nueva, PASSWORD_DEFAULT);
            
            $sql_update_pass = "UPDATE users_login SET contraseña = '$password_encriptada' WHERE idUser = $idUser";
            
            if (mysqli_query($conexion, $sql_update_pass)) {
                $mensaje = "¡Contraseña actualizada correctamente!";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Error al actualizar la contraseña: " . mysqli_error($conexion);
                $tipo_mensaje = "error";
            }
            
        } else {
            $mensaje = "La contraseña actual es incorrecta.";
            $tipo_mensaje = "error";
        }
    }
}

// Incluir header (ajustando la ruta)
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
                    <li><a href="citaciones.php">Mis Citas</a></li>
                <?php endif; ?>
                <li><a href="perfil.php" class="activo">Perfil</a></li>
                <li><a href="../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>
    
    <main>
<div class="contenedor">
    <h1 class="texto-centro" style="margin-bottom: 2rem;">Mi Perfil</h1>
    
    <?php 
    if (!empty($mensaje)) {
        echo mostrar_alerta($tipo_mensaje, $mensaje);
    }
    ?>
    
    <!-- Sección: Datos Personales -->
    <div class="formulario" style="max-width: 800px;">
        <h2>Datos Personales</h2>
        
        <form method="POST" action="">
            <div class="campo">
                <label for="usuario"><strong>Nombre de Usuario:</strong></label>
                <input type="text" id="usuario" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" disabled>
                <small style="color: #666;">El nombre de usuario no se puede cambiar</small>
            </div>
            
            <div class="campo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
            </div>
            
            <div class="campo">
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" required
                       value="<?php echo htmlspecialchars($usuario['apellidos']); ?>">
            </div>
            
            <div class="campo">
                <label for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($usuario['email']); ?>">
            </div>
            
            <div class="campo">
                <label for="telefono">Teléfono *</label>
                <input type="tel" id="telefono" name="telefono" required
                       value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
            </div>
            
            <div class="campo">
                <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                       value="<?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?>">
            </div>
            
            <div class="campo">
                <label for="direccion">Dirección</label>
                <textarea id="direccion" name="direccion" rows="3"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
            </div>
            
            <div class="campo">
                <label for="sexo">Género *</label>
                <select id="sexo" name="sexo" required>
                    <option value="Masculino" <?php echo ($usuario['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="Femenino" <?php echo ($usuario['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                    <option value="Otro" <?php echo ($usuario['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    <option value="Prefiero no decir" <?php echo ($usuario['sexo'] == 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no decir</option>
                </select>
            </div>
            
            <button type="submit" name="actualizar_datos" class="boton boton-principal boton-completo">
                Actualizar Datos
            </button>
        </form>
    </div>
    
    <!-- Sección: Cambiar Contraseña -->
    <div class="formulario" style="max-width: 800px; margin-top: 2rem;">
        <h2>Cambiar Contraseña</h2>
        
        <form method="POST" action="">
            <div class="campo">
                <label for="password_actual">Contraseña Actual *</label>
                <input type="password" id="password_actual" name="password_actual" required>
            </div>
            
            <div class="campo">
                <label for="password_nueva">Contraseña Nueva *</label>
                <input type="password" id="password_nueva" name="password_nueva" required>
                <small style="color: #666;">Mínimo 6 caracteres</small>
            </div>
            
            <div class="campo">
                <label for="password_confirmar">Confirmar Contraseña Nueva *</label>
                <input type="password" id="password_confirmar" name="password_confirmar" required>
            </div>
            
            <button type="submit" name="cambiar_password" class="boton boton-exito boton-completo">
                Cambiar Contraseña
            </button>
        </form>
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



