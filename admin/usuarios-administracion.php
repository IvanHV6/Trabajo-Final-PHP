<?php
$titulo = "Administración de Usuarios";
require_once '../config/database.php';

// Verificar que sea administrador
if (!usuario_logueado() || !es_administrador()) {
    header("Location: ../index.php");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";
$editando = false;
$usuario_editar = null;

// CREAR NUEVO USUARIO
if (isset($_POST['crear_usuario'])) {
    
    $nombre = limpiar_entrada($_POST['nombre']);
    $apellidos = limpiar_entrada($_POST['apellidos']);
    $email = limpiar_entrada($_POST['email']);
    $telefono = limpiar_entrada($_POST['telefono']);
    $fecha_nacimiento = limpiar_entrada($_POST['fecha_nacimiento']);
    $direccion = limpiar_entrada($_POST['direccion']);
    $sexo = limpiar_entrada($_POST['sexo']);
    $usuario = limpiar_entrada($_POST['usuario']);
    $password = $_POST['password'];
    $rol = limpiar_entrada($_POST['rol']);
    
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || 
        empty($fecha_nacimiento) || empty($sexo) || empty($usuario) || empty($password) || empty($rol)) {
        
        $mensaje = "Por favor, completa todos los campos obligatorios.";
        $tipo_mensaje = "error";
    }else{
        
        // Verificar que el email no exista
        $sql_email = "SELECT idUser FROM users_data WHERE email = '$email'";
        if (mysqli_num_rows(mysqli_query($conexion, $sql_email)) > 0) {
            $mensaje = "El email ya está registrado.";
            $tipo_mensaje = "error";
        }else{
            
            // Verificar que el usuario no exista
            $sql_usuario = "SELECT idLogin FROM users_login WHERE usuario = '$usuario'";
            if (mysqli_num_rows(mysqli_query($conexion, $sql_usuario)) > 0) {
                $mensaje = "El nombre de usuario ya está en uso.";
                $tipo_mensaje = "error";
            }else{
                
                $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar en users_data
                $sql_data = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) 
                             VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$sexo')";
                
                if (mysqli_query($conexion, $sql_data)) {
                    $idUser = mysqli_insert_id($conexion);
                    
                    // Insertar en users_login
                    $sql_login = "INSERT INTO users_login (idUser, usuario, password, rol) 
                                  VALUES ($idUser, '$usuario', '$password_encriptada', '$rol')";
                    
                    if (mysqli_query($conexion, $sql_login)) {
                        $mensaje = "¡Usuario creado correctamente!";
                        $tipo_mensaje = "exito";
                    }else{
                        $mensaje = "Error al crear el login: " . mysqli_error($conexion);
                        $tipo_mensaje = "error";
                    }
                }else{
                    $mensaje = "Error al crear el usuario: " . mysqli_error($conexion);
                    $tipo_mensaje = "error";
                }
            }
        }
    }
}

// ACTUALIZAR USUARIO
if (isset($_POST['actualizar_usuario'])) {
    
    $idUser = limpiar_entrada($_POST['idUser']);
    $nombre = limpiar_entrada($_POST['nombre']);
    $apellidos = limpiar_entrada($_POST['apellidos']);
    $email = limpiar_entrada($_POST['email']);
    $telefono = limpiar_entrada($_POST['telefono']);
    $fecha_nacimiento = limpiar_entrada($_POST['fecha_nacimiento']);
    $direccion = limpiar_entrada($_POST['direccion']);
    $sexo = limpiar_entrada($_POST['sexo']);
    $rol = limpiar_entrada($_POST['rol']);
    
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || 
        empty($fecha_nacimiento) || empty($sexo) || empty($rol)) {
        
        $mensaje = "Por favor, completa todos los campos obligatorios.";
        $tipo_mensaje = "error";
    }else{
        
        // Verificar que el email no esté en uso por otro usuario
        $sql_email = "SELECT idUser FROM users_data WHERE email = '$email' AND idUser != $idUser";
        if (mysqli_num_rows(mysqli_query($conexion, $sql_email)) > 0) {
            $mensaje = "El email ya está en uso por otro usuario.";
            $tipo_mensaje = "error";
        }else{
            
            // Actualizar users_data
            $sql_data = "UPDATE users_data SET 
                        nombre = '$nombre',
                        apellidos = '$apellidos',
                        email = '$email',
                        telefono = '$telefono',
                        fecha_nacimiento = '$fecha_nacimiento',
                        direccion = '$direccion',
                        sexo = '$sexo'
                        WHERE idUser = $idUser";
            
            // Actualizar rol en users_login
            $sql_login = "UPDATE users_login SET rol = '$rol' WHERE idUser = $idUser";
            
            if (mysqli_query($conexion, $sql_data) && mysqli_query($conexion, $sql_login)) {
                $mensaje = "¡Usuario actualizado correctamente!";
                $tipo_mensaje = "exito";
                header("refresh:1;url=usuarios-administracion.php");
            }else{
                $mensaje = "Error al actualizar: " . mysqli_error($conexion);
                $tipo_mensaje = "error";
            }
        }
    }
}

