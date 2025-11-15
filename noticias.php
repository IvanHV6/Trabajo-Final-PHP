<?php
$titulo = "Noticias";
require_once 'includes/header.php';

// Consultar todas las noticias ordenadas por fecha (más recientes primero)
$sql = "SELECT n.*, ud.nombre, ud.apellidos 
        FROM noticias n
        INNER JOIN users_data ud ON n.idUser = ud.idUser
        ORDER BY n.fecha DESC";

$resultado = mysqli_query($conexion, $sql);
$num_noticias = mysqli_num_rows($resultado);
?>

<div class="contenedor">

    <div class="banner">
        <h1>Noticias y Actualizaciones</h1>
        <p>Mantente informado con las últimas novedades</p>
    </div>

    <?php if ($num_noticias > 0): ?>
        
        <div class="noticias-grid">
            <?php while ($noticia = mysqli_fetch_assoc($resultado)): ?>
                
                <article class="noticia-card">
                    <?php if (!empty($noticia['imagen'])): ?>
                        <div class="noticia-imagen">
                            <?php if (file_exists('images/' . $noticia['imagen'])): ?>
                                <img src="images/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <?php else: ?>
                                <div class="imagen-placeholder">
                                    <span style="font-size: 3rem;">📰</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="noticia-contenido">
                        <h2><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                        
                        <div class="noticia-meta">
                            <span><?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?></span>
                            <span><?php echo htmlspecialchars($noticia['nombre'] . ' ' . $noticia['apellidos']); ?></span>
                        </div>
                        
                        <div class="noticia-texto">
                            <?php 
                            // Mostrar el texto completo o un extracto
                            $texto = $noticia['texto'];
                            if (strlen($texto) > 300) {
                                echo nl2br(htmlspecialchars(substr($texto, 0, 300))) . '...';
                            } else {
                                echo nl2br(htmlspecialchars($texto));
                            }
                            ?>
                        </div>
                    </div>
                </article>
                
            <?php endwhile; ?>
        </div>
        
    <?php else: ?>
        
        <div class="alerta alerta-info" style="margin-top: 2rem;">
            <h3>📭 No hay noticias disponibles</h3>
            <p>Aún no se han publicado noticias. ¡Vuelve pronto para ver las novedades!</p>
        </div>
        
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>