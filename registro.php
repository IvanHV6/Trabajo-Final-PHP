<?php
$titulo = "Registro";
require_once 'includes/header.php';

$mensaje = "";
// Pagina de registro de usuarios
$tipo_mensaje = "";

// procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = limpiar_entrada($_POST['nombre']);
    $apellidos = limpiar_entrada($_POST['apellidos']);
    $email = limpiar_entrada($_POST['email']);
    $telefono = limpiar_entrada($_POST['telefono']);
    $fecha_nacimiento = limpiar_entrada($_POST['fecha_nacimiento']);
    $direccion = limpiar_entrada($_POST['direccion']);
    $sexo = limpiar_entrada($_POST['sexo']);
    $usuario = limpiar_entrada($_POST['usuario']);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    // validar campos
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || 
        empty($fecha_nacimiento) || empty($sexo) || empty($usuario) || empty($password)) {
        
        $mensaje = "Por favor completa todos los campos obligatorios";
        $tipo_mensaje = "error";
        
    }else if ($password != $confirmar_password) {
        
        $mensaje = "Las contraseñas no coinciden";
        $tipo_mensaje = "error";
        
    }else if (strlen($password) < 6) {
        
        $mensaje = "La contraseña debe tener al menos 6 caracteres";
        $tipo_mensaje = "error";
        
    } else {
        
        // verificar email
        $sql_email = "SELECT idUser FROM users_data WHERE email = '$email'";
        $resultado_email = mysqli_query($conexion, $sql_email);
        
        if (mysqli_num_rows($resultado_email) > 0) {
            $mensaje = "Este email ya esta registrado";
            $tipo_mensaje = "error";
        } else {
            
            // verificar usuario
        // FIXME: verificar que el email sea valido antes
            $sql_usuario = "SELECT idLogin FROM users_login WHERE usuario = '$usuario'";
            $resultado_usuario = mysqli_query($conexion, $sql_usuario);
            
            if (mysqli_num_rows($resultado_usuario) > 0) {
                $mensaje = "Este usuario ya esta en uso";
                $tipo_mensaje = "error";
            } else {
                
                // encriptar password
                $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
                
                // insertar en users_data
                $sql_data = "INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) 
                             VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$fecha_nacimiento', '$direccion', '$sexo')";
                
                if (mysqli_query($conexion, $sql_data)) {
                    
                    $idUser = mysqli_insert_id($conexion);
                    
                    // insertar en users_login
                    $sql_login = "INSERT INTO users_login (idUser, usuario, password, rol) 
                                  VALUES ($idUser, '$usuario', '$password_encriptada', 'user')";
                    
                    if (mysqli_query($conexion, $sql_login)) {
                        $mensaje = "Registro exitoso! Redirigiendo...";
                        $tipo_mensaje = "exito";
                        header("refresh:2;url=login.php");
                    } else {
                        $mensaje = "Error al crear usuario: " . mysqli_error($conexion);
                        $tipo_mensaje = "error";
                    }
                    
                } else {
                    $mensaje = "Error al registrar: " . mysqli_error($conexion);
                    $tipo_mensaje = "error";
                }
            }
        }
    }
}
?>

<div class="contenedor">
    <div class="formulario">
        <h2>Crear Cuenta Nueva</h2>
        
        <?php 
        if (!empty($mensaje)) {
            echo mostrar_alerta($tipo_mensaje, $mensaje);
        }
        ?>
        
        <p class="texto-centro">
            ¿Ya tienes cuenta? <a href="login.php" style="color: #4169E1;">Inicia sesion aqui</a>
        </p>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validarRegistro()">
            
            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Datos Personales</h3>
            
            <div class="campo">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="campo">
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            
            <div class="campo">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="campo">
                <label for="telefono">Telefono *</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            
            <div class="campo">
                <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            
            <div class="campo">
                <label for="direccion">Direccion</label>
                <textarea id="direccion" name="direccion" rows="3"></textarea>
            </div>
            
            <div class="campo">
                <label for="sexo">Genero *</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Selecciona...</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                    <option value="Prefiero no decir">Prefiero no decir</option>
                </select>
            </div>
            
            <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Datos de Acceso</h3>
            
            <div class="campo">
                <label for="usuario">Nombre de Usuario *</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            
            <div class="campo">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" required>
                <small>Minimo 6 caracteres</small>
            </div>
            
            <div class="campo">
                <label for="confirmar_password">Confirmar Contraseña *</label>
                <input type="password" id="confirmar_password" name="confirmar_password" required>
            </div>
            
            <button type="submit" class="boton boton-principal boton-completo">Registrarse</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