// ELIMINAR USUARIO
if (isset($_GET['eliminar'])) {
    
    $idUser = limpiar_entrada($_GET['eliminar']);
    
    // No permitir eliminar al propio admin
    if ($idUser == $_SESSION['idUser']) {
        $mensaje = "No puedes eliminarte a ti mismo.";
        $tipo_mensaje = "error";
    }else{
        
        $sql_delete = "DELETE FROM users_data WHERE idUser = $idUser";
        
        if (mysqli_query($conexion, $sql_delete)) {
            $mensaje = "Usuario eliminado correctamente.";
            $tipo_mensaje = "exito";
            header("refresh:1;url=usuarios-administracion.php");
        }else{
            $mensaje = "Error al eliminar: " . mysqli_error($conexion);
            $tipo_mensaje = "error";
        }
    }
}

// CARGAR USUARIO PARA EDITAR
if (isset($_GET['editar'])) {
    $idUser = limpiar_entrada($_GET['editar']);
    
    $sql_editar = "SELECT ud.*, ul.rol 
                   FROM users_data ud
                   INNER JOIN users_login ul ON ud.idUser = ul.idUser
                   WHERE ud.idUser = $idUser";
    $resultado_editar = mysqli_query($conexion, $sql_editar);
    
    if (mysqli_num_rows($resultado_editar) > 0) {
        $usuario_editar = mysqli_fetch_assoc($resultado_editar);
        $editando = true;
    }
}

// OBTENER TODOS LOS USUARIOS
$sql_usuarios = "SELECT ud.*, ul.usuario, ul.rol 
                 FROM users_data ud
                 INNER JOIN users_login ul ON ud.idUser = ul.idUser
                 ORDER BY ud.idUser DESC";
$resultado_usuarios = mysqli_query($conexion, $sql_usuarios);
$num_usuarios = mysqli_num_rows($resultado_usuarios);
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
                <li><a href="usuarios-administracion.php" class="activo">Usuarios</a></li>
                <li><a href="citas-administracion.php">Citas</a></li>
                <li><a href="noticias-administracion.php">Admin Noticias</a></li>
                <li><a href="../usuario/perfil.php">Perfil</a></li>
                <li><a href="../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>
    
    <main>
