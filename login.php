<?php
$titulo = "Iniciar Sesion";
require_once 'includes/header.php';

// si ya esta logueado redirigir
// Login del sistema
if (usuario_logueado()) {
    ir_a('index.php');
}

// variable para mensajes
$mensaje = "";
$tipo_mensaje = "";

// procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario = limpiar_entrada($_POST['usuario']);
    $password = $_POST['password'];
    
    // validar campos
    if(empty($usuario) || empty($password)) {
        $mensaje = "Por favor completa todos los campos";
        $tipo_mensaje = "error";
    } else {
        
        // buscar usuario en la BD
        $sql = "SELECT ul.*, ud.nombre, ud.apellidos 
                FROM users_login ul 
                INNER JOIN users_data ud ON ul.idUser = ud.idUser 
                WHERE ul.usuario = '$usuario'";
    // verificar datos del usuario
        
        $resultado = mysqli_query($conexion, $sql);
        
        if (mysqli_num_rows($resultado) == 1) {
            
            $fila = mysqli_fetch_assoc($resultado);
            
            // verificar password
            if (password_verify($password, $fila['password'])) {
                
                // crear sesion
                $_SESSION['idUser'] = $fila['idUser'];
                $_SESSION['usuario'] = $fila['usuario'];
                $_SESSION['nombre'] = $fila['nombre'];
                $_SESSION['apellidos'] = $fila['apellidos'];
                $_SESSION['rol'] = $fila['rol'];
                
                // redirigir
                ir_a('index.php');
                
            } else {
                $mensaje = "Contraseña incorrecta";
                $tipo_mensaje = "error";
            }
            
        } else {
            $mensaje = "El usuario no existe";
            $tipo_mensaje = "error";
        }
    }
}
?>

<div class="contenedor">
    <div class="formulario">
        <h2>Iniciar Sesion</h2>
        
        <?php 
        if (!empty($mensaje)) {
            echo mostrar_alerta($tipo_mensaje, $mensaje);
        }
        ?>
        
        <p class="texto-centro">
            ¿No tienes cuenta? <a href="registro.php" style="color: #4169E1;">Registrate aqui</a>
        </p>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            
            <div class="campo">
                <label for="usuario">Nombre de Usuario</label>
                <input type="text" id="usuario" name="usuario" required 
                       value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                       placeholder="Ingresa tu usuario">
            </div>
            
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required
                       placeholder="Ingresa tu contraseña">
            </div>
            
            <button type="submit" class="boton boton-principal boton-completo">Iniciar Sesion</button>
        </form>
        
        <div class="info-usuarios-prueba">
            <p class="info-titulo"><strong>Usuarios de prueba:</strong></p>
            <p class="info-linea">Admin: admin / admin123</p>
            <p class="info-linea">Usuario: Pedro / 123456</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
