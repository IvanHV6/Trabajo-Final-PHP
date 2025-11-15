<?php
$titulo = "Inicio";
require_once 'includes/header.php';
?>
<div class="contenedor">
    <!-- Banner principal -->
    <div class="banner">
        <h1>Bienvenido al Proyecto Final</h1>
        <p>Sistema de gestion de citas y noticias</p>
        <!-- TODO: mejorar el diseño del banner -->
        
        <?php if (usuario_logueado()): ?>
            <p style="margin-top: 1rem; font-size: 1.2rem;">
                Hola, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
            </p>
        <?php else: ?>
            <p style="margin-top: 1rem;">
                <a href="registro.php" class="boton boton-principal">Registrarse</a>
                <a href="login.php" class="boton boton-exito" style="margin-left: 1rem;">Iniciar Sesion</a>
            </p>
        <?php endif; ?>
    </div>

    <!-- tarjetas de servicios -->
    <h2 class="texto-centro margen-superior">Nuestros Servicios</h2>
    
    <div class="tarjetas">
        <div class="tarjeta">
            <h3>Noticias</h3>
            <p>Mantente informado con las ultimas novedades y actualizaciones de nuestra plataforma.</p>
            <a href="noticias.php" class="boton boton-principal" style="margin-top: 1rem;">Ver Noticias</a>
        </div>

        <div class="tarjeta">
            <h3>Gestion de Citas</h3>
            <p>Programa y gestiona tus citas de forma rapida y sencilla.</p>
            <?php if (usuario_logueado()): ?>
                <a href="usuario/citaciones.php" class="boton boton-principal" style="margin-top: 1rem;">Mis Citas</a>
            <?php else: ?>
                <a href="login.php" class="boton boton-principal" style="margin-top: 1rem;">Iniciar Sesion</a>
            <?php endif; ?>
        </div>

        <div class="tarjeta">
            <h3>Perfil Personal</h3>
            <p>Accede y actualiza tu informacion personal cuando lo necesites.</p>
            <?php if (usuario_logueado()): ?>
                <a href="usuario/perfil.php" class="boton boton-principal" style="margin-top: 1rem;">Mi Perfil</a>
            <?php else: ?>
                <a href="registro.php" class="boton boton-principal" style="margin-top: 1rem;">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- seccion adicional -->
    <div class="tarjeta margen-superior">
        <h2>¿Por que elegirnos?</h2>
        <p>Somos una plataforma comprometida con ofrecerte la mejor experiencia.</p>
        
        <div class="tarjetas" style="margin-top: 2rem;">
            <div>
                <h4 style="color: #4169E1;">Facil de Usar</h4>
                <p>Interfaz intuitiva y amigable para todos los usuarios.</p>
            </div>
            <div>
                <h4 style="color: #4169E1;">Seguro</h4>
                <p>Tus datos protegidos con altos estandares de seguridad.</p>
            </div>
            <div>
                <h4 style="color: #4169E1;">Soporte</h4>
                <p>Estamos aqui para ayudarte cuando lo necesites.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>