<div class="contenedor">
    <h1 class="texto-centro" style="margin-bottom: 2rem;">Administración de Usuarios</h1>
    
    <?php 
    if (!empty($mensaje)) {
        echo mostrar_alerta($tipo_mensaje, $mensaje);
    }
    ?>
    
    <!-- Formulario: Crear o Editar Usuario -->
    <div class="formulario">
        <h2><?php echo $editando ? 'Editar Usuario' : 'Nuevo Usuario'; ?></h2>
        
        <form method="POST" action="">
            <?php if ($editando): ?>
                <input type="hidden" name="idUser" value="<?php echo $usuario_editar['idUser']; ?>">
            <?php endif; ?>
            
            <div class="campo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required
                       value="<?php echo $editando ? htmlspecialchars($usuario_editar['nombre']) : ''; ?>">
            </div>
            
            <div class="campo">
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" required
                       value="<?php echo $editando ? htmlspecialchars($usuario_editar['apellidos']) : ''; ?>">
            </div>
            
            <div class="campo">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo $editando ? htmlspecialchars($usuario_editar['email']) : ''; ?>">
            </div>
            
            <div class="campo">
                <label for="telefono">Teléfono *</label>
                <input type="tel" id="telefono" name="telefono" required
                       value="<?php echo $editando ? htmlspecialchars($usuario_editar['telefono']) : ''; ?>">
            </div>
            
            <div class="campo">
                <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                       value="<?php echo $editando ? $usuario_editar['fecha_nacimiento'] : ''; ?>">
            </div>
            
            <div class="campo">
                <label for="direccion">Dirección</label>
                <textarea id="direccion" name="direccion" rows="3"><?php echo $editando ? htmlspecialchars($usuario_editar['direccion']) : ''; ?></textarea>
            </div>
            
            <div class="campo">
                <label for="sexo">Género *</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Selecciona...</option>
                    <option value="Masculino" <?php echo ($editando && $usuario_editar['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="Femenino" <?php echo ($editando && $usuario_editar['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                    <option value="Otro" <?php echo ($editando && $usuario_editar['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    <option value="Prefiero no decir" <?php echo ($editando && $usuario_editar['sexo'] == 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no decir</option>
                </select>
            </div>
            
            <?php if (!$editando): ?>
            <div class="campo">
                <label for="usuario">Nombre de Usuario *</label>
                <input type="text" id="usuario" name="usuario" required>
                <small style="color: #666;">No se puede cambiar después</small>
            </div>
            
            <div class="campo">
                <label for="password">password *</label>
                <input type="password" id="password" name="password" required>
                <small style="color: #666;">Mínimo 6 caracteres</small>
            </div>
            <?php endif; ?>
            
            <div class="campo">
                <label for="rol">Rol *</label>
                <select id="rol" name="rol" required>
                    <option value="user" <?php echo ($editando && $usuario_editar['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario Normal</option>
                    <option value="admin" <?php echo ($editando && $usuario_editar['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>
            
            <?php if ($editando): ?>
                <button type="submit" name="actualizar_usuario" class="boton boton-principal boton-completo">
                    Actualizar Usuario
                </button>
                <a href="usuarios-administracion.php" class="boton boton-secundario boton-completo" style="margin-top: 0.5rem;">
                    Cancelar
                </a>
            <?php else: ?>
                <button type="submit" name="crear_usuario" class="boton boton-principal boton-completo">
                    Crear Usuario
                </button>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Lista de Usuarios -->
    <div class="tabla-contenedor" style="margin-top: 2rem;">
        <h2>Todos los Usuarios (<?php echo $num_usuarios; ?>)</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($resultado_usuarios)): ?>
                    <tr>
                        <td><?php echo $user['idUser']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']); ?></strong>
                            
                            <small style="color: #666;">Tel: <?php echo $user['telefono']; ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                        <td>
                            <?php if ($user['rol'] == 'admin'): ?>
                                <span style="color: #dc3545; font-weight: bold;">Admin</span>
                            <?php else: ?>
                                <span style="color: #28a745;">Usuario</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="usuarios-administracion.php?editar=<?php echo $user['idUser']; ?>" 
                               class="boton boton-advertencia" style="font-size: 0.9rem;">
                                Editar
                            </a>
                            <?php if ($user['idUser'] != $_SESSION['idUser']): ?>
                            <a href="usuarios-administracion.php?eliminar=<?php echo $user['idUser']; ?>" 
                               class="boton boton-peligro" style="font-size: 0.9rem;"
                               onclick="return confirm('¿Eliminar a <?php echo htmlspecialchars($user['nombre']); ?>?')">
                                Eliminar
                            </a>
                            <?php else: ?>
                            <span style="color: #999; font-size: 0.9rem;">Tú mismo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